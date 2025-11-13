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
            ->take(5)
            ->get();

        return view('monitor.show', compact('office', 'cashiers', 'servingVisitors', 'upcomingQueues'));
    }

    public function monitorData($officeId)
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

        return response()->json([
            'cashiers' => $cashiers->map(function ($c) use ($servingVisitors) {
                return [
                    'id'      => $c->id,
                    'name'    => $c->name,
                    'serving' => $servingVisitors->has($c->id)
                        ? [
                            'id_number'    => $servingVisitors[$c->id]->id_number,
                            'queue_number' => $servingVisitors[$c->id]->queue_number,
                        ]
                        : null,
                ];
            }),
            'upcomingQueues' => $upcomingQueues->map(function ($v) {
                return [
                    'id_number'    => $v->id_number,
                    'queue_number' => $v->queue_number,
                ];
            }),
        ]);
    }
}
