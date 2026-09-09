<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isHead()) {
            $staff = User::with(['office', 'courses'])
                ->where('office_id', $user->office_id)
                ->where('role', 'staff')
                ->paginate(7);

            $offices = Office::where('id', $user->office_id)->get();
        } else {
            $staff = User::with(['office', 'courses'])->paginate(8);
            $offices = Office::all();
        }

        $courses = Course::orderBy('name')->get();

        return view('staff.index', compact('staff', 'offices', 'courses'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:staff,head,guard,admin',
            'office_id' => 'nullable|exists:offices,id',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        if ($user->isHead()) {
            if ($request->role !== 'staff') {
                abort(403);
            }

            if ((int) $request->office_id !== (int) $user->office_id) {
                abort(403);
            }
        }

        if (in_array($request->role, ['guard', 'admin'])) {
            $officeId = null;
        } else {
            if (!$request->office_id) {
                abort(422, 'Office is required for Staff and Head users.');
            }

            $officeId = $request->office_id;
        }

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'office_id' => $officeId,
        ]);

        $staff->load('office');

        if (
            $staff->role === 'staff' &&
            $staff->office?->abbreviation === 'RO'
        ) {
            $staff->courses()->sync($request->input('courses', []));
        }

        return redirect()
            ->route('staff.index')
            ->with('success', 'User registered successfully.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $staff = User::with('courses')->findOrFail($id);

        if ($user->isHead()) {
            if (
                $staff->role !== 'staff' ||
                (int) $staff->office_id !== (int) $user->office_id
            ) {
                abort(403);
            }

            $offices = Office::where('id', $user->office_id)->get();
        } else {
            $offices = Office::all();
        }

        $courses = Course::orderBy('name')->get();

        return view('staff.edit_staff', compact('staff', 'offices', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $staff = User::findOrFail($id);

        if ($user->isHead()) {
            if (
                $staff->role !== 'staff' ||
                (int) $staff->office_id !== (int) $user->office_id
            ) {
                abort(403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email,' . $staff->id,
                'password' => 'nullable|string|min:6',
                'office_id' => 'required|exists:offices,id',
                'courses' => 'nullable|array',
                'courses.*' => 'exists:courses,id',
            ]);

            if ((int) $request->office_id !== (int) $user->office_id) {
                abort(403);
            }

            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->role = 'staff';
            $staff->office_id = $user->office_id;
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email,' . $staff->id,
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:staff,head,guard,admin',
                'office_id' => 'nullable|exists:offices,id',
                'courses' => 'nullable|array',
                'courses.*' => 'exists:courses,id',
            ]);

            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->role = $request->role;

            if (in_array($request->role, ['guard', 'admin'])) {
                $staff->office_id = null;
            } else {
                if (!$request->office_id) {
                    abort(422, 'Office is required for Staff and Head users.');
                }

                $staff->office_id = $request->office_id;
            }
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
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $staff = User::findOrFail($id);

        if ($user->isHead()) {
            if (
                $staff->role !== 'staff' ||
                (int) $staff->office_id !== (int) $user->office_id
            ) {
                abort(403);
            }
        }

        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'User deleted successfully.');
    }
}
