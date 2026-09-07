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
        ])->where(function ($q) {
            $q->whereIn('status', ['done', 'skipped', 'transferred'])
                ->orWhereNull('office_id');
        });

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
                    })
                    ->orWhere('other_office', 'like', "%{$search}%");
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
            } elseif (is_null($visitor->office_id)) {
                $visitor->registration_office_id = null;
                $visitor->registration_office_name =
                    $visitor->other_office ?? '—';
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
                        $visitor->created_at->format('Y-m-d') . '_' .
                        ($visitor->office_id ?? 'others') . '_' .
                        ($visitor->other_office ?? '');
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
        $user = Auth::user();

        $range = $request->get('range', 'weekly');
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        if (!in_array($range, ['weekly', 'monthly'])) {
            $range = 'weekly';
        }

        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $selectedDate = now();
            $selectedMonth = now()->format('Y-m');
        }

        $selectedYear = $selectedDate->year;
        $selectedMonthNumber = $selectedDate->month;

        $months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => Carbon::create()->month($month)->format('F')];
        });

        $years = range(now()->year - 2, now()->year + 1);

        $weeks = [];
        $selectedWeek = (int) $request->get('week', 1);

        if ($range === 'weekly') {
            $firstDay = Carbon::create(
                $selectedYear,
                $selectedMonthNumber,
                1
            );

            $lastDay = $firstDay->copy()->endOfMonth();

            $weekStart = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
            $weekNumber = 1;

            while ($weekStart->lte($lastDay)) {
                $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

                if ($weekEnd->gt($lastDay)) {
                    $weekEnd = $lastDay->copy();
                }

                $weekStartForPeriod = $weekStart->lt($firstDay)
                    ? $firstDay->copy()
                    : $weekStart->copy();

                $weeks[$weekNumber] = [
                    'start' => $weekStartForPeriod,
                    'end' => $weekEnd,
                ];

                $weekStart->addWeek();
                $weekNumber++;
            }

            if (!isset($weeks[$selectedWeek])) {
                $selectedWeek = 1;
            }

            $periodStart = $weeks[$selectedWeek]['start']
                ->copy()
                ->startOfDay();

            $periodEnd = $weeks[$selectedWeek]['end']
                ->copy()
                ->endOfDay();
        } else {
            $periodStart = Carbon::create(
                $selectedYear,
                $selectedMonthNumber,
                1
            )->startOfDay();

            $periodEnd = $periodStart
                ->copy()
                ->endOfMonth()
                ->endOfDay();
        }

        $isStaff = $user &&
            method_exists($user, 'isStaff') &&
            $user->isStaff();

        $isAdmin = $user &&
            method_exists($user, 'isAdmin') &&
            $user->isAdmin();

        if ($isStaff) {
            $officeId = $user->office_id;

            $officeTickets = Visitor::where('office_id', $officeId)
                ->whereNotNull('ticket_number')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get();

            $transferredTickets = VisitorTransfer::where('from_office_id', $officeId)
                ->whereColumn('from_office_id', '!=', 'to_office_id')
                ->whereBetween('transferred_at', [$periodStart, $periodEnd])
                ->with('visitor')
                ->get();

            $totalTickets = $officeTickets->count() + $transferredTickets->count();
            $completed = $officeTickets->where('status', 'done')->count();
            $skipped = $officeTickets->where('status', 'skipped')->count();
            $transferred = $transferredTickets->count();

            $students = $officeTickets->where('type', 'student')->count() + $transferredTickets->filter(function ($transfer) {
                return $transfer->visitor && $transfer->visitor->type === 'student';
            })->count();

            $visitorsCount = $officeTickets->where('type', 'visitor')->count() + $transferredTickets->filter(function ($transfer) {
                return $transfer->visitor && $transfer->visitor->type === 'visitor';
            })->count();

            $labels = [];
            $counts = [];

            if ($range === 'weekly') {
                $current = $periodStart->copy()->startOfDay();

                while ($current->lte($periodEnd)) {
                    $dayStart = $current->copy()->startOfDay();
                    $dayEnd = $current->copy()->endOfDay();

                    $normalCount = Visitor::where('office_id', $officeId)
                        ->whereNotNull('ticket_number')
                        ->whereBetween('created_at', [$dayStart, $dayEnd])
                        ->count();

                    $transferCount = VisitorTransfer::where('from_office_id', $officeId)
                        ->whereBetween('transferred_at', [$dayStart, $dayEnd])
                        ->count();

                    $labels[] = $current->format('M j');
                    $counts[] = $normalCount + $transferCount;
                    $current->addDay();
                }
            } else {
                $current = $periodStart->copy()->startOfWeek(Carbon::MONDAY);
                $week = 1;

                while ($current->lte($periodEnd)) {
                    $weekStart = $current->copy()->startOfDay();
                    $weekEnd = $current->copy()->endOfWeek(Carbon::SUNDAY);

                    if ($weekEnd->gt($periodEnd)) {
                        $weekEnd = $periodEnd->copy()->endOfDay();
                    }

                    $normalCount = Visitor::where('office_id', $officeId)
                        ->whereNotNull('ticket_number')
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();

                    $transferCount = VisitorTransfer::where('from_office_id', $officeId)
                        ->whereBetween('transferred_at', [$weekStart, $weekEnd])
                        ->count();

                    $labels[] = "Week {$week}";
                    $counts[] = $normalCount + $transferCount;
                    $current->addWeek();
                    $week++;
                }
            }

            $officeData = collect();
            $registrationLabels = [];
            $registrationCounts = [];
            $totalRegistrations = 0;
            $others = 0;

            return view('queue.statistics', compact(
                'range',
                'months',
                'weeks',
                'years',
                'selectedMonth',
                'selectedMonthNumber',
                'selectedWeek',
                'selectedYear',
                'periodStart',
                'periodEnd',
                'totalRegistrations',
                'totalTickets',
                'completed',
                'skipped',
                'transferred',
                'students',
                'visitorsCount',
                'officeData',
                'labels',
                'counts',
                'registrationLabels',
                'registrationCounts',
                'others'
            ))->with('role', 'staff');
        }

        $registrationQuery = Visitor::query()
            ->whereBetween('created_at', [
                $periodStart,
                $periodEnd,
            ]);

        $registrations = $registrationQuery->get();
        $offices = Office::orderBy('name')->get();

        $officeData = $offices->map(function ($office) use (
            $periodStart,
            $periodEnd
        ) {
            $registrations = Visitor::where('office_id', $office->id)
                ->whereBetween('created_at', [
                    $periodStart,
                    $periodEnd,
                ])
                ->get();

            $tickets = Visitor::where('office_id', $office->id)
                ->whereNotNull('ticket_number')
                ->whereBetween('created_at', [
                    $periodStart,
                    $periodEnd,
                ])
                ->get();

            return [
                'office' => $office->name,
                'isOther' => false,
                'totalRegistrations' => $registrations->count(),
                'total' => $tickets->count(),
                'completed' => $tickets
                    ->where('status', 'done')
                    ->count(),
                'skipped' => $tickets
                    ->where('status', 'skipped')
                    ->count(),
                'transferred' => $tickets
                    ->where('status', 'transferred')
                    ->count(),
                'students' => $registrations
                    ->where('type', 'student')
                    ->count(),
                'visitors' => $registrations
                    ->where('type', 'visitor')
                    ->count(),
            ];
        });

        $otherData = $registrations
            ->whereNull('office_id')
            ->groupBy(function ($visitor) {
                return trim($visitor->other_office ?? 'Unknown');
            })
            ->map(function ($records, $officeName) {
                return [
                    'office' => $officeName,
                    'isOther' => true,
                    'totalRegistrations' => $records->count(),
                    'total' => 0,
                    'completed' => 0,
                    'skipped' => 0,
                    'transferred' => 0,
                    'students' => $records
                        ->where('type', 'student')
                        ->count(),
                    'visitors' => $records
                        ->where('type', 'visitor')
                        ->count(),
                ];
            })
            ->values();

        $officeData = $officeData
            ->concat($otherData)
            ->values();

        $registrationLabels = $officeData
            ->pluck('office')
            ->values()
            ->toArray();

        $registrationCounts = $officeData
            ->pluck('totalRegistrations')
            ->values()
            ->toArray();

        $totalRegistrations = $officeData
            ->sum('totalRegistrations');

        $totalTickets = $officeData
            ->sum('total');

        $completed = $officeData
            ->sum('completed');

        $skipped = $officeData
            ->sum('skipped');

        $transferred = $officeData
            ->sum('transferred');

        $students = $officeData
            ->sum('students');

        $visitorsCount = $officeData
            ->sum('visitors');

        $others = $otherData
            ->sum('totalRegistrations');

        $labels = [];
        $counts = [];

        return view('queue.statistics', [
            'range' => $range,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedMonthNumber' => $selectedMonthNumber,
            'selectedWeek' => $selectedWeek,
            'months' => $months,
            'years' => $years,
            'weeks' => $weeks,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'totalRegistrations' => $totalRegistrations,
            'totalTickets' => $totalTickets,
            'completed' => $completed,
            'skipped' => $skipped,
            'transferred' => $transferred,
            'students' => $students,
            'visitorsCount' => $visitorsCount,
            'others' => $others,
            'labels' => $labels,
            'counts' => $counts,
            'registrationLabels' => $registrationLabels,
            'registrationCounts' => $registrationCounts,
            'officeData' => $officeData,
        ])->with('role', 'admin');
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
