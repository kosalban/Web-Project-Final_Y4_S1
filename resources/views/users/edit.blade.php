@extends('layouts.app')
@section('title', __('Edit User'))
@section('page-heading', __('Edit User'))
@section('content')
<div class="card form-card shadow-sm">
    <div class="card-body p-4">
        <h3 class="fw-bold mb-4">{{ __('Edit User') }}</h3>
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Password') }} <small class="text-muted">{{ __('(leave blank to keep current password)') }}</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Role') }}</label>
                    <select name="role" class="form-select" required>
                        <option value="Staff" @selected(old('role', $user->role) === 'Staff')>{{ __('Staff') }}</option>
                        <option value="Admin" @selected(old('role', $user->role) === 'Admin')>{{ __('Admin') }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
