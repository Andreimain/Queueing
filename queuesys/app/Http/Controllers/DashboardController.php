<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function liveData()
    {
        $offices = Office::all()->map(function ($office) {
            $currentServing = Visitor::where('office_id', $office->id)
                ->where('status', 'serving')
                ->orderBy('updated_at', 'desc')
                ->first();
            $waitingCount = Visitor::where('office_id', $office->id)
                ->where('status', 'waiting')
                ->count();
            return [
                'name' => $office->name,
                'current_serving' => $currentServing ? $currentServing->id_number : '—',
                'waiting_count' => $waitingCount,
            ];
        });

        return response()->json([
            'totalOffices' => Office::count(),
            'totalStaff' => User::where('role', 'staff')->count(),
            'visitorsToday' => Visitor::whereDate('created_at', today())->count(),
            'activeQueues' => Office::has('visitors')->count(),
            'offices' => $offices,
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
            ->orderBy('updated_at', 'desc')
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
            'current_serving' => $currentServing ? $currentServing->id_number : '—',
            'waiting_count' => $waitingCount,
            'skipped_count' => $skippedCount,
        ]);
    }

}
