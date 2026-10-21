@extends('layouts.app')
@section('title', __('Add Category'))
@section('page-heading', __('Add Category'))
@section('content')
<div class="card form-card shadow-sm"><div class="card-body p-4"><h3 class="fw-bold mb-4">{{ __('Add Category') }}</h3>
<form method="POST" action="{{ route('categories.store') }}">@csrf @if(null)<input type="hidden" name="_method" value="{{ null }}">@endif
<div class="mb-3"><label class="form-label">{{ __('Category Name') }}</label><input name="name" value="{{ old('name',$category->name ?? '') }}" class="form-control" required></div>
<div class="mb-3"><label class="form-label">{{ __('Description') }}</label><textarea name="description" rows="4" class="form-control">{{ old('description',$category->description ?? '') }}</textarea></div>
<div class="mb-3"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-select"><option value="Active" @selected(old('status',$category->status ?? 'Active')==='Active')>{{ __('Active') }}</option><option value="Inactive" @selected(old('status',$category->status ?? '')==='Inactive')>{{ __('Inactive') }}</option></select></div>
<button class="btn btn-primary">{{ __('Save') }}</button> <a href="{{ route('categories.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
</form></div></div>
@endsection
