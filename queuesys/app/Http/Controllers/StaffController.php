<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $tab = $request->query('tab') === 'deleted' && $user->isSuperAdmin()
            ? 'deleted'
            : 'active';

        if ($user->isHead()) {
            $staff = User::with(['office', 'courses'])
                ->where('office_id', $user->office_id)
                ->where('role', 'staff')
                ->paginate(7)
                ->withQueryString();

            $offices = Office::where('id', $user->office_id)->get();
        } elseif ($user->isSuperAdmin()) {
            if ($tab === 'deleted') {
                $staff = User::onlyTrashed()
                    ->with(['office', 'courses'])
                    ->where('role', '!=', 'super_admin')
                    ->orderByDesc('deleted_at')
                    ->paginate(8)
                    ->withQueryString();
            } else {
                $staff = User::with(['office', 'courses'])
                    ->where('role', '!=', 'super_admin')
                    ->paginate(8)
                    ->withQueryString();
            }

            $offices = Office::all();
        } elseif ($user->isAdmin()) {
            $staff = User::with(['office', 'courses'])
                ->where('id', '!=', $user->id)
                ->paginate(8)
                ->withQueryString();

            $offices = Office::all();
        } else {
            abort(403);
        }

        $courses = Course::orderBy('name')->get();

        return view('staff.index', compact(
            'staff',
            'offices',
            'courses',
            'tab'
        ));
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
            'handle' => [
                'nullable',
                Rule::in(['Enrollment', 'Document Request']),
                Rule::requiredIf(function () use ($request) {
                    return $request->role === 'staff'
                        && Office::whereKey($request->office_id)
                            ->whereRaw('UPPER(abbreviation) = ?', ['RO'])
                            ->exists();
                }),
            ],
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

        if (!$user->isAdmin() && !$user->isSuperAdmin() && !$user->isHead()) {
            abort(403);
        }

        if (in_array($request->role, ['guard', 'admin'])) {
            $officeId = null;
        } else {
            if (!$request->office_id) {
                abort(422, 'Office is required for Staff and Head users.');
            }

            $officeId = $request->office_id;
        }

        $office = $officeId ? Office::findOrFail($officeId) : null;
        $isRegistrarStaff = $request->role === 'staff'
            && strtoupper($office?->abbreviation ?? '') === 'RO';

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'office_id' => $officeId,
            'handle' => $isRegistrarStaff ? $request->handle : null,
        ]);

        if ($isRegistrarStaff && $request->handle === 'Enrollment') {
            $staff->courses()->sync($request->input('courses', []));
        } else {
            $staff->courses()->detach();
        }

        return redirect()
            ->route('staff.index')
            ->with('success', 'User registered successfully.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $staff = User::with('courses')->findOrFail($id);

        if ((int) $staff->id === (int) $user->id) {
            abort(403);
        }

        if ($staff->isSuperAdmin()) {
            abort(403);
        }

        if ($user->isHead()) {
            if (
                $staff->role !== 'staff' ||
                (int) $staff->office_id !== (int) $user->office_id
            ) {
                abort(403);
            }

            $offices = Office::where('id', $user->office_id)->get();
        } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
            $offices = Office::all();
        } else {
            abort(403);
        }

        $courses = Course::orderBy('name')->get();

        return view('staff.edit_staff', compact(
            'staff',
            'offices',
            'courses'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $staff = User::findOrFail($id);

        if ((int) $staff->id === (int) $user->id) {
            abort(403);
        }

        if ($staff->isSuperAdmin()) {
            abort(403);
        }

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
                'handle' => [
                    'nullable',
                    Rule::in(['Enrollment', 'Document Request']),
                    Rule::requiredIf(function () use ($request) {
                        return Office::whereKey($request->office_id)
                            ->whereRaw('UPPER(abbreviation) = ?', ['RO'])
                            ->exists();
                    }),
                ],
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
        } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email,' . $staff->id,
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:staff,head,guard,admin',
                'office_id' => 'nullable|exists:offices,id',
                'handle' => [
                    'nullable',
                    Rule::in(['Enrollment', 'Document Request']),
                    Rule::requiredIf(function () use ($request) {
                        return $request->role === 'staff'
                            && Office::whereKey($request->office_id)
                                ->whereRaw('UPPER(abbreviation) = ?', ['RO'])
                                ->exists();
                    }),
                ],
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
        } else {
            abort(403);
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->load('office');

        $isRegistrarStaff = $staff->role === 'staff'
            && strtoupper($staff->office?->abbreviation ?? '') === 'RO';

        $staff->handle = $isRegistrarStaff ? $request->handle : null;
        $staff->save();

        if ($isRegistrarStaff && $request->handle === 'Enrollment') {
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

        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $staff = User::findOrFail($id);

        if (
            (int) $staff->id === (int) $user->id ||
            $staff->isSuperAdmin()
        ) {
            abort(403);
        }

        $staff->delete();

        return redirect()
            ->route('staff.index', ['tab' => 'active'])
            ->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $staff = User::onlyTrashed()->findOrFail($id);

        if ($staff->isSuperAdmin()) {
            abort(403);
        }

        $staff->restore();

        return redirect()
            ->route('staff.index', ['tab' => 'deleted'])
            ->with('success', 'User restored successfully.');
    }
}
