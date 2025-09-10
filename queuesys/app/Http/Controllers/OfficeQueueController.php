<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;

class OfficeQueueController extends Controller
{
    /**
     * Ensure the user can only access their assigned office (if staff).
     *
     * Note: this returns the Office model (no explicit return type to avoid
     * editor/linter issues on some setups).
     *
     * @param  int|string  $officeId
     * @return \App\Models\Office
     */
    private function authorizeOffice($officeId)
    {
        $office = Office::findOrFail($officeId);

        $user = Auth::user();
        if ($user && $user->isStaff() && $user->office_id !== $office->id) {
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
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();

        // Mark current serving visitor as done
        Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->update(['status' => 'done']);

        // Get next waiting visitor
        $next = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->first();

        if ($next) {
            $next->update(['status' => 'serving']);
        }

        return back()->with('success', 'Next visitor is now being served.');
    }

    public function markDone($officeId)
    {
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();

        $serving = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        if ($serving) {
            $serving->update(['status' => 'done']);
            return back()->with('success', 'Current visitor has been marked as done.');
        }

        return back()->with('error', 'No visitor is currently being served.');
    }

    public function markSkip($officeId)
    {
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();

        $serving = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        if ($serving) {
            $serving->update(['status' => 'skipped']);
            return back()->with('success', 'Current visitor has been marked as skipped.');
        }

        return back()->with('error', 'No visitor is currently being served.');
    }

    public function viewSkippedAll(Request $request)
    {
        $today = now()->toDateString();

        $query = Visitor::with('office')
            ->whereDate('created_at', $today)
            ->where('status', 'skipped');

        // Staff restriction: only their office
        $user = Auth::user();
        if ($user && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        // If searching
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

        // Staff restriction: only their office
        $user = Auth::user();
        if ($user && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        $query->update(['status' => 'waiting']);

        return back()->with('success', 'Selected visitors have been restored to the waiting queue.');
    }
}
