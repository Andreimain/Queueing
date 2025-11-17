<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $totalOffices = Office::count();
        $totalStaff = User::where('role', 'staff')->count();
        $visitorsToday = Visitor::whereDate('created_at', $today)->count();
        $activeQueues = Office::whereHas('visitors', function ($q) use ($today) {
            $q->whereDate('created_at', $today)
              ->where('status', 'waiting');
        })->count();

        $offices = Office::withCount(['visitors as waiting_count' => function($q) use ($today){
            $q->whereDate('created_at', $today)->where('status', 'waiting');
        }])->get();

        return view('dashboard', compact('totalOffices', 'totalStaff', 'visitorsToday', 'activeQueues', 'offices'));
    }
    public function liveData()
    {
        $today = now()->toDateString();

        $offices = Office::withCount(['visitors as waiting_count' => function($q) use ($today){
            $q->whereDate('created_at', $today)->where('status', 'waiting');
        }])->get();

        return response()->json([
            'totalOffices' => Office::count(),
            'totalStaff' => User::where('role', 'staff')->count(),
            'visitorsToday' => Visitor::whereDate('created_at', $today)->count(),
            'activeQueues' => Office::whereHas('visitors', function ($q) use ($today) {
                $q->whereDate('created_at', $today)->where('status', 'waiting');
            })->count(),
            'offices' => $offices->map(function($office) {
                return [
                    'name' => $office->name,
                    'waiting_count' => $office->waiting_count,
                ];
            }),
        ]);
    }
    public function staffData()
    {
        $office = Auth::user()->office;

        if (!$office) {
            return response()->json(['error' => 'No office assigned'], 404);
        }

        $today = now()->toDateString();

        $currentServing = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->latest('updated_at')
            ->first();

        $waitingCount = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->count();

        $skippedCount = Visitor::where('office_id', $office->id)
            ->whereDate('created_at', $today)
            ->where('status', 'skipped')
            ->count();

        return response()->json([
            'current_serving' => $currentServing ? $currentServing->ticket_number : '—',
            'waiting_count' => $waitingCount,
            'skipped_count' => $skippedCount,
        ]);
    }
}
