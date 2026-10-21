@extends('layouts.app')
@section('title', __('Dashboard'))
@section('page-heading', __('Dashboard'))

@section('content')
<div class="row g-3 mb-4">
    @foreach([
        ['Users',$usersCount,'bi-people','primary'],
        ['Technicians',$techniciansCount,'bi-person-gear','info'],
        ['Total Tickets',$ticketsCount,'bi-ticket-detailed','warning'],
        ['Open / Working',$openCount,'bi-hourglass-split','danger'],
        ['Resolved',$resolvedCount,'bi-check-circle','success'],
        ['Overdue',$overdueCount,'bi-calendar-x','dark'],
        ['Categories',$categoriesCount,'bi-tags','secondary'],
    ] as [$label,$value,$icon,$color])
    @if($label === 'Users' && auth()->check() && auth()->user()->isStaff())
        @continue
    @endif
    <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><div class="text-muted small">{{ __($label) }}</div><div class="fs-3 fw-bold">{{ $value }}</div></div>
                    <div class="stat-icon text-bg-{{ $color }} bg-opacity-10 text-{{ $color }}"><i class="bi {{ $icon }}"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card table-card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1 fw-bold">{{ __('Recent Tickets') }}</h5>
            <div class="small text-muted">{{ __('Latest support requests') }}</div>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>#</th><th>{{ __('Title') }}</th><th>{{ __('User') }}</th><th>{{ __('Category') }}</th><th>{{ __('Priority') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
            <tbody>
            @forelse($recentTickets as $ticket)
                <tr>
                    <td>#{{ $ticket->id }}</td>
                    <td class="fw-semibold">{{ $ticket->title }}</td>
                    <td>{{ $ticket->user->name }}</td>
                    <td>{{ $ticket->category->name }}</td>
                    <td><span class="badge text-bg-{{ in_array($ticket->priority,['High','Critical']) ? 'danger' : ($ticket->priority === 'Medium' ? 'warning' : 'success') }}">{{ __($ticket->priority) }}</span></td>
                    <td><span class="badge text-bg-{{ $ticket->status === 'Resolved' ? 'success' : ($ticket->status === 'Closed' ? 'secondary' : ($ticket->status === 'In Progress' ? 'info' : 'warning')) }}">{{ __($ticket->status) }}</span></td>
                    <td><a href="{{ route('tickets.show',$ticket) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No tickets yet.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
