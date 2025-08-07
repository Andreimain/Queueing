<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class OfficeQueueController extends Controller
{
    /**
     * Display the current queue for a specific office.
     */
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

    /**
     * Serve the next visitor in the queue.
     */
    public function next($office)
    {
        $office = ucwords(strtolower($office));
        $today = now()->toDateString();

        // Mark the current serving visitor as 'done'
        Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->update(['status' => 'done']);

        // Get and update the next waiting visitor
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

        // Mark the current serving visitor as 'done'
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
}
