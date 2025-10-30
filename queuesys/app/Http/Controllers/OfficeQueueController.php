<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeQueueController extends Controller
{
    private function authorizeOffice($officeId)
    {
        $office = Office::findOrFail($officeId);

        $user = Auth::user();
        if ($user && method_exists($user, 'isStaff') && $user->isStaff() && $user->office_id !== $office->id) {
            abort(403, 'Unauthorized access to this office queue.');
        }

        return $office;
    }

    public function index($officeId)
    {
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();

        $serving = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        $waiting = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->get();

        return view('queue.office', compact('office', 'serving', 'waiting'));
    }

    public function next($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();

            // Finish the current serving (if any)
            $current = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->first();

            if ($current) {
                $wasPriority = (bool) $current->priority;
                $current->update(['status' => 'done']);

                if ($wasPriority) {
                    $office->priority_counter = 0; // reset after serving priority
                } else {
                    $office->priority_counter = (int) ($office->priority_counter ?? 0) + 1;
                }
                $office->save();
            }

            // Find waiting visitors
            $priority = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting')
                ->where('priority', true)
                ->orderBy('queue_number')
                ->first();

            $regular = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting')
                ->where(function ($q) {
                    $q->where('priority', false)->orWhereNull('priority');
                })
                ->orderBy('queue_number')
                ->first();

            if (! $priority && ! $regular) {
                return ['status' => 'empty', 'message' => 'No visitors left to serve.'];
            }

            $next = null;
            $counter = (int) ($office->priority_counter ?? 0);

            if ($priority) {
                if ($counter >= 2) {
                    // After 2 regulars, priority is allowed again
                    $next = $priority;
                } else {
                    // Need more regulars first, unless no regulars exist
                    $next = $regular ?? $priority;
                }
            } else {
                // No priority waiting, serve regular
                $next = $regular;
            }

            if ($next) {
                $next->update(['status' => 'serving']);
                return ['status' => 'success', 'message' => "Visitor #{$next->queue_number} is now being served."];
            }

            return ['status' => 'empty', 'message' => 'No visitors left to serve.'];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function markDone($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();

            $serving = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->first();

            if (! $serving) {
                return ['status' => 'error', 'message' => 'No visitor is currently being served.'];
            }

            $wasPriority = (bool) $serving->priority;
            $serving->update(['status' => 'done']);

            if ($wasPriority) {
                $office->priority_counter = 0;
            } else {
                $office->priority_counter = (int) ($office->priority_counter ?? 0) + 1;
            }
            $office->save();

            return ['status' => 'success', 'message' => 'Current visitor has been marked as done.'];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function markSkip($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();

            $serving = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->first();

            if (! $serving) {
                return ['status' => 'error', 'message' => 'No visitor is currently being served.'];
            }

            $wasPriority = (bool) $serving->priority;
            $serving->update(['status' => 'skipped']);

            if ($wasPriority) {
                $office->priority_counter = 0;
            } else {
                $office->priority_counter = (int) ($office->priority_counter ?? 0) + 1;
            }
            $office->save();

            return ['status' => 'success', 'message' => 'Current visitor has been marked as skipped.'];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function viewSkippedAll(Request $request)
    {
        $today = now()->toDateString();

        $query = Visitor::with('office')
            ->whereDate('created_at', $today)
            ->where('status', 'skipped');

        $user = Auth::user();
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('office', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $skipped = $query->orderBy('updated_at', 'asc')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->view('queue.skiplist-body', compact('skipped'));
        }

        return view('queue.skiplist', compact('skipped'));
    }

    public function restoreSkipped(Request $request)
    {
        $ids = explode(',', $request->input('selected_ids'));

        $query = Visitor::whereIn('id', $ids)
            ->where('status', 'skipped');

        $user = Auth::user();
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        $query->update(['status' => 'waiting']);

        return back()->with('success', 'Selected visitors have been restored to the waiting queue.');
    }
}
