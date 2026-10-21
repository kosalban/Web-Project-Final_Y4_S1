@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
    <strong>{{ __('Please check the following:') }}</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif
