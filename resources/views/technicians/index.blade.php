@extends('layouts.app')
@section('title', __('Technicians'))
@section('page-heading', __('Technicians'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">{{ __('Technicians') }}</h3>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('technicians.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('Add Technician') }}</a>
    @endif
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('technicians.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-7 col-xl-8">
                <label class="form-label" for="technician-search">{{ __('Search') }}</label>
                <input id="technician-search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Name, email, phone, or specialization') }}">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label class="form-label" for="technician-status-filter">{{ __('Status') }}</label>
                <select id="technician-status-filter" name="status" class="form-select">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach(['Active', 'Inactive'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 col-xl-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>{{ __('Filter') }}</button>
                <a href="{{ route('technicians.index') }}" class="btn btn-outline-secondary" aria-label="{{ __('Clear filters') }}" title="{{ __('Clear filters') }}"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Specialization') }}</th><th>{{ __('Status') }}</th>@if(auth()->user()->isAdmin())<th class="text-end">{{ __('Actions') }}</th>@endif</tr>
            </thead>
            <tbody>
            @forelse($technicians as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->email }}</td>
                    <td>{{ $t->phone ?: '-' }}</td>
                    <td>{{ $t->specialization ?: '-' }}</td>
                    <td><span class="badge text-bg-{{ $t->status==='Active'?'success':'secondary' }}">{{ __($t->status) }}</span></td>
                    @if(auth()->user()->isAdmin())
                        <td class="text-end">
                            <form method="POST" action="{{ route('technicians.toggle-status', $t) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $t->status === 'Active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ __($t->status === 'Active' ? 'Deactivate' : 'Activate') }}
                                </button>
                            </form>
                            <a href="{{ route('technicians.edit',$t) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form id="delete-form-{{ $t->id }}" class="d-inline" method="POST" action="{{ route('technicians.destroy',$t) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $t->id }}, '{{ addslashes($t->name) }}')">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center py-4">{{ __('No technicians found.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $technicians->links() }}</div>
</div>

@if(auth()->user()->isAdmin())
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete technician') }} <strong id="deleteTechnicianName"></strong>{{ __('? This action cannot be undone.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    let activeFormId = null;

    function confirmDelete(technicianId, technicianName) {
        activeFormId = 'delete-form-' + technicianId;
        document.getElementById('deleteTechnicianName').textContent = technicianName;
        
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (activeFormId) {
            document.getElementById(activeFormId).submit();
        }
    });
</script>
@endif
@endsection