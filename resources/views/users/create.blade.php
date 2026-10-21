@extends('layouts.app')
@section('title', __('Add User'))
@section('page-heading', __('Add User'))
@section('content')
<div class="card form-card shadow-sm">
    <div class="card-body p-4">
        <h3 class="fw-bold mb-4">{{ __('Add User') }}</h3>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Role') }}</label>
                    <select name="role" class="form-select" required>
                        <option value="Staff" @selected(old('role', 'Staff') === 'Staff')>{{ __('Staff') }}</option>
                        <option value="Admin" @selected(old('role') === 'Admin')>{{ __('Admin') }}</option>
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
