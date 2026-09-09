<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class GuardController extends Controller
{
    public function index()
    {
        $visitors = Visitor::with(['office', 'cashier'])
            ->where('type', 'visitor')
            ->whereIn('status', ['waiting', 'serving'])
            ->orderBy('created_at')
            ->get();

        return view('guard.visitors', compact('visitors'));
    }

    public function history(Request $request)
    {
        $query = Visitor::with(['office', 'cashier'])
            ->where('type', 'visitor')
            ->where(function ($q) {
                $q->whereIn('status', ['done', 'skipped', 'transferred'])
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id')
                            ->whereNotNull('other_office');
                    });
            });

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {

                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('other_office', 'like', "%{$search}%")

                    ->orWhereHas('cashier', function ($cashier) use ($search) {
                        $cashier->where('name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('office', function ($office) use ($search) {
                        $office->where('name', 'like', "%{$search}%");
                    });

                if (strtotime($search)) {
                    $q->orWhereDate('created_at', $search);
                }
            });
        }

        $visitors = $query
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('guard.history', compact('visitors'));
    }

    public function data()
    {
        $visitors = Visitor::query()
            ->select([
                'id',
                'ticket_number',
                'name',
                'office_id',
                'cashier_id',
                'status',
            ])
            ->with([
                'office:id,name',
                'cashier:id,name',
            ])
            ->where('type', 'visitor')
            ->whereIn('status', ['waiting', 'serving'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'ticket_number' => $visitor->ticket_number,
                    'name' => $visitor->name,
                    'office' => $visitor->office?->name,
                    'cashier' => $visitor->cashier?->name,
                    'status' => $visitor->status,
                ];
            })
            ->values();

        return response()->json($visitors);
    }
}
