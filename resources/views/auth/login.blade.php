@extends('layouts.guest')

@section('title', 'Sign in - DENR-4A RDAC Portal')

@push('styles')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="min-vh-100">
  <div class="lg">
    <section class="lg-hero">
      <div class="lg-seal">
        <img src="https://thumb.wikimedia.org/wikipedia/commons/thumb/0/0f/Seal_of_the_Department_of_Environment_and_Natural_Resources.svg/500px-Seal_of_the_Department_of_Environment_and_Natural_Resources.svg.png" alt="DENR Seal">
        <div class="lh-sm">
          <div class="small text-uppercase opacity-75" style="letter-spacing:.08em;">Republic of the Philippines</div>
          <div class="fw-bold">Department of Environment and Natural Resources</div>
          <div class="small opacity-75">Region 4-A (CALABARZON)</div>
        </div>
      </div>
      <div class="lg-copy">
        <h2>Regional DENR Action Center (RDAC)</h2>
        <p class="mb-0 opacity-75" style="max-width:34ch;">File, receive, route and resolve environmental complaints across CALABARZON, in one secure place.</p>
      </div>
      <div class="small opacity-75">Mayapa Main Road (Along SLEX), Brgy. Mayapa, Calamba City, Laguna</div>
    </section>

    <section class="lg-panel">
      <div class="lg-form">
        <h1 class="h3 fw-bold mb-1" style="color:var(--navy);">Welcome back</h1>
        <p class="text-muted mb-4">Sign in to the RDAC Portal with your staff account.</p>

        @if ($errors->any())
          <div class="alert alert-danger small py-2" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold" for="login">Username or Email</label>
            <input type="text" id="login" name="login" value="{{ old('login') }}" class="form-control form-control-lg fs-6"
                   placeholder="Enter username or email" autocomplete="username" required autofocus>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold" for="password">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control form-control-lg fs-6"
                     placeholder="Enter password" autocomplete="current-password" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" aria-label="Show password">
                <i class="fa-solid fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
          <div class="text-end mb-4">
            <span class="small text-muted">Forgot password? (coming soon)</span>
          </div>
          <button type="submit" class="btn btn-denr-primary btn-lg w-100 fw-bold">Sign in</button>
        </form>

        <p class="small text-muted text-center mt-4 mb-0"><i class="fa-solid fa-lock me-1"></i>Accounts are managed by the DENR-4A Regional Office.</p>
      </div>
    </section>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Show / hide the password
  function togglePassword() {
    var box = document.getElementById('password');
    var icon = document.getElementById('eyeIcon');
    var show = box.type === 'password';
    box.type = show ? 'text' : 'password';
    icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
  }
</script>
@endpush