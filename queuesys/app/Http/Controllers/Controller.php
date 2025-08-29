<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Visitor;
use Carbon\Carbon;

class Controller extends BaseController
{
    public function monitor($office)
    {
        $office = ucwords(strtolower($office));
        $today  = Carbon::today();

        $currentQueue = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->orderBy('updated_at', 'desc')
            ->first();

        $upcomingQueues = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number', 'asc')
            ->take(5)
            ->get();

        return view('monitor.show', compact('office', 'currentQueue', 'upcomingQueues'));
    }

    public function monitorData($office)
    {
        $office = ucwords(strtolower($office));
        $today  = Carbon::today();

        $currentQueue = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->orderBy('updated_at', 'desc')
            ->first();

        $upcomingQueues = Visitor::where('office', $office)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number', 'asc')
            ->take(15)
            ->get();

        return response()->json([
            'currentQueue' => $currentQueue ? [
                'queue_number' => $currentQueue->queue_number,
                'first_name'   => $currentQueue->first_name,
                'last_name'    => $currentQueue->last_name,
            ] : null,
            'upcomingQueues' => $upcomingQueues->map(function ($v) {
                return [
                    'queue_number' => $v->queue_number,
                    'first_name'   => $v->first_name,
                    'last_name'    => $v->last_name,
                ];
            })->values(),
        ]);
    }
}
