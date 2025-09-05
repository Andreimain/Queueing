<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Visitor;
use App\Models\Office;
use Carbon\Carbon;

class Controller extends BaseController
{
    public function monitor($officeId)
    {
        $today  = Carbon::today();
        $office = Office::findOrFail($officeId);

        $currentQueue = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->orderBy('updated_at', 'desc')
            ->first();

        $upcomingQueues = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number', 'asc')
            ->take(5)
            ->get();

        return view('monitor.show', [
            'office'         => $office,
            'currentQueue'   => $currentQueue,
            'upcomingQueues' => $upcomingQueues,
        ]);
    }

    public function monitorData($office)
    {
        $office = \App\Models\Office::findOrFail($office);
        $today  = \Carbon\Carbon::today();

        $currentQueue = \App\Models\Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->orderBy('updated_at', 'desc')
            ->first();

        $upcomingQueues = \App\Models\Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number', 'asc')
            ->take(15)
            ->get();

        return response()->json([
            'currentQueue' => $currentQueue ? [
                'id_number' => $currentQueue->id_number,
            ] : null,
            'upcomingQueues' => $upcomingQueues->map(function ($v) {
                return [
                    'id_number' => $v->id_number,
                ];
            })->values(),
        ]);
    }

}
