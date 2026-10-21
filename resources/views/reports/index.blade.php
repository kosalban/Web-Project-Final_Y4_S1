@extends('layouts.app')
@section('title', __('Reports'))
@section('page-heading', __('Reports'))
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h3 class="fw-bold text-dark fs-3 mb-0">{{ __('Ticket Reports') }}</h3>
    <a href="{{ route('reports.export', array_filter($filters)) }}" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i>{{ __('Export CSV') }}</a>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-sm-5 col-lg-4">
                <label for="from" class="form-label">{{ __('Created from') }}</label>
                <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
                @error('from')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-sm-5 col-lg-4">
                <label for="to" class="form-label">{{ __('Created to') }}</label>
                <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
                @error('to')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-sm-2 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
                @if(array_filter($filters))
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
                @endif
            </div>
        </form>
    </div>
</div>
<div class="row g-3 mb-4">
    @foreach($summary as $label => $total)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __($label) }}</div>
                    <div class="fs-3 fw-bold text-dark">{{ $total }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white fw-semibold rounded-top-3 py-3 fs-5">
                {{ __('Tickets by Status') }}
            </div>
            <div class="card-body p-4">
                @forelse($byStatus as $status => $total)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3 fs-6">
                        <span class="text-secondary fw-medium">{{ __($status) }}</span>
                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center my-3">{{ __('No data available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-dark text-white fw-semibold rounded-top-3 py-3 fs-5">
                {{ __('Tickets by Priority') }}
            </div>
            <div class="card-body p-4">
                @forelse($byPriority as $priority => $total)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3 fs-6">
                        <span class="text-secondary fw-medium">{{ __($priority) }}</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center my-3">{{ __('No data available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-success text-white fw-semibold rounded-top-3 py-3 fs-5">
                {{ __('Tickets by Category') }}
            </div>
            <div class="card-body p-4">
                @forelse($byCategory as $category)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3 fs-6">
                        <span class="text-secondary fw-medium">{{ $category->name }}</span>
                        <span class="badge bg-success rounded-pill px-3 py-2 fs-6">{{ $category->tickets_count }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center my-3">{{ __('No categories available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-info text-dark fw-semibold rounded-top-3 py-3 fs-5">
                {{ __('Tickets by Technician') }}
            </div>
            <div class="card-body p-4">
                @forelse($byTechnician as $technician)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3 fs-6">
                        <span class="text-secondary fw-medium">{{ $technician->name }}</span>
                        <span class="badge bg-info text-dark rounded-pill px-3 py-2 fs-6">{{ $technician->total }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center my-3">{{ __('No tickets available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection