<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;

class OfficeQueueController extends Controller
{
    public function index($office)
    {
        $office = ucwords(strtolower($office));
        $today = now()->toDateString();

        $serving = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        $waiting = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->get();

        return view('queue.office', compact('office', 'serving', 'waiting'));
    }

    public function next($office)
    {
        $office = ucwords(strtolower($office));
        $today = now()->toDateString();

        // Mark current serving visitor as done
        Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->update(['status' => 'done']);

        // Get next waiting visitor
        $next = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->first();

        if ($next) {
            $next->update(['status' => 'serving']);
        }

        return back()->with('success', 'Next visitor is now being served.');
    }

    public function markDone($office)
    {
        $office = ucwords(strtolower($office));
        $today = now()->toDateString();

        $serving = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        if ($serving) {
            $serving->update(['status' => 'done']);
            return back()->with('success', 'Current visitor has been marked as done.');
        }

        return back()->with('error', 'No visitor is currently being served.');
    }

    public function markSkip($office)
    {
        $office = ucwords(strtolower($office));
        $today = now()->toDateString();

        $serving = Visitor::where('office', $office)
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

        $query = Visitor::whereDate('created_at', $today)
            ->where('status', 'skipped');

        // If searching
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('office', 'like', "%{$search}%");
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

        Visitor::whereIn('id', $ids)
            ->where('status', 'skipped')
            ->update(['status' => 'waiting']);

        return back()->with('success', 'Selected visitors have been restored to the waiting queue.');
    }
}
