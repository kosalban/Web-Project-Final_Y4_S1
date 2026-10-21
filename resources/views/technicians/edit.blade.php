@extends('layouts.app')
@section('title', __('Edit Technician'))
@section('page-heading', __('Edit Technician'))
@section('content')
<div class="card form-card shadow-sm"><div class="card-body p-4"><h3 class="fw-bold mb-4">{{ __('Edit Technician') }}</h3>
<form method="POST" action="{{ route('technicians.update',$technician) }}">@csrf @if('PUT')<input type="hidden" name="_method" value="{{ 'PUT' }}">@endif
<div class="row g-3">
<div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" value="{{ old('name',$technician->name ?? '') }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input type="email" name="email" value="{{ old('email',$technician->email ?? '') }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input name="phone" value="{{ old('phone',$technician->phone ?? '') }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">{{ __('Specialization') }}</label><input name="specialization" value="{{ old('specialization',$technician->specialization ?? '') }}" class="form-control"></div>
<div class="col-md-6"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-select"><option value="Active" @selected(old('status',$technician->status ?? 'Active')==='Active')>{{ __('Active') }}</option><option value="Inactive" @selected(old('status',$technician->status ?? '')==='Inactive')>{{ __('Inactive') }}</option></select></div>
</div><div class="mt-4"><button class="btn btn-primary">{{ __('Save') }}</button> <a href="{{ route('technicians.index') }}" class="btn btn-light">{{ __('Cancel') }}</a></div>
</form></div></div>
@endsection
