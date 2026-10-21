@extends('layouts.app')
@section('title', __('Settings'))
@section('page-heading', __('Settings'))
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<h3 class="fw-bold mb-4">{{ __('Profile Settings') }}</h3>
<form method="POST" action="{{ route('settings.update') }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" value="{{ old('name',$user->name) }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">{{ __('New Password') }}</label><input type="password" name="password" class="form-control"></div>
<div class="col-md-6"><label class="form-label">{{ __('Confirm New Password') }}</label><input type="password" name="password_confirmation" class="form-control"></div>
</div><div class="mt-4"><button class="btn btn-primary">{{ __('Update Profile') }}</button></div>
</form></div></div>
@endsection
