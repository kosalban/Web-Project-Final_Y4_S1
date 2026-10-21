@extends('layouts.app')
@section('title', __('Ticket #').$ticket->id)
@section('page-heading', __('Ticket Details'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="fw-bold mb-1">{{ __('Ticket #') }}{{ $ticket->id }}</h3>
        <p class="text-muted mb-0">{{ $ticket->title }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tickets.print', $ticket) }}" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> {{ __('Print / PDF') }}</a>
        @can('manage-tickets')
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            <form id="delete-ticket-form" class="d-inline" method="POST" action="{{ route('tickets.destroy', $ticket) }}">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">{{ __('Delete') }}</button>
            </form>
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold">{{ __('Description') }}</h5>
                <p class="text-secondary" style="white-space:pre-line">{{ $ticket->description }}</p>
                <hr>
                
                @if($ticket->attachment_path)
                    <p><strong>{{ __('Attachment') }}:</strong> <a href="{{ route('tickets.attachment', $ticket) }}">{{ $ticket->attachment_name }}</a></p>
                @endif
                
                @if($ticket->resolution)
                    <h5 class="fw-bold mt-4">{{ __('Resolution') }}</h5>
                    <p class="text-secondary" style="white-space:pre-line">{{ $ticket->resolution }}</p>
                @endif
                
                @if($ticket->root_cause)
                    <h5 class="fw-bold mt-4">{{ __('Root Cause') }}</h5>
                    <p class="text-secondary" style="white-space:pre-line">{{ $ticket->root_cause }}</p>
                @endif
                
                <hr>
                <h5 class="fw-bold mb-3">{{ __('Ticket History') }}</h5>
                
                @forelse($ticket->comments as $comment)
                    <div class="border rounded-3 p-3 mb-2">
                        <div class="fw-semibold">{{ $comment->user->name }} <small class="text-muted fw-normal">{{ $comment->created_at->locale(app()->getLocale())->diffForHumans() }}</small></div>
                        <div class="mt-1" style="white-space:pre-line">{{ $comment->comment }}</div>
                    </div>
                @empty
                    <p class="text-muted">{{ __('No comments yet.') }}</p>
                @endforelse

                <hr>
                <h5 class="fw-bold mb-3">{{ __('Activity') }}</h5>
                <div class="border-start border-2 ps-3 ms-1">
                    @forelse($ticket->activities as $activity)
                        <div class="mb-3">
                            <div class="fw-semibold">{{ $activity->user?->name ?? __('System') }} <small class="text-muted fw-normal">{{ $activity->created_at->format('Y-m-d H:i') }}</small></div>
                            <div class="text-secondary">
                                @switch($activity->type)
                                    @case('created')
                                        {{ __('created this ticket') }}
                                        @break
                                    @case('assigned')
                                        {{ __('changed assignment from :from to :to', ['from' => __($activity->details['from'] ?? 'Unassigned'), 'to' => __($activity->details['to'] ?? 'Unassigned')]) }}
                                        @break
                                    @case('status_changed')
                                        {{ __('changed status from :from to :to', ['from' => __($activity->details['from']), 'to' => __($activity->details['to'])]) }}
                                        @break
                                    @case('updated')
                                        {{ __('updated :fields', ['fields' => implode(', ', array_map(fn (string $field): string => __($field), $activity->details['fields'] ?? []))]) }}
                                        @break
                                    @case('commented')
                                        {{ __('added a comment') }}
                                        @break
                                    @default
                                        {{ __('updated this ticket') }}
                                @endswitch
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('No activity recorded yet.') }}</p>
                    @endforelse
                </div>

                @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('tickets.comments.store',$ticket) }}" class="mt-3">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" placeholder="{{ __('Write a comment...') }}" required></textarea>
                        <button class="btn btn-primary">{{ __('Add Comment') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('Ticket Information') }}</h5>
                <table class="table table-sm">
                    <tr><th>{{ __('Requester') }}</th><td>{{ $ticket->user->name }}</td></tr>
                    <tr><th>{{ __('Department') }}</th><td>{{ $ticket->department ?: __('Not set') }}</td></tr>
                    <tr><th>{{ __('Device / Asset') }}</th><td>{{ $ticket->asset ?: __('Not set') }}</td></tr>
                    <tr><th>{{ __('Category') }}</th><td>{{ $ticket->category->name }}</td></tr>
                    <tr><th>{{ __('Technician') }}</th><td>{{ $ticket->technician->name ?? __('Unassigned') }}</td></tr>
                    <tr><th>{{ __('Priority') }}</th><td>{{ __($ticket->priority) }}</td></tr>
                    <tr><th>{{ __('Status') }}</th><td>{{ __($ticket->status) }}</td></tr>
                    <tr><th>{{ __('Due Date') }}</th><td>{{ $ticket->due_date?->format('Y-m-d') ?? __('Not set') }}</td></tr>
                    <tr><th>{{ __('Created') }}</th><td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@can('manage-tickets')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete') }} <strong>{{ __('Ticket #') }}{{ $ticket->id }}</strong>{{ __('? This action cannot be undone.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('Delete Ticket') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        document.getElementById('delete-ticket-form').submit();
    });
</script>
@endcan
@endsection