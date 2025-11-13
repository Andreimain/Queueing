<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeQueueController extends Controller
{
    // Ensure staff can only access their office
    private function authorizeOffice($officeId)
    {
        $office = Office::findOrFail($officeId);
        $user = Auth::user();

        if ($user && method_exists($user, 'isStaff') && $user->isStaff() && $user->office_id !== $office->id) {
            abort(403, 'Unauthorized access to this office queue.');
        }

        return $office;
    }

    // Show queue: all serving visitors + waiting list
    public function index($officeId)
    {
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();

        // All visitors currently being served (by any cashier)
        $serving = Visitor::with('cashier')
            ->where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->get();

        // Waiting visitors in FIFO order
        $waiting = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->get();

        return view('queue.office', compact('office', 'serving', 'waiting'));
    }

    // Assign the next waiting visitor to the logged-in cashier
    public function next($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();
            $cashierId = Auth::id();

            // Check if this cashier is already serving someone
            $alreadyServing = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->where('cashier_id', $cashierId)
                ->first();

            if ($alreadyServing) {
                return [
                    'status' => 'error',
                    'message' => "You are already serving visitor #{$alreadyServing->queue_number}."
                ];
            }

            // Get the next visitor, priority first
            $next = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting')
                ->orderByDesc('priority')  // priority first
                ->orderBy('queue_number')  // then FIFO
                ->first();

            if (!$next) {
                return ['status' => 'empty', 'message' => 'No visitors left to serve.'];
            }

            // Serve the visitor
            $next->update([
                'status' => 'serving',
                'cashier_id' => $cashierId,
            ]);

            $next->load('cashier');

            return [
                'status' => 'success',
                'message' => "Visitor #{$next->queue_number} is now being served by {$next->cashier->name}."
            ];
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
            $cashierId = Auth::id();

            $serving = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->where('cashier_id', $cashierId)
                ->first();

            if (!$serving) {
                return ['status' => 'error', 'message' => 'You are not currently serving any visitor.'];
            }

            $wasPriority = (bool) $serving->priority;

            $serving->update(['status' => 'done']);

            // Update office priority counter
            if ($wasPriority) {
                $office->priority_counter = 0;
            } else {
                $office->priority_counter = (int) ($office->priority_counter ?? 0) + 1;
            }
            $office->save();

            return ['status' => 'success', 'message' => 'Visitor marked as done.'];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }


    // Skip a visitor (either selected or currently served by this cashier)
    public function markSkip(Request $request, $officeId)
    {
        $result = DB::transaction(function () use ($request, $officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();
            $cashierId = Auth::id();

            $selected = $request->input('selected_visitors', []);

            if (!empty($selected)) {
                $visitors = Visitor::whereIn('id', $selected)
                    ->where('office_id', $office->id)
                    ->whereDate('created_at', $today)
                    ->where('status', 'waiting')
                    ->lockForUpdate()
                    ->get();

                if ($visitors->isEmpty()) {
                    return ['status' => 'error', 'message' => 'No valid visitors found to skip.'];
                }

                foreach ($visitors as $v) {
                    $v->update(['status' => 'skipped']);
                }

                return ['status' => 'success', 'message' => "{$visitors->count()} visitor(s) skipped successfully."];
            }

            // Skip visitor currently served by this cashier
            $serving = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->where('cashier_id', $cashierId)
                ->lockForUpdate()
                ->first();

            if (!$serving) {
                return ['status' => 'error', 'message' => 'You are not currently serving any visitor.'];
            }

            $serving->update(['status' => 'skipped']);
            return ['status' => 'success', 'message' => 'Visitor skipped successfully.'];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    // Show all skipped visitors (filter by office if staff)
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

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('office', fn ($oq) => $oq->where('name', 'like', "%{$search}%"));
            });
        }

        $skipped = $query->orderBy('updated_at')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->view('queue.skiplist-body', compact('skipped'));
        }

        return view('queue.skiplist', compact('skipped'));
    }

    // Restore skipped visitors to waiting queue
    public function restoreSkipped(Request $request)
    {
        $ids = explode(',', $request->input('selected_ids'));
        $query = Visitor::whereIn('id', $ids)->where('status', 'skipped');

        $user = Auth::user();
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        $query->update(['status' => 'waiting']);
        return back()->with('success', 'Selected visitors restored to waiting queue.');
    }
}
