<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;

class OfficeController extends Controller
{
    // Show create office form
    public function create()
    {
        return view('office_create');
    }

    // Store new office
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:offices,name',
        ]);

        Office::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Office created successfully!');
    }
}
