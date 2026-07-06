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
        $cashiers = $office->staff()->get();

        $servingVisitors = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->get()
            ->keyBy('cashier_id');

        $upcomingQueues = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number', 'asc')
            ->take(15)
            ->get();

        return view('monitor.show', compact('office', 'cashiers', 'servingVisitors', 'upcomingQueues'));
    }

    protected function buildMonitorPayload(Office $office): array
    {
        $today = Carbon::today();

        $cashiers = $office->staff()->get();

        $servingVisitors = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->get()
            ->keyBy('cashier_id');

        $upcomingQueues = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->take(15)
            ->get();

        return [
            'serving' => $cashiers->mapWithKeys(function ($cashier) use ($servingVisitors) {
                if (!$servingVisitors->has($cashier->id)) {
                    return [$cashier->id => null];
                }

                $v = $servingVisitors[$cashier->id];

                return [
                    $cashier->id => [
                        'ticket' => $v->ticket_number,
                        'queue'  => $v->queue_number,
                    ],
                ];
            })->toArray(),

            'upcoming' => $upcomingQueues
                ->pluck('ticket_number')
                ->toArray(),
        ];
    }
}
