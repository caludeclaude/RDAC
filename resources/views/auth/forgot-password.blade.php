@extends('layouts.guest')

@section('title', 'Forgot Password - DENR-4A RDAC Portal')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3" style="background: var(--surface-muted);">
  <div class="card p-4" style="max-width: 460px; width: 100%;">
    <h4 class="fw-bold mb-2" style="color: var(--navy);"><i class="fa-solid fa-key me-2"></i>Forgot Password</h4>
    <p class="small text-muted">
      Enter your registered <strong>Email</strong> or <strong>Username</strong>.
      A temporary password will be sent to the email address on file.
    </p>

    @if (session('status'))
      <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-bold" for="identifier">Email or Username *</label>
        <input type="text" id="identifier" name="identifier" class="form-control"
               placeholder="e.g. juancruz / juan@denr.gov.ph" value="{{ old('identifier') }}"
               autocomplete="username" required autofocus>
      </div>
      <button type="submit" class="btn btn-denr-primary fw-bold">
        <i class="fa-solid fa-paper-plane me-1"></i>Send Temporary Password
      </button>
      <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to sign in</a>
    </form>
  </div>
</div>
@endsection