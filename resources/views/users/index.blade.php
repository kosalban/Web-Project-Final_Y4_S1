@extends('layouts.app')
@section('title', __('Users'))
@section('page-heading', __('Users'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">{{ __('Users') }}</h3>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('Add User') }}</a>
</div>

<div class="card table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Role') }}</th><th>{{ __('Created') }}</th><th class="text-end">{{ __('Actions') }}</th></tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge text-bg-{{ $user->role==='Admin'?'primary':'secondary' }}">{{ __($user->role) }}</span></td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td class="text-end">
                        <a href="{{ route('users.edit',$user) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                        
                        <form id="delete-form-{{ $user->id }}" class="d-inline" method="POST" action="{{ route('users.destroy',$user) }}">
                            @csrf 
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">{{ __('No users found.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-content-body p-3">
                {{ __('Are you sure you want to delete') }} <strong id="deleteUserName"></strong>{{ __('? This action cannot be undone.') }}
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

    function confirmDelete(userId, userName) {
        activeFormId = 'delete-form-' + userId;
        document.getElementById('deleteUserName').textContent = userName;
        
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (activeFormId) {
            document.getElementById(activeFormId).submit();
        }
    });
</script>
@endsection