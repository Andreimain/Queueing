<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use App\Models\Visitor;
use App\Models\Office;
use App\Models\Course;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function create()
    {
        $offices = Office::all();
        $courses = Course::orderBy('name')->get();

        return view('register', compact('offices', 'courses'));
    }

    /**
     * Store the visitor registration form data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'contact_number' => 'required|string|max:15',
            'id_number' => 'nullable|string|max:50',
            'type' => 'required|in:student,visitor',
            'office_id' => 'required|exists:offices,id',
            'course_id' => 'nullable|exists:courses,id',
            'priority' => 'nullable|boolean',
        ]);

        $office = Office::findOrFail($request->office_id);

        $isRegistrar = $office->abbreviation === 'RO';

        if (
            $isRegistrar &&
            $request->type === 'student' &&
            !$request->filled('course_id')
        ) {
            return back()
                ->withErrors([
                    'course_id' => 'Please select your course.'
                ])
                ->withInput();
        }

        $today = now()->toDateString();

        $lastQueue = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $queueNumber = ($lastQueue ?? 0) + 1;

        $prefix = $request->type === 'student' ? 'ST' : 'VS';

        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $suffix = '';

            for ($i = 0; $i < 4; $i++) {
                $suffix .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $ticketNumber = "{$prefix}-{$suffix}";
        } while (
            Visitor::whereDate('created_at', $today)
            ->where('ticket_number', $ticketNumber)
            ->exists()
        );

        $visitor = Visitor::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'id_number' => $request->id_number,
            'type' => $request->type,
            'course_id' => $request->type === 'student'
                ? $request->course_id
                : null,

            'office_id' => $office->id,
            'previous_office_id' => null,

            'queue_number' => $queueNumber,
            'ticket_number' => $ticketNumber,

            'status' => 'waiting',
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
                    return [
                        $cashier->id => null
                    ];
                }

                $visitor = $servingVisitors[$cashier->id];

                return [
                    $cashier->id => [
                        'ticket' => $visitor->ticket_number,
                        'queue' => $visitor->queue_number,
                    ],
                ];
            })->toArray(),

            'upcoming' => $upcomingQueues
                ->pluck('ticket_number')
                ->toArray(),
        ];
    }
}
