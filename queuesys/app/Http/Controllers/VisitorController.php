<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use App\Models\Visitor;
use App\Models\Office;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Show the visitor registration form.
     */
    public function create()
    {
        // Fetch all offices from database
        $offices = Office::all();

        return view('register', compact('offices'));
    }

    /**
     * Store the visitor registration form data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'contact_number' => 'required|string|max:15',
            'id_number'      => 'nullable|string|max:50',
            'office_id'      => 'required|exists:offices,id',
            'priority'       => 'nullable|boolean',
        ]);

        $office = Office::findOrFail($request->office_id);
        $today = now()->toDateString();

        $lastQueue = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $queueNumber = ($lastQueue ?? 0) + 1;

        $ticketNumber = $office->abbreviation . '-' . str_pad($queueNumber, 3, '0', STR_PAD_LEFT);

        $visitor = Visitor::create([
            'name'     => $request->name,
            'contact_number' => $request->contact_number,
            'id_number'      => $request->id_number,
            'office_id'      => $office->id,
            'previous_office_id' => null,
            'queue_number'   => $queueNumber,
            'ticket_number'  => $ticketNumber,
            'status'         => 'waiting',
            'priority' => (bool) $request->priority,
        ]);

        event(new QueueUpdated(
            $office->id,
            $this->buildMonitorPayload($office)
        ));
        return view('ticket', compact('visitor'));
    }

    protected function buildMonitorPayload(Office $office): array
    {
        $today = now()->toDateString();

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
