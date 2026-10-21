@extends('layouts.app')
@section('title', __('Create Ticket'))
@section('page-heading', __('Create Ticket'))
@section('content')
<div class="card form-card shadow-sm">
	<div class="card-body p-4">
		<h3 class="fw-bold mb-4">{{ __('Create Ticket') }}</h3>
		<form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
			@csrf
			<div class="row g-3">	
					<div class="col-md-8">
					<label for="title" class="form-label">{{ __('Title') }}</label>
					<input id="title" name="title" value="{{ old('title') }}" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label for="category_id" class="form-label">{{ __('Category') }}</label>
					<select id="category_id" name="category_id" class="form-select" required>
						<option value="">{{ __('Select category') }}</option>
						@foreach($categories as $category)
							<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
						@endforeach
					</select>
				</div>
			<div class="col-md-6">
    <label for="requester_id" class="form-label">{{ __('Requester / User') }}</label>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <select id="requester_id" name="requester_id" class="form-select" required>
            <option value="">{{ __('Select requester') }}</option>
            @foreach($requesters as $requester)
                <option value="{{ $requester->id }}" @selected(old('requester_id', auth()->id()) == $requester->id)>{{ $requester->name }}</option>
            @endforeach
        </select>
    @else
        <input class="form-control" value="{{ auth()->user()->name }}" readonly>
    @endif
</div>
				<div class="col-md-6">
					<label for="department" class="form-label">{{ __('Department') }}</label>
					<input id="department" name="department" value="{{ old('department') }}" class="form-control" maxlength="150">
				</div>
				<div class="col-12">
					<label for="asset" class="form-label">{{ __('Device / Asset') }}</label>
					<input id="asset" name="asset" value="{{ old('asset') }}" class="form-control" maxlength="200" placeholder="{{ __('Device name, asset tag, or serial number') }}">
				</div>
				<div class="col-12">
					<label for="description" class="form-label">{{ __('Description') }}</label>
					<textarea id="description" name="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
				</div>
				<div class="col-md-4">
					<label for="technician_id" class="form-label">{{ __('Technician') }}</label>
					<select id="technician_id" name="technician_id" class="form-select">
						<option value="">{{ __('Unassigned') }}</option>
						@foreach($technicians as $technician)
							<option value="{{ $technician->id }}" @selected(old('technician_id') == $technician->id)>{{ $technician->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="priority" class="form-label">{{ __('Priority') }}</label>
					<select id="priority" name="priority" class="form-select">
						@foreach(['Low', 'Medium', 'High', 'Critical'] as $priority)
							<option value="{{ $priority }}" @selected(old('priority', 'Medium') === $priority)>{{ __($priority) }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="status" class="form-label">{{ __('Status') }}</label>
					<select id="status" name="status" class="form-select">
						@foreach(['Open', 'In Progress', 'Resolved', 'Closed'] as $status)
							<option value="{{ $status }}" @selected(old('status', 'Open') === $status)>{{ __($status) }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<label for="due_date" class="form-label">{{ __('Due Date') }}</label>
					<input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}" class="form-control" min="{{ now()->toDateString() }}">
				</div>
				<div class="col-md-8">
					<label for="attachment" class="form-label">{{ __('Attachment') }}</label>
					<input id="attachment" type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt">
					<div class="form-text">{{ __('Maximum 10 MB.') }}</div>
				</div>
				<div class="col-md-6">
					<label for="resolution" class="form-label">{{ __('Resolution') }}</label>
					<textarea id="resolution" name="resolution" rows="3" class="form-control">{{ old('resolution') }}</textarea>
				</div>
				<div class="col-md-6">
					<label for="root_cause" class="form-label">{{ __('Root Cause') }}</label>
					<textarea id="root_cause" name="root_cause" rows="3" class="form-control">{{ old('root_cause') }}</textarea>
				</div>
			</div>
			<div class="mt-4">
				<button class="btn btn-primary">{{ __('Create Ticket') }}</button>
				<a href="{{ route('tickets.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
			</div>
		</form>
	</div>
</div>
@endsection
