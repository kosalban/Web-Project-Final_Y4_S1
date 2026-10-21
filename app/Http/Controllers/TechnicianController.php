<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', 'in:Active,Inactive'],
        ]);

        $technicians = Technician::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        Technician::create($request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:150|unique:technicians,email',
            'phone' => 'nullable|max:30',
            'specialization' => 'nullable|max:150',
            'status' => 'required|in:Active,Inactive',
        ]));

        return redirect()->route('technicians.index')->with('success', __('Technician created.'));
    }

    public function edit(Technician $technician)
    {
        return view('technicians.edit', compact('technician'));
    }

    public function update(Request $request, Technician $technician)
    {
        $technician->update($request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:150|unique:technicians,email,'.$technician->id,
            'phone' => 'nullable|max:30',
            'specialization' => 'nullable|max:150',
            'status' => 'required|in:Active,Inactive',
        ]));

        return redirect()->route('technicians.index')->with('success', __('Technician updated.'));
    }

    public function destroy(Technician $technician)
    {
        if ($technician->tickets()->exists()) {
            return back()->with('error', __('This technician is assigned to tickets and cannot be deleted.'));
        }

        $technician->delete();

        return back()->with('success', __('Technician deleted.'));
    }

    public function toggleStatus(Technician $technician)
    {
        $newStatus = $technician->status === 'Active' ? 'Inactive' : 'Active';
        $technician->update(['status' => $newStatus]);

        return back()->with('success', __('Technician status updated to :status.', ['status' => __($newStatus)]));
    }
}
