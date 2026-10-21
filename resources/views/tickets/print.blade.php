@extends('layouts.app')
@section('title', __('Print Ticket').' #'.$ticket->id)
@section('page-heading', __('Print Ticket'))
@section('content')

<style>
    /* Modern UI Card Styling for Screen */
    .ticket-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        background: #fff;
    }

    .shop-header {
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }

    .meta-table td {
        padding: 0.5rem 0.25rem;
        vertical-align: top;
    }

    .section-title {
        border-left: 4px solid #0d6efd;
        padding-left: 8px;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #495057;
    }

    /* Clean Print Stylesheet */
    @media print {
        body { 
            background: #fff !important; 
            font-size: 11pt !important;
            color: #000 !important;
        }
        .sidebar, .topbar, .no-print, nav, footer { 
            display: none !important; 
        }
        .main, main, container, .container-fluid { 
            width: 100% !important; 
            margin: 0 !important; 
            padding: 0 !important; 
        }
        .ticket-card { 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important;
        }
        .shop-header {
            border-bottom: 2px solid #000 !important;
        }
        .section-title {
            border-left-color: #000 !important;
            color: #000 !important;
        }
        a { 
            color: inherit !important; 
            text-decoration: none !important; 
        }
        .badge {
            border: 1px solid #000 !important;
            color: #000 !important;
            background: transparent !important;
        }
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }
        @page {
            size: A4;
            margin: 1.2cm;
        }
    }
</style>

<!-- Top Action Bar (Hidden on Print) -->
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h4 class="fw-bold mb-0">Ticket #{{ $ticket->id }}</h4>
        <small class="text-muted">{{ __('Kosal-IT-Room Ticket Printout') }}</small>
    </div>
    <button type="button" class="btn btn-primary shadow-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> {{ __('Print / Save as PDF') }}
    </button>
</div>

<!-- Ticket Document -->
<div class="card ticket-card">
    <div class="card-body p-4 p-md-5">
        
        <!-- Header / Branding -->
        <div class="shop-header d-flex justify-content-between align-items-start">
            <div>
                <h2 class="h3 fw-bold text-primary mb-0 text-uppercase" style="letter-spacing: 1px;">Kosal-IT-Room</h2>
                <span class="text-muted small">{{ __('Support Hardware & Software') }}</span>
            </div>
            <div class="text-end">
                <h3 class="h5 fw-bold mb-0">{{ __('Ticket #') }}{{ $ticket->id }}</h3>
                <small class="text-muted">{{ __('Printed:') }} {{ now()->format('Y-m-d H:i') }}</small>
            </div>
        </div>

        <!-- Ticket Title & Main Details -->
        <div class="row align-items-center mb-4 pb-3 border-bottom">
            <div class="col-8">
                <h1 class="h4 fw-bold mb-1">{{ $ticket->title }}</h1>
                <div class="text-muted">{{ __('Category') }}: {{ $ticket->category->name ?? __('General') }}</div>
            </div>
            <div class="col-4 text-end">
                <span class="badge bg-primary fs-6 mb-1">{{ __($ticket->status) }}</span>
                <div class="small text-uppercase fw-semibold text-muted">{{ __($ticket->priority) }} {{ __('Priority') }}</div>
            </div>
        </div>

        <!-- Meta Details Table Layout -->
        <div class="row g-3 mb-3 bg-light p-3 rounded page-break-inside-avoid" style="border: 1px solid #edf2f7;">
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Requester') }}</span>
                <strong>{{ $ticket->user->name }}</strong>
            </div>
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Department') }}</span>
                <strong>{{ $ticket->department ?: '—' }}</strong>
            </div>
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Device / Asset') }}</span>
                <strong>{{ $ticket->asset ?: '—' }}</strong>
            </div>
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Assigned Technician') }}</span>
                <strong>{{ $ticket->technician->name ?? __('Unassigned') }}</strong>
            </div>
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Created Date') }}</span>
                <strong>{{ $ticket->created_at->format('Y-m-d H:i') }}</strong>
            </div>
            <div class="col-4">
                <span class="text-muted d-block small text-uppercase">{{ __('Due Date') }}</span>
                <strong>{{ $ticket->due_date?->format('Y-m-d') ?? '—' }}</strong>
            </div>
        </div>

        <!-- Description -->
        <div class="section-title">{{ __('Issue Description') }}</div>
        <div class="p-2 mb-3" style="white-space: pre-line; text-align: justify;">{{ $ticket->description }}</div>

        <!-- Resolution & Root Cause -->
        @if($ticket->resolution)
            <div class="section-title">{{ __('Resolution') }}</div>
            <div class="p-2 mb-3" style="white-space: pre-line; text-align: justify;">{{ $ticket->resolution }}</div>
        @endif

        @if($ticket->root_cause)
            <div class="section-title">{{ __('Root Cause') }}</div>
            <div class="p-2 mb-3" style="white-space: pre-line; text-align: justify;">{{ $ticket->root_cause }}</div>
        @endif

        @if($ticket->attachment_name)
            <div class="mb-3 small">
                <strong>{{ __('Attachment') }}:</strong> <span class="text-muted">{{ $ticket->attachment_name }}</span>
            </div>
        @endif

        <!-- Ticket History / Comments -->
        <div class="section-title mt-4">{{ __('Ticket History & Activity Log') }}</div>
        <div class="timeline ps-2">
            @forelse($ticket->comments as $comment)
                <div class="mb-3 pb-2 border-bottom page-break-inside-avoid">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <strong class="text-dark">{{ $comment->user->name }}</strong>
                        <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div style="white-space: pre-line;">{{ $comment->comment }}</div>
                </div>
            @empty
                <p class="text-muted small fst-italic">{{ __('No history entries recorded.') }}</p>
            @endforelse
        </div>

        <!-- Print Footer Sign-off Block -->
        <div class="row mt-5 pt-4 text-center page-break-inside-avoid">
            <div class="col-6">
                <div style="border-top: 1px dashed #000; width: 70%; margin: 0 auto; padding-top: 5px;" class="small fw-bold">
                    {{ __('Requester Signature') }}
                </div>
            </div>
            <div class="col-6">
                <div style="border-top: 1px dashed #000; width: 70%; margin: 0 auto; padding-top: 5px;" class="small fw-bold">
                    {{ __('Technician Signature') }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection