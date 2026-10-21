@extends('layouts.app')
@section('title', __('Categories'))
@section('page-heading', __('Categories'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">{{ __('Categories') }}</h3>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('Add Category') }}</a>
    @endif
</div>

<div class="card table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>{{ __('Name') }}</th><th>{{ __('Description') }}</th><th>{{ __('Status') }}</th>@if(auth()->user()->isAdmin())<th class="text-end">{{ __('Actions') }}</th>@endif</tr>
            </thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->name }}</td>
                    <td>{{ $c->description ?: '-' }}</td>
                    <td><span class="badge text-bg-{{ $c->status==='Active'?'success':'secondary' }}">{{ __($c->status) }}</span></td>
                    @if(auth()->user()->isAdmin())
                        <td class="text-end">
                            <a href="{{ route('categories.edit',$c) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form id="delete-form-{{ $c->id }}" class="d-inline" method="POST" action="{{ route('categories.destroy',$c) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $c->id }}, '{{ addslashes($c->name) }}')">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ auth()->user()->isAdmin() ? 4 : 3 }}" class="text-center py-4">{{ __('No categories found.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $categories->links() }}</div>
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
                {{ __('Are you sure you want to delete category') }} <strong id="deleteCategoryName"></strong>{{ __('? This action cannot be undone.') }}
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

    function confirmDelete(categoryId, categoryName) {
        activeFormId = 'delete-form-' + categoryId;
        document.getElementById('deleteCategoryName').textContent = categoryName;
        
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