@extends('layouts.app')
@section('title', __('Edit Ticket').' #'.$ticket->id)
@section('page-heading', __('Edit Ticket'))
@section('content')
<div class="card form-card shadow-sm">
	<div class="card-body p-4">
		<h3 class="fw-bold mb-4">{{ __('Edit Ticket') }} #{{ $ticket->id }}</h3>
		<form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			<div class="row g-3">
				<div class="col-md-8">
					<label for="title" class="form-label">{{ __('Title') }}</label>
					<input id="title" name="title" value="{{ old('title', $ticket->title) }}" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label for="category_id" class="form-label">{{ __('Category') }}</label>
					<select id="category_id" name="category_id" class="form-select" required>
						@foreach($categories as $category)
							<option value="{{ $category->id }}" @selected(old('category_id', $ticket->category_id) == $category->id)>{{ $category->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-6">
					<label for="requester_id" class="form-label">{{ __('Requester / User') }}</label>
					@if(auth()->user()->isAdmin() || auth()->user()->isStaff())
						<select id="requester_id" name="requester_id" class="form-select" required>
							@foreach($requesters as $requester)
								<option value="{{ $requester->id }}" @selected(old('requester_id', $ticket->user_id) == $requester->id)>{{ $requester->name }} ({{ $requester->email }})</option>
							@endforeach
						</select>
					@else
						<input class="form-control" value="{{ $ticket->user->name }}" readonly>
					@endif
				</div>
				<div class="col-md-6">
					<label for="department" class="form-label">{{ __('Department') }}</label>
					<input id="department" name="department" value="{{ old('department', $ticket->department) }}" class="form-control" maxlength="150">
				</div>
				<div class="col-12">
					<label for="asset" class="form-label">{{ __('Device / Asset') }}</label>
					<input id="asset" name="asset" value="{{ old('asset', $ticket->asset) }}" class="form-control" maxlength="200">
				</div>
				<div class="col-12">
					<label for="description" class="form-label">{{ __('Description') }}</label>
					<textarea id="description" name="description" rows="5" class="form-control" required>{{ old('description', $ticket->description) }}</textarea>
				</div>
				<div class="col-md-4">
					<label for="technician_id" class="form-label">{{ __('Technician') }}</label>
					<select id="technician_id" name="technician_id" class="form-select">
						<option value="">{{ __('Unassigned') }}</option>
						@foreach($technicians as $technician)
							<option value="{{ $technician->id }}" @selected(old('technician_id', $ticket->technician_id) == $technician->id)>{{ $technician->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="priority" class="form-label">{{ __('Priority') }}</label>
					<select id="priority" name="priority" class="form-select">
						@foreach(['Low', 'Medium', 'High', 'Critical'] as $priority)
							<option value="{{ $priority }}" @selected(old('priority', $ticket->priority) === $priority)>{{ __($priority) }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="status" class="form-label">{{ __('Status') }}</label>
					<select id="status" name="status" class="form-select">
						@foreach(['Open', 'In Progress', 'Resolved', 'Closed'] as $status)
							<option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>{{ __($status) }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="due_date" class="form-label">{{ __('Due Date') }}</label>
					<input id="due_date" type="date" name="due_date" value="{{ old('due_date', $ticket->due_date?->toDateString()) }}" class="form-control">
				</div>
				<div class="col-md-8">
					<label for="attachment" class="form-label">{{ __('Attachment') }}</label>
					<input id="attachment" type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt">
					@if($ticket->attachment_path)
						<div class="form-text">{{ __('Current file:') }} <a href="{{ route('tickets.attachment', $ticket) }}">{{ $ticket->attachment_name }}</a></div>
					@endif
				</div>
				<div class="col-md-6">
					<label for="resolution" class="form-label">{{ __('Resolution') }}</label>
					<textarea id="resolution" name="resolution" rows="3" class="form-control">{{ old('resolution', $ticket->resolution) }}</textarea>
				</div>
				<div class="col-md-6">
					<label for="root_cause" class="form-label">{{ __('Root Cause') }}</label>
					<textarea id="root_cause" name="root_cause" rows="3" class="form-control">{{ old('root_cause', $ticket->root_cause) }}</textarea>
				</div>
			</div>
			<div class="mt-4">
				<button class="btn btn-primary">{{ __('Save Ticket') }}</button>
				<a href="{{ route('tickets.show', $ticket) }}" class="btn btn-light">{{ __('Cancel') }}</a>
			</div>
		</form>
	</div>
</div>
@endsection
