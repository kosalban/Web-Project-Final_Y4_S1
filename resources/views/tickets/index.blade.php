@extends('layouts.app')
@section('title', __('Tickets'))
@section('page-heading', __('Tickets'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">{{ __('Tickets') }}</h3>
    @can('create-tickets')
        <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ __('Create Ticket') }}</a>
    @endcan
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tickets.index') }}" class="row g-2 align-items-end">
            <!-- Find by Dropdown -->
            <div class="col-12 col-md-3 col-xl-2">
                <label class="form-label text-muted small mb-1" for="filter-by">{{ __('Find by') }}</label>
                <select id="filter-by" name="filter_by" class="form-select border-primary-subtle">
                    <option value="title" @selected(request('filter_by') === 'title')>{{ __('Search by ticket title...') }}</option>
                    <option value="id" @selected(request('filter_by') === 'id')>{{ __('Search by ticket ID...') }}</option>
                </select>
            </div>

            <!-- Search Input with Search Icon -->
            <div class="col-12 col-md-4 col-xl-4">
                <label class="form-label text-muted small mb-1" for="ticket-search">{{ __('Search term') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input id="ticket-search" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search by ticket title...') }}">
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="col-6 col-md-2 col-xl-2">
                <label class="form-label text-muted small mb-1" for="status-filter">{{ __('Status') }}</label>
                <select id="status-filter" name="status" class="form-select">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach(['Open','In Progress','Resolved','Closed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2 col-xl-2">
                <label class="form-label text-muted small mb-1" for="priority-filter">{{ __('Priority') }}</label>
                <select id="priority-filter" name="priority" class="form-select">
                    <option value="">{{ __('All priorities') }}</option>
                    @foreach(['Low','Medium','High','Critical'] as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ __($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Search & Reset Action Buttons matching the design -->
            <div class="col-12 col-md-auto d-flex gap-2 ms-auto mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">{{ __('Search') }}</button>
                <a href="{{ route('tickets.index') }}" class="btn btn-danger px-4 fw-semibold text-decoration-none">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Technician') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>#{{ $t->id }}</td>
                        <td class="fw-semibold">{{ $t->title }}</td>
                        <td>{{ $t->user->name }}</td>
                        <td>{{ $t->technician->name ?? __('Unassigned') }}</td>
                        <td>{{ $t->category->name }}</td>
                        <td><span class="badge text-bg-{{ in_array($t->priority,['High','Critical'])?'danger':($t->priority==='Medium'?'warning':'success') }}">{{ __($t->priority) }}</span></td>
                        <td><span class="badge text-bg-{{ $t->status==='Resolved'?'success':($t->status==='Closed'?'secondary':($t->status==='In Progress'?'info':'warning')) }}">{{ __($t->status) }}</span></td>
                        <td><a href="{{ route('tickets.show',$t) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">{{ __('No tickets found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $tickets->links() }}</div>
</div>
@endsection