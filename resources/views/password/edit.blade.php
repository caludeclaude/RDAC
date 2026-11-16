@extends('layouts.app')

@section('title', 'Change Password - DENR-4A RDAC Portal')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card p-4">
      @php $forced = auth()->user()->must_change_password; @endphp

      <h4 class="fw-bold mb-3" style="color: var(--navy);">
        <i class="fa-solid fa-lock me-2"></i>{{ $forced ? 'Mandatory Password Change' : 'Change Password' }}
      </h4>

      @if ($forced)
        <p class="small text-muted">This is your first login or your password was reset. Please set a new password to continue.</p>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger small py-2">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-bold" for="current_password">{{ $forced ? 'Temporary password' : 'Current password' }}</label>
          <div class="input-group">
            <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password" required>
            <button type="button" class="btn btn-outline-secondary" data-for="current_password" onclick="toggleEye(this)" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold" for="password">New password</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="At least 12 characters, letters and numbers" autocomplete="new-password" required>
            <button type="button" class="btn btn-outline-secondary" data-for="password" onclick="toggleEye(this)" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold" for="password_confirmation">Confirm new password</label>
          <div class="input-group">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
            <button type="button" class="btn btn-outline-secondary" data-for="password_confirmation" onclick="toggleEye(this)" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
          </div>
        </div>

        <ul class="list-unstyled small mb-3" id="passRules"></ul>
        <div class="form-text mb-3">Common passwords are rejected.</div>

        <button type="submit" class="btn btn-denr-primary fw-bold">Update Password</button>
        @unless ($forced)
          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
        @endunless
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Show / hide a password box
  function toggleEye(btn) {
    var box = document.getElementById(btn.getAttribute('data-for'));
    var show = box.type === 'password';
    box.type = show ? 'text' : 'password';
    btn.innerHTML = '<i class="fa-solid ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
  }

  // Live checklist: a rule turns green when it is met (the server checks again)
  function updateRules() {
    var cur = document.getElementById('current_password').value;
    var p = document.getElementById('password').value;
    var c = document.getElementById('password_confirmation').value;
    var rules = [
      [p.length >= 12, 'At least 12 characters'],
      [/[A-Za-z]/.test(p) && /\d/.test(p), 'At least one letter and one number'],
      [p !== '' && p !== cur, 'Different from your current password'],
      [p !== '' && p === c, 'Both new passwords match']
    ];
    document.getElementById('passRules').innerHTML = rules.map(function (r) {
      return '<li class="' + (r[0] ? 'text-success' : 'text-muted') + '"><i class="' +
             (r[0] ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle') + ' me-2"></i>' + r[1] + '</li>';
    }).join('');
  }
  ['current_password', 'password', 'password_confirmation'].forEach(function (id) {
    document.getElementById(id).addEventListener('input', updateRules);
  });
  updateRules();
</script>
@endpush