<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Office;
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

        // All visitors currently being served (by any cashier)
        $serving = Visitor::with('cashier')
            ->where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->get();

        // Waiting visitors in FIFO order
        $waiting = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->orderBy('updated_at')
            ->get();

        $allOffices = Office::all();

        return view('queue.office', compact('office', 'serving', 'waiting', 'allOffices'));
    }

    // Assign the next waiting visitor to the logged-in cashier
    public function next($officeId)
    {
        $result = DB::transaction(function () use ($officeId) {
            $office = $this->authorizeOffice($officeId);
            $today = now()->toDateString();
            $cashierId = Auth::id();

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

            // Get the next visitor, priority first
            $next = Visitor::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting')
                ->orderByDesc('priority')
                ->orderBy('updated_at')
                ->first();

            if (!$next) {
                return ['status' => 'empty', 'message' => 'No visitors left to serve.'];
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
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
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
                'updated_at' => now(),
            ]);
        }

        $officeIds = $visitors->pluck('office_id')->unique();
        foreach ($officeIds as $officeId) {
            $this->broadcastMonitorUpdate($officeId);
        }

        return back()->with('success', 'Selected visitors restored to the end of the queue.');
    }

    public function transfer(Request $request, $id)
    {
        $request->validate([
            'new_office_id' => 'required|exists:offices,id',
        ]);

        $old = Visitor::findOrFail($id);

        if ($old->status !== 'serving' || $old->cashier_id !== auth()->id()) {
            return back()->with('error', 'You can only transfer a visitor you are currently serving.');
        }

        $old->status = 'transferred';
        $old->save();

        $this->broadcastMonitorUpdate($old->office_id);
        $newOffice = Office::findOrFail($request->new_office_id);

        $nextQueue = (Visitor::where('office_id', $newOffice->id)
            ->whereDate('created_at', today())
            ->max(DB::raw('CAST(SUBSTR(ticket_number, 4) AS INTEGER)')) ?? 0) + 1;

        $prefix = $newOffice->abbreviation;
        $nextTicket = sprintf("%s-%03d", $prefix, $nextQueue);


        $new = Visitor::create([
            'name'                => $old->name,
            'contact_number'      => $old->contact_number,
            'id_number'           => $old->id_number,

            'office_id'           => $newOffice->id,
            'previous_office_id'  => $old->office_id,

            'queue_number'        => $nextQueue,
            'ticket_number'       => $nextTicket,

            'status'              => 'waiting',
            'priority'            => $old->priority,
        ]);

        $this->broadcastMonitorUpdate($newOffice->id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'new_ticket' => $nextTicket,
            ]);
        }

        return back()->with('success', 'Visitor transferred successfully.');
    }


    private function transferResponse(Request $request, array $data)
    {
        if ($request->ajax()) {
            return response()->json($data);
        }

        return back()->with(
            $data['success'] ? 'success' : 'error',
            $data['success'] ? 'Visitor transferred successfully.' : $data['message']
        );
    }

    public function history(Request $request)
    {
        $query = Visitor::with(['office', 'cashier'])
            ->whereIn('status', ['done', 'skipped', 'transferred']);

        $user = Auth::user();

        // Staff sees only their office
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $query->where('office_id', $user->office_id);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('cashier', fn ($c) =>
                    $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('office', fn ($o) =>
                    $o->where('name', 'like', "%{$search}%"));

                if (strtotime($search)) {
                    $q->orWhereDate('created_at', $search);
                }
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $history = $query
            ->orderByDesc('created_at')
            ->get();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {

            $history = $history
                ->groupBy(function ($item) {

                    return $item->name . '_' .
                        $item->created_at->format('Y-m-d');
                })
                ->map(function ($tickets) {

                    return [
                        'visitor' => $tickets->first(),
                        'tickets' => $tickets
                            ->sortBy('created_at')
                            ->values(),
                    ];
                })
                ->values();
        }

        return view('queue.history', compact('history'));
    }

    public function statistics(Request $request)
    {
        $user = auth()->user();
        $range = $request->get('range', 'weekly');
        $selectedMonth = $request->get('month') ?? now('Asia/Manila')->format('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = now('Asia/Manila')->format('Y-m');
        }
        $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth, 'Asia/Manila')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        if ($range === 'weekly') {
            $referenceDate = now('Asia/Manila');
            $start = $referenceDate->copy()->startOfWeek(Carbon::MONDAY);
            $end = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY);
        } else {
            $start = $monthStart->copy();
            $end = $monthEnd->copy();
        }

        if ($user->isStaff()) {
            $visitors = DB::table('visitors')
                ->where('office_id', $user->office_id)
                ->whereBetween('created_at', [$start, $end])
                ->get();

            if ($range === 'weekly') {

                $data = $visitors
                    ->groupBy(function ($v) {
                        return Carbon::parse($v->created_at)->toDateString();
                    })
                    ->map->count();

                $labels = [];
                $counts = [];

                $current = $start->copy();
                while ($current <= $end) {
                    $dateString = $current->toDateString();
                    $labels[] = $current->format('D');
                    $counts[] = $data[$dateString] ?? 0;
                    $current->addDay();
                }

                return view('queue.statistics', [
                    'role' => 'staff',
                    'labels' => $labels,
                    'counts' => $counts,
                    'range' => $range,
                    'weekCounts' => [],
                    'selectedMonth' => $selectedMonth
                ]);
            }

            $weekRanges = [];
            $weekCounts = [];

            $current = $start->copy();
            $weekNumber = 1;

            while ($current <= $end) {
                $weekStart = $current->copy();
                $weekEnd = $current->copy()->endOfWeek(Carbon::SUNDAY);
                if ($weekEnd->gt($end)) $weekEnd = $end->copy();

                $weekRanges[$weekNumber] = [
                    'start' => $weekStart->format('M j'),
                    'end' => $weekEnd->format('M j')
                ];

                $weekCounts[$weekNumber] = 0;
                $current = $weekEnd->copy()->addDay();
                $weekNumber++;
            }

            foreach ($visitors as $visitor) {
                $date = Carbon::parse($visitor->created_at);
                foreach ($weekRanges as $week => $rangeData) {
                    $startDate = Carbon::parse($rangeData['start']);
                    $endDate = Carbon::parse($rangeData['end']);
                    if ($date->between($startDate, $endDate)) {
                        $weekCounts[$week]++;
                        break;
                    }
                }
            }

            $labels = [];
            $counts = [];
            foreach ($weekCounts as $week => $count) {
                $labels[] = 'Week ' . $week;
                $counts[] = $count;
            }

            return view('queue.statistics', [
                'role' => 'staff',
                'labels' => $labels,
                'counts' => $counts,
                'range' => $range,
                'weekCounts' => $weekCounts,
                'weekRanges' => $weekRanges,
                'selectedMonth' => $selectedMonth
            ]);
        }

        if ($user->isAdmin()) {
            $data = DB::table('visitors')
                ->join('offices', 'visitors.office_id', '=', 'offices.id')
                ->selectRaw('offices.name as office, COUNT(visitors.id) as total')
                ->whereBetween('visitors.created_at', [$start, $end])
                ->groupBy('offices.name')
                ->orderByDesc('total')
                ->get();

            return view('queue.statistics', [
                'role' => 'admin',
                'officeLabels' => $data->pluck('office'),
                'officeCounts' => $data->pluck('total'),
                'officeData' => $data,
                'range' => $range,
                'selectedMonth' => $selectedMonth
            ]);
        }

        abort(403);
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
