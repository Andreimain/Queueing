<?php

namespace App\Http\Controllers;

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
            'first_name'     => 'required|string|max:50',
            'last_name'      => 'required|string|max:50',
            'contact_number' => 'required|string|max:15',
            'id_number'      => 'required|string|max:50',
            'office_id'      => 'required|exists:offices,id',
            'priority'       => 'nullable|boolean',
        ]);

        $today = now()->toDateString();

        $maxQueueNumber = Visitor::where('office_id', $request->office_id)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $nextNumber = $maxQueueNumber ? $maxQueueNumber + 1 : 1;

        $visitor = Visitor::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'contact_number'  => $request->contact_number,
            'id_number'       => $request->id_number,
            'office_id'       => $request->office_id,
            'queue_number'    => $nextNumber,
            'status'          => 'waiting',
            'priority'        => $request->type === 'visitor' ? (bool) $request->priority : false,
        ]);

        return view('ticket', compact('visitor'));
    }
}
