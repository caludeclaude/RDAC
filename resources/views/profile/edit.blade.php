@extends('layouts.app')

@section('title', 'Profile - DENR-4A RDAC Portal')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card p-4">
      <h4 class="fw-bold mb-3" style="color: var(--navy);">
        <i class="fa-solid fa-user-gear me-2"></i>Profile &amp; Account Settings
      </h4>

      @if ($errors->any())
        <div class="alert alert-danger small py-2">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-bold" for="full_name">Full name</label>
          <input type="text" id="full_name" name="full_name" class="form-control" maxlength="100"
                 value="{{ old('full_name', $user->full_name) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" maxlength="64"
                 value="{{ old('username', $user->username) }}" required>
          <div class="form-text">Used to sign in. Letters, numbers and . _ @ + - only.</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold" for="phone">Contact / mobile number</label>
          <input type="text" id="phone" name="phone" class="form-control" maxlength="16" inputmode="tel"
                 placeholder="e.g. 09171234567" value="{{ old('phone', $user->phone) }}">
          <div class="form-text">Format: 09XXXXXXXXX. Optional.</div>
        </div>

        <hr>
        <div class="small text-muted mb-3">
          <div><strong>E-mail:</strong> {{ $user->email ?: '-' }}</div>
          <div><strong>Role:</strong> {{ $user->role->label() }}</div>
          <div><strong>Office:</strong> {{ $user->office->name }}</div>
          <div class="mt-1">Your e-mail, role and office can only be changed by the Regional Office.</div>
        </div>

        <button type="submit" class="btn btn-denr-primary fw-bold">Save Changes</button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection