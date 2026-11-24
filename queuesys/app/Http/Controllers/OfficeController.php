<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->isSuperAdmin(),
            403,
            'Only Super Admin can delete or restore offices.'
        );
    }

    public function create()
    {
        $tab = request('tab', 'active');

        if ($tab === 'deleted') {
            // Only Super Admin can view deleted offices.
            $this->authorizeSuperAdmin();

            $offices = Office::onlyTrashed()
                ->orderBy('name')
                ->get();
        } else {
            $tab = 'active';

            $offices = Office::orderBy('name')
                ->get();
        }

        return view('office_create', compact('offices', 'tab'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'abbreviation' => strtoupper(
                trim($request->abbreviation ?? '')
            ),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:offices,name',
            ],
            'abbreviation' => [
                'required',
                'string',
                'max:10',
                'unique:offices,abbreviation',
            ],
        ]);

        Office::create([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
        ]);

        return redirect()
            ->route('admin.offices.create')
            ->with('success', 'Office created successfully!');
    }

    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->merge([
            'abbreviation' => strtoupper(
                trim($request->abbreviation ?? '')
            ),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('offices', 'name')->ignore($office->id),
            ],
            'abbreviation' => [
                'required',
                'string',
                'max:10',
                Rule::unique('offices', 'abbreviation')->ignore($office->id),
            ],
        ]);

        $office->update([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
        ]);

        return redirect()
            ->route('admin.offices.create')
            ->with('success', 'Office updated successfully!');
    }

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        $office = Office::findOrFail($id);

        // Prevent deletion while users are assigned to this office.
        if ($office->users()->exists()) {
            return redirect()
                ->route('admin.offices.create')
                ->with(
                    'error',
                    'Reassign the users in this office before deleting it.'
                );
        }

        $office->delete();

        return redirect()
            ->route('admin.offices.create')
            ->with('success', 'Office moved to Deleted successfully!');
    }

    public function restore($id)
    {
        $this->authorizeSuperAdmin();

        $office = Office::onlyTrashed()->findOrFail($id);

        $office->restore();

        return redirect()
            ->route('admin.offices.create', ['tab' => 'deleted'])
            ->with('success', 'Office restored successfully!');
    }
}
