<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Show the visitor registration form.
     */
    public function create()
    {
        $offices = [
            'Business Office',
            'Library',
            'Student Affairs',
            'Registrar',
        ];

        return view('register', compact('offices'));
    }

    /**
     * Store the visitor registration form data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'contact'    => 'required|string|max:15',
            'email'      => 'nullable|email',
            'office'     => 'required|string',
        ]);

        $today = now()->toDateString();

        // Get the latest queue number for today's date and selected office
        $maxQueueNumber = Visitor::where('office', $request->office)
            ->whereDate('created_at', $today)
            ->max('queue_number');

        $nextNumber = $maxQueueNumber ? $maxQueueNumber + 1 : 1;

        Visitor::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'contact'      => $request->contact,
            'email'        => $request->email,
            'office'       => $request->office,
            'queue_number' => $nextNumber,
            'status'       => 'waiting',
        ]);

        return redirect()->back()->with('success', 'You have been added to the queue.');
    }

}
