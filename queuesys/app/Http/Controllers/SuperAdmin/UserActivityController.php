<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('office')
            ->whereNull('deleted_at');

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by office
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->input('office_id'));
        }

        $users = $query
            ->orderByRaw('last_activity_at IS NULL ASC')
            ->orderByDesc('last_activity_at')
            ->paginate(15)
            ->withQueryString();

        $now = now();

        $users->getCollection()->transform(function (User $user) use ($now) {
            $lastActivity = $user->last_activity_at;

            if (!$lastActivity) {
                $user->activity_status = 'offline';
            } elseif ($lastActivity->gte($now->copy()->subMinutes(2))) {
                $user->activity_status = 'online';
            } elseif ($lastActivity->gte($now->copy()->subMinutes(10))) {
                $user->activity_status = 'idle';
            } else {
                $user->activity_status = 'offline';
            }

            return $user;
        });

        $offices = Office::orderBy('name')->get();

        $roles = [
            'super_admin',
            'admin',
            'head',
            'staff',
            'guard',
        ];

        $counts = [
            'online' => User::whereNotNull('last_activity_at')
                ->where('last_activity_at', '>=', now()->subMinutes(2))
                ->count(),

            'idle' => User::where('last_activity_at', '<', now()->subMinutes(2))
                ->where('last_activity_at', '>=', now()->subMinutes(10))
                ->count(),

            'offline' => User::whereNull('last_activity_at')
                ->orWhere('last_activity_at', '<', now()->subMinutes(10))
                ->count(),
        ];

        return view('super-admin.user-activity', compact(
            'users',
            'offices',
            'roles',
            'counts'
        ));
    }
}
