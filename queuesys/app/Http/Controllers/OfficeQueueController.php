<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Office;
use App\Models\User;
use App\Models\VisitorTransfer;
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
        $user = Auth::user();

        // All visitors currently being served
        $serving = Visitor::with('cashier')
            ->where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->get();

        // Waiting visitors
        $waitingQuery = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting');

        if (
            $user &&
            $user->isStaff() &&
            $office->abbreviation === 'RO'
        ) {
            $assignedCourseIds = $user->courses()
                ->pluck('courses.id');

            $waitingQuery->where(function ($query) use ($assignedCourseIds) {
                $query->where('type', 'visitor')
                    ->orWhere(function ($studentQuery) use ($assignedCourseIds) {
                        $studentQuery
                            ->where('type', 'student')
                            ->whereIn('course_id', $assignedCourseIds);
                    });
            });
        }

        $waiting = $waitingQuery
            ->orderByDesc('priority')
            ->orderBy('queue_number')
            ->get();

        $allOffices = Office::with('users')->get();

        return view('queue.office', compact(
            'office',
            'serving',
            'waiting',
            'allOffices'
        ));
    }

    // Assign the next waiting visitor to the logged-in cashier
    public function next($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();
            $cashierId = Auth::id();
            $user = Auth::user();

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

            // Get waiting visitors
            $nextQuery = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting');

            /*
             * Registrar filtering:
             * - Visitors can be served by any Registrar staff.
             * - Students can only be served if their course
             *   is assigned to the logged-in Registrar staff.
             */
            if (
                $user &&
                $user->isStaff() &&
                $office->abbreviation === 'RO'
            ) {
                $assignedCourseIds = $user->courses()
                    ->pluck('courses.id');

                $nextQuery->where(function ($query) use ($assignedCourseIds) {
                    $query->where('type', 'visitor')
                        ->orWhere(function ($studentQuery) use ($assignedCourseIds) {
                            $studentQuery
                                ->where('type', 'student')
                                ->whereIn('course_id', $assignedCourseIds);
                        });
                });
            }

            // Priority first, then FIFO
            $next = $nextQuery
                ->orderByDesc('priority')
                ->orderBy('queue_number')
                ->first();

            if (!$next) {
                return [
                    'status' => 'empty',
                    'message' => 'No visitors left to serve.'
                ];
            }

            // Serve the visitor
            $next->update([
                'status' => 'serving',
                'cashier_id' => $cashierId,
            ]);

            $this->broadcastMonitorUpdate($office->id);

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
            $this->broadcastMonitorUpdate($office->id);

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
            $this->broadcastMonitorUpdate($office->id);
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
                $q->where('name', 'like', "%{$search}%")
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
        $ids = array_filter(explode(',', $request->input('selected_ids')));

        $query = Visitor::whereIn('id', $ids)
            ->where('status', 'skipped');

        $user = Auth::user();
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        $visitors = $query->get();

        foreach ($visitors as $visitor) {
            $visitor->update([
                'status' => 'waiting',
            ]);
        }

        $officeIds = $visitors->pluck('office_id')->unique();
        foreach ($officeIds as $officeId) {
            $this->broadcastMonitorUpdate($officeId);
        }

        return back()->with('success', 'Selected visitors restored to the end of the queue.');
    }

    public function swapSkipped(Request $request)
    {
        $request->validate([
            'selected_id' => 'required|integer|exists:visitors,id',
        ]);

        $result = DB::transaction(function () use ($request) {

            $user = Auth::user();
            $today = now()->toDateString();

            $skipped = Visitor::where('id', $request->selected_id)
                ->whereDate('created_at', $today)
                ->where('status', 'skipped')
                ->where('office_id', $user->office_id)
                ->lockForUpdate()
                ->first();

            if (!$skipped) {
                return [
                    'status' => 'error',
                    'message' => 'The selected visitor is no longer available for swapping.',
                ];
            }

            $serving = Visitor::where('office_id', $user->office_id)
                ->whereDate('created_at', $today)
                ->where('status', 'serving')
                ->where('cashier_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$serving) {
                return [
                    'status' => 'error',
                    'message' => 'You are not currently serving a visitor.',
                ];
            }

            $serving->update([
                'status' => 'waiting',
                'cashier_id' => null,
            ]);

            $skipped->update([
                'status' => 'serving',
                'cashier_id' => $user->id,
            ]);

            $this->broadcastMonitorUpdate($user->office_id);

            return [
                'status' => 'success',
                'message' => "Ticket {$skipped->ticket_number} is now being served.",
            ];
        });

        return $result['status'] === 'success'
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }
    public function transfer(Request $request, $id)
    {
        $request->validate([
            'new_office_id' => 'required|exists:offices,id',
            'new_cashier_id' => 'nullable|exists:users,id',
        ]);

        $visitor = Visitor::findOrFail($id);

        if (
            $visitor->status !== 'serving' ||
            $visitor->cashier_id !== auth()->id()
        ) {
            return back()->with(
                'error',
                'You can only transfer a visitor you are currently serving.'
            );
        }

        $currentOffice = Office::findOrFail($visitor->office_id);
        $targetOffice = Office::findOrFail($request->new_office_id);

        if ($targetOffice->id === $currentOffice->id) {

            if (!$request->new_cashier_id) {
                return back()->with(
                    'error',
                    'Please select the staff member you want to transfer to.'
                );
            }

            $newStaff = User::where('id', $request->new_cashier_id)
                ->where('office_id', $currentOffice->id)
                ->first();

            if (!$newStaff) {
                return back()->with(
                    'error',
                    'Invalid staff member selected.'
                );
            }

            if ($newStaff->id === auth()->id()) {
                return back()->with(
                    'error',
                    'You cannot transfer the visitor to yourself.'
                );
            }

            $alreadyServing = Visitor::where('office_id', $currentOffice->id)
                ->whereDate('created_at', today())
                ->where('status', 'serving')
                ->where('cashier_id', $newStaff->id)
                ->exists();

            if ($alreadyServing) {
                return back()->with(
                    'error',
                    "{$newStaff->name} is already serving another visitor."
                );
            }

            $visitor->update([
                'cashier_id' => $newStaff->id,
            ]);

            $this->broadcastMonitorUpdate($currentOffice->id);

            return response()->json([
                'success' => true,
                'message' => "Visitor transferred to {$newStaff->name}.",
            ]);
        }

        $today = now()->toDateString();
        $lastQueue = Visitor::where('office_id', $targetOffice->id)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $nextQueue = ($lastQueue ?? 0) + 1;

        VisitorTransfer::create([
            'visitor_id' => $visitor->id,
            'from_office_id' => $currentOffice->id,
            'to_office_id' => $targetOffice->id,
            'from_queue_number' => $visitor->queue_number,
            'to_queue_number' => $nextQueue,
            'transferred_by' => auth()->id(),
            'transferred_at' => now(),
        ]);

        $visitor->update([
            'office_id' => $targetOffice->id,
            'previous_office_id' => $currentOffice->id,
            'queue_number' => $nextQueue,
            'status' => 'waiting',
            'cashier_id' => null,
        ]);

        $this->broadcastMonitorUpdate($currentOffice->id);
        $this->broadcastMonitorUpdate($targetOffice->id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'new_ticket' => $visitor->ticket_number,
                'message' => "Visitor transferred to {$targetOffice->name}.",
            ]);
        }

        return back()->with(
            'success',
            "Visitor transferred to {$targetOffice->name}. Ticket remains {$visitor->ticket_number}."
        );
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        $isStaff = $user &&
            method_exists($user, 'isStaff') &&
            $user->isStaff();

        $isAdmin = $user &&
            method_exists($user, 'isAdmin') &&
            $user->isAdmin();

        $query = Visitor::with([
            'office',
            'previousOffice',
            'cashier',
            'course',
            'transfers' => function ($query) {
                $query->orderBy('transferred_at');
            },
            'transfers.fromOffice:id,name',
            'transfers.toOffice:id,name',
            'transfers.transferredBy:id,name',
        ])->whereIn('status', ['done', 'skipped', 'transferred']);

        if ($isStaff) {
            $query->where(function ($q) use ($user) {
                $q->where('office_id', $user->office_id)
                    ->orWhereHas('transfers', function ($transferQuery) use ($user) {
                        $transferQuery
                            ->where('from_office_id', $user->office_id)
                            ->orWhere('to_office_id', $user->office_id);
                    });
            });
        }

        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('cashier', function ($cashierQuery) use ($search) {
                        $cashierQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('office', function ($officeQuery) use ($search) {
                        $officeQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $history = $query
            ->orderByDesc('updated_at')
            ->get();

        $history->each(function ($visitor) {

            $firstTransfer = $visitor->transfers
                ->sortBy('transferred_at')
                ->first();

            if ($firstTransfer) {
                $visitor->registration_office_id = $firstTransfer->from_office_id;
                $visitor->registration_office_name =
                    $firstTransfer->fromOffice->name ?? '—';
            } else {
                $visitor->registration_office_id = $visitor->office_id;
                $visitor->registration_office_name =
                    $visitor->office->name ?? '—';
            }
        });

        if ($isAdmin) {
            $history = $history
                ->groupBy(function ($visitor) {
                    return $visitor->name . '_' .
                        $visitor->created_at->format('Y-m-d');
                })
                ->map(function ($tickets) {

                    $tickets = $tickets
                        ->sortBy('created_at')
                        ->values();

                    return [
                        'visitor' => $tickets->last(),
                        'tickets' => $tickets,
                    ];
                })
                ->sortByDesc(function ($group) {
                    return $group['visitor']->updated_at;
                })
                ->values();
        }

        return view('queue.history', [
            'history' => $history,
            'isAdmin' => $isAdmin,
            'isStaff' => $isStaff,
            'staffOfficeId' => $isStaff
                ? $user->office_id
                : null,
        ]);
    }
    public function statistics(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Report type
        |--------------------------------------------------------------------------
        */

        $range = $request->get('range', 'weekly');

        if (!in_array($range, ['weekly', 'monthly'])) {
            $range = 'weekly';
        }

        /*
        |--------------------------------------------------------------------------
        | Selected month / year
        |--------------------------------------------------------------------------
        */

        $selectedMonth = $request->get(
            'month',
            now('Asia/Manila')->format('Y-m')
        );

        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = now('Asia/Manila')->format('Y-m');
        }

        $monthStart = Carbon::createFromFormat(
            'Y-m',
            $selectedMonth,
            'Asia/Manila'
        )->startOfMonth();

        $monthEnd = $monthStart->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Month / Year dropdown values
        |--------------------------------------------------------------------------
        */

        $selectedYear = (int) $monthStart->format('Y');
        $selectedMonthNumber = $monthStart->format('m');

        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        $currentYear = now('Asia/Manila')->year;

        $years = range(
            $currentYear - 2,
            $currentYear + 2
        );

        $weeks = [];

        $current = $monthStart->copy();
        $weekNumber = 1;

        while ($current->lte($monthEnd)) {

            $weekStart = $current->copy();

            $weekEnd = $current
                ->copy()
                ->endOfWeek(Carbon::SUNDAY);

            if ($weekEnd->gt($monthEnd)) {
                $weekEnd = $monthEnd->copy();
            }

            $weeks[$weekNumber] = [
                'start' => $weekStart->copy(),
                'end' => $weekEnd->copy(),
            ];

            $current = $weekEnd->copy()->addDay();

            $weekNumber++;
        }

        /*
        |--------------------------------------------------------------------------
        | Selected week
        |--------------------------------------------------------------------------
        */

        $selectedWeek = (int) $request->get('week', 1);

        if (!isset($weeks[$selectedWeek])) {
            $selectedWeek = 1;
        }

        /*
        |--------------------------------------------------------------------------
        | Reporting period
        |--------------------------------------------------------------------------
        */

        if ($range === 'weekly') {

            $start = $weeks[$selectedWeek]['start']
                ->copy()
                ->startOfDay();

            $end = $weeks[$selectedWeek]['end']
                ->copy()
                ->endOfDay();

        } else {

            $start = $monthStart
                ->copy()
                ->startOfDay();

            $end = $monthEnd
                ->copy()
                ->endOfDay();
        }

        $visitorsQuery = Visitor::with([
            'transfers',
        ])->where(function ($query) use ($start, $end) {

            $query
                ->whereBetween('created_at', [$start, $end])
                ->orWhereHas('transfers', function ($transferQuery) use ($start, $end) {
                    $transferQuery->whereBetween(
                        'transferred_at',
                        [$start, $end]
                    );
                });

        });

        /*
        |--------------------------------------------------------------------------
        | STAFF STATISTICS
        |--------------------------------------------------------------------------
        */

        if ($user->isStaff()) {

            $officeId = $user->office_id;

            $visitors = $visitorsQuery
                ->where(function ($query) use ($officeId) {

                    $query
                        ->where('office_id', $officeId)

                        ->orWhereHas('transfers', function ($transferQuery) use ($officeId) {

                            $transferQuery
                                ->where('from_office_id', $officeId)
                                ->orWhere('to_office_id', $officeId);

                        });

                })
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Determine every office touched by each ticket
            |--------------------------------------------------------------------------
            */

            $officeTickets = $visitors
                ->filter(function ($visitor) use ($officeId) {

                    if ((int) $visitor->office_id === (int) $officeId) {
                        return true;
                    }

                    return $visitor->transfers->contains(function ($transfer) use ($officeId) {

                        return
                            (int) $transfer->from_office_id === (int) $officeId ||
                            (int) $transfer->to_office_id === (int) $officeId;

                    });

                })

                /*
                |--------------------------------------------------------------------------
                | Same ticket should only count once for this office
                |--------------------------------------------------------------------------
                */

                ->unique('ticket_number')
                ->values();

            $totalTickets = $officeTickets->count();

            /*
            |--------------------------------------------------------------------------
            | Completed
            |--------------------------------------------------------------------------
            |
            | Completed belongs to the office where the ticket currently finished.
            |
            */

            $completed = $officeTickets
                ->filter(function ($visitor) use ($officeId) {

                    return
                        $visitor->status === 'done' &&
                        (int) $visitor->office_id === (int) $officeId;

                })
                ->unique('ticket_number')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Skipped
            |--------------------------------------------------------------------------
            */

            $skipped = $officeTickets
                ->filter(function ($visitor) use ($officeId) {

                    return
                        $visitor->status === 'skipped' &&
                        (int) $visitor->office_id === (int) $officeId;

                })
                ->unique('ticket_number')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Transferred
            |--------------------------------------------------------------------------
            |
            | A transferred ticket is one that had a transfer during the
            | selected reporting period and has not ultimately been completed
            | or skipped at this office.
            |
            */

            $transferred = $officeTickets
                ->filter(function ($visitor) use ($officeId, $start, $end) {

                    $wasTransferred = $visitor->transfers->contains(function ($transfer) use ($start, $end) {

                        if (!$transfer->transferred_at) {
                            return false;
                        }

                        $transferredAt = Carbon::parse(
                            $transfer->transferred_at,
                            'Asia/Manila'
                        );

                        return $transferredAt->between(
                            $start,
                            $end
                        );

                    });

                    if (!$wasTransferred) {
                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | If the ticket eventually completed/skipped at this office,
                    | count it under that final status instead of Transferred.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $visitor->office_id === (int) $officeId &&
                        in_array($visitor->status, ['done', 'skipped'])
                    ) {
                        return false;
                    }

                    return true;

                })
                ->unique('ticket_number')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Students / Visitors
            |--------------------------------------------------------------------------
            */

            $students = $officeTickets
                ->where('type', 'student')
                ->unique('ticket_number')
                ->count();

            $visitorsCount = $officeTickets
                ->where('type', 'visitor')
                ->unique('ticket_number')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Chart data
            |--------------------------------------------------------------------------
            |
            | Weekly:
            | Show the selected week's daily ticket totals.
            |
            | Monthly:
            | Show Week 1, Week 2, Week 3, etc.
            |
            */

            $labels = [];
            $counts = [];

            if ($range === 'weekly') {

                $current = $start->copy()->startOfDay();

                while ($current->lte($end)) {

                    $dayStart = $current->copy()->startOfDay();
                    $dayEnd = $current->copy()->endOfDay();

                    $dayTickets = $officeTickets
                        ->filter(function ($visitor) use ($dayStart, $dayEnd) {

                            $created = Carbon::parse(
                                $visitor->created_at,
                                'Asia/Manila'
                            );

                            return $created->between(
                                $dayStart,
                                $dayEnd
                            );

                        })
                        ->unique('ticket_number');

                    $labels[] = $current->format('D');
                    $counts[] = $dayTickets->count();

                    $current->addDay();
                }

            } else {

                foreach ($weeks as $weekNumber => $week) {

                    $weekStart = $week['start']
                        ->copy()
                        ->startOfDay();

                    $weekEnd = $week['end']
                        ->copy()
                        ->endOfDay();

                    $weekTickets = $officeTickets
                        ->filter(function ($visitor) use ($weekStart, $weekEnd) {

                            $created = Carbon::parse(
                                $visitor->created_at,
                                'Asia/Manila'
                            );

                            return $created->between(
                                $weekStart,
                                $weekEnd
                            );

                        })
                        ->unique('ticket_number');

                    $labels[] = 'Week ' . $weekNumber;
                    $counts[] = $weekTickets->count();
                }
            }

            return view('queue.statistics', [

                'role' => 'staff',

                'range' => $range,

                'selectedMonth' => $selectedMonth,
                'selectedMonthNumber' => $selectedMonthNumber,
                'selectedYear' => $selectedYear,

                'months' => $months,
                'years' => $years,

                'weeks' => $weeks,
                'selectedWeek' => $selectedWeek,

                'periodStart' => $start,
                'periodEnd' => $end,

                'totalTickets' => $totalTickets,
                'completed' => $completed,
                'skipped' => $skipped,
                'transferred' => $transferred,

                'students' => $students,
                'visitorsCount' => $visitorsCount,

                'labels' => $labels,
                'counts' => $counts,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN STATISTICS
        |--------------------------------------------------------------------------
        */

        if ($user->isAdmin()) {

            $offices = Office::orderBy('name')->get();

            $visitors = $visitorsQuery->get();

            $officeData = $offices->map(function ($office) use (
                $visitors,
                $start,
                $end
            ) {

                /*
                |--------------------------------------------------------------------------
                | Tickets that touched this office
                |--------------------------------------------------------------------------
                */

                $officeTickets = $visitors
                    ->filter(function ($visitor) use ($office) {

                        if ((int) $visitor->office_id === (int) $office->id) {
                            return true;
                        }

                        return $visitor->transfers->contains(function ($transfer) use ($office) {

                            return
                                (int) $transfer->from_office_id === (int) $office->id ||
                                (int) $transfer->to_office_id === (int) $office->id;

                        });

                    })
                    ->unique('ticket_number')
                    ->values();

                $totalTickets = $officeTickets->count();

                /*
                |--------------------------------------------------------------------------
                | Completed
                |--------------------------------------------------------------------------
                */

                $completed = $officeTickets
                    ->filter(function ($visitor) use ($office) {

                        return
                            $visitor->status === 'done' &&
                            (int) $visitor->office_id === (int) $office->id;

                    })
                    ->unique('ticket_number')
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Skipped
                |--------------------------------------------------------------------------
                */

                $skipped = $officeTickets
                    ->filter(function ($visitor) use ($office) {

                        return
                            $visitor->status === 'skipped' &&
                            (int) $visitor->office_id === (int) $office->id;

                    })
                    ->unique('ticket_number')
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Transferred
                |--------------------------------------------------------------------------
                */

                $transferred = $officeTickets
                    ->filter(function ($visitor) use (
                        $office,
                        $start,
                        $end
                    ) {

                        $wasTransferred = $visitor->transfers->contains(function ($transfer) use (
                            $start,
                            $end
                        ) {

                            if (!$transfer->transferred_at) {
                                return false;
                            }

                            $transferredAt = Carbon::parse(
                                $transfer->transferred_at,
                                'Asia/Manila'
                            );

                            return $transferredAt->between(
                                $start,
                                $end
                            );

                        });

                        if (!$wasTransferred) {
                            return false;
                        }

                        if (
                            (int) $visitor->office_id === (int) $office->id &&
                            in_array($visitor->status, ['done', 'skipped'])
                        ) {
                            return false;
                        }

                        return true;

                    })
                    ->unique('ticket_number')
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Students / Visitors
                |--------------------------------------------------------------------------
                */

                $students = $officeTickets
                    ->where('type', 'student')
                    ->unique('ticket_number')
                    ->count();

                $visitorCount = $officeTickets
                    ->where('type', 'visitor')
                    ->unique('ticket_number')
                    ->count();

                return [
                    'office' => $office->name,
                    'total' => $totalTickets,
                    'completed' => $completed,
                    'skipped' => $skipped,
                    'transferred' => $transferred,
                    'students' => $students,
                    'visitors' => $visitorCount,
                ];
            });

            /*
            |--------------------------------------------------------------------------
            | Admin overall totals
            |--------------------------------------------------------------------------
            */

            $adminTotalTickets = $officeData->sum('total');
            $adminCompleted = $officeData->sum('completed');
            $adminSkipped = $officeData->sum('skipped');
            $adminTransferred = $officeData->sum('transferred');
            $adminStudents = $officeData->sum('students');
            $adminVisitors = $officeData->sum('visitors');

            return view('queue.statistics', [

                'role' => 'admin',

                'range' => $range,

                'selectedMonth' => $selectedMonth,
                'selectedMonthNumber' => $selectedMonthNumber,
                'selectedYear' => $selectedYear,

                'months' => $months,
                'years' => $years,

                'weeks' => $weeks,
                'selectedWeek' => $selectedWeek,

                'periodStart' => $start,
                'periodEnd' => $end,

                'officeData' => $officeData,

                'officeLabels' => $officeData->pluck('office'),
                'officeCounts' => $officeData->pluck('total'),

                'totalTickets' => $adminTotalTickets,
                'completed' => $adminCompleted,
                'skipped' => $adminSkipped,
                'transferred' => $adminTransferred,
                'students' => $adminStudents,
                'visitorsCount' => $adminVisitors,
            ]);
        }

        abort(403);
    }

    public function callAgain($officeId)
    {
        $office = $this->authorizeOffice($officeId);
        $today = now()->toDateString();
        $cashierId = Auth::id();

        $serving = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->where('cashier_id', $cashierId)
            ->first();

        if (!$serving) {
            return back()->with(
                'error',
                'You are not currently serving any visitor.'
            );
        }

        $serving->load('cashier');

        broadcast(new \App\Events\QueueAnnounce(
            $office->id,
            $serving->ticket_number,
            $serving->cashier->name ?? 'Cashier'
        ));

        return back()->with(
            'success',
            "Ticket {$serving->ticket_number} announced again."
        );
    }

    private function broadcastMonitorUpdate(int $officeId)
    {
        $serving = Visitor::where('office_id', $officeId)
            ->whereDate('created_at', now()->toDateString())
            ->where('status', 'serving')
            ->get()
            ->groupBy('cashier_id')
            ->mapWithKeys(fn ($visitors, $cashierId) => [
                $cashierId => [
                    'ticket' => $visitors[0]->ticket_number,
                    'queue'  => $visitors[0]->queue_number,
                ]
            ])->toArray();

        $upcoming = Visitor::where('office_id', $officeId)
            ->whereDate('created_at', now()->toDateString())
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->pluck('ticket_number')
            ->toArray();

        broadcast(new \App\Events\QueueUpdated($officeId, [
            'serving' => $serving,
            'upcoming' => $upcoming,
        ]));
    }
}
