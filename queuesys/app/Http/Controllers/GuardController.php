<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                'id_number',
                'photo_path',
                'office_id',
                'other_office',
                'status',
            ])
            ->with([
                'office:id,name',
            ])
            ->where('type', 'visitor')
            ->whereIn('status', ['waiting', 'serving'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'ticket_number' => $visitor->ticket_number,
                    'id_number' => $visitor->id_number,
                    'photo' => $visitor->photo_path
                        ? route('guard.visitors.photo', $visitor)
                        : null,
                    'office' => $visitor->office?->name
                        ?? $visitor->other_office
                        ?? 'N/A',
                    'status' => $visitor->status,
                ];
            })
            ->values();

        return response()->json($visitors);
    }

    public function photo(Visitor $visitor)
    {
        abort_unless(
            $visitor->type === 'visitor',
            404
        );

        abort_unless(
            $visitor->photo_path &&
                Storage::exists($visitor->photo_path),
            404
        );

        return Storage::response(
            $visitor->photo_path
        );
    }
}
