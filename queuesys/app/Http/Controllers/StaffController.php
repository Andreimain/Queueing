<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Show staff list + registration form
     */
    public function index()
    {
        $staff = User::with(['office', 'courses'])->paginate(7);
        $offices = Office::all();
        $courses = Course::orderBy('name')->get();

        return view('staff.index', compact('staff', 'offices', 'courses'));
    }


    /**
     * Store a newly created staff user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|unique:users',
            'password'  => 'required|string|min:6',
            'office_id' => 'required|exists:offices,id',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $staff = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role' => 'staff',
            'office_id' => $request->office_id,
        ]);

        if ($staff->office?->abbreviation === 'RO') {
            $staff->courses()->sync($request->input('courses', []));
        }

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff registered successfully.');
    }

    /**
     * Show the form to edit a staff member
     */
    public function edit($id)
    {
        $staff = User::with('courses')->findOrFail($id);
        $offices = Office::all();
        $courses = Course::orderBy('name')->get();

        return view('staff.edit_staff', compact('staff', 'offices', 'courses'));
    }

    /**
     * Update staff info
     */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:staff,admin',
            'office_id' => 'nullable|exists:offices,id',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $staff->name  = $request->name;
        $staff->email = $request->email;
        $staff->role  = $request->role;

        if ($request->role === 'admin') {
            $staff->office_id = null;
        } else {
            $staff->office_id = $request->office_id;
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();
        $staff->load('office');

        if (
            $staff->role === 'staff' &&
            $staff->office?->abbreviation === 'RO'
        ) {
            $staff->courses()->sync($request->input('courses', []));
        } else {
            $staff->courses()->detach();
        }
        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff updated successfully.');
    }

    /**
     * Delete a staff member
     */
    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff deleted successfully.');
    }
}
