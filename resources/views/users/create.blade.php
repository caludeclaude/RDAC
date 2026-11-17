@extends('layouts.app')

@section('title', 'Create Staff Account - DENR-4A RDAC Portal')

@push('styles')
    <link href="{{ asset('css/user-form.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $roles = [
        'penro'       => ['PENRO',       'Provincial Field Office', 'fa-map-location-dot'],
        'cenro'       => ['CENRO',       'City / Municipal Office', 'fa-location-dot'],
        'division'    => ['Division',    'Regional Division Staff', 'fa-sitemap'],
        'super_admin' => ['Super Admin', 'Regional Office (RO-4A)', 'fa-user-shield'],
    ];
    $currentRole = old('role', 'penro');
@endphp

<div class="card shadow-sm bg-white" style="overflow:hidden;">
  <div class="uc-head">
    <span class="uc-ico"><i class="fa-solid fa-user-plus"></i></span>
    <div class="flex-grow-1">
      <h5>Create Staff Account</h5>
      <p>Login credentials are e-mailed to the new user automatically.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>

  <div class="p-3 p-md-4">
    @if ($errors->any())
      <div class="alert alert-danger small py-2">
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
      @csrf

      <div class="uc-sec"><b>1</b>Account holder</div>
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
          <label class="uc-lbl" for="full_name">Full name<span class="req">*</span></label>
          <input type="text" id="full_name" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                 value="{{ old('full_name') }}" placeholder="e.g. Juan Dela Cruz" maxlength="100" required>
        </div>
        <div class="col-12 col-md-6">
          <label class="uc-lbl" for="username">Username<span class="req">*</span></label>
          <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
                 value="{{ old('username') }}" placeholder="e.g. juancruz" maxlength="64" autocapitalize="off" spellcheck="false" required>
          <div class="uc-hint">3&ndash;64 characters: letters, numbers and . _ @ + - &middot; suggested from the name.</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="uc-lbl" for="email">E-mail address<span class="req">*</span></label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" placeholder="e.g. juan@denr.gov.ph" maxlength="100" autocapitalize="off" spellcheck="false" required>
          <div class="uc-hint">Receives the temporary password and notices.</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="uc-lbl" for="phone">Mobile number<span class="req">*</span></label>
          <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                 value="{{ old('phone') }}" placeholder="e.g. 09171234567" maxlength="16" inputmode="tel" required>
          <div class="uc-hint">Format: 09XXXXXXXXX.</div>
        </div>
      </div>

      <div class="uc-sec"><b>2</b>Role &amp; office</div>
      <div class="uc-roles" role="radiogroup" aria-label="Staff role">
        @foreach ($roles as $value => [$title, $sub, $icon])
          <div class="uc-role">
            <input type="radio" name="role" id="role_{{ $value }}" value="{{ $value }}" @checked($currentRole === $value)>
            <label for="role_{{ $value }}">
              <span class="uc-rico"><i class="fa-solid {{ $icon }}"></i></span>
              <span class="uc-rt">{{ $title }}</span>
              <span class="uc-rs">{{ $sub }}</span>
            </label>
          </div>
        @endforeach
      </div>
      <div class="uc-roledesc" id="roleDesc"></div>

      <div class="mt-3">
        <div id="office-penro">
          <label class="uc-lbl" for="province">Province<span class="req">*</span></label>
          <select id="province" name="province" class="form-select @error('province') is-invalid @enderror">
            <option value="">Select province...</option>
            @foreach ($provinces as $province)
              <option value="{{ $province }}" @selected(old('province') === $province)>{{ $province }}</option>
            @endforeach
          </select>
        </div>
        <div id="office-cenro" class="d-none">
          <label class="uc-lbl" for="city">City / Municipality covered<span class="req">*</span></label>
          <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror"
                 value="{{ old('city') }}" placeholder="e.g. Calamba" maxlength="90">
          <div class="uc-hint">Type the city or municipality only (e.g. Calamba, Lipa). &ldquo;CENRO&rdquo; is added for you.</div>
        </div>
        <div id="office-division" class="d-none">
          <label class="uc-lbl" for="division">Division name<span class="req">*</span></label>
          <input type="text" id="division" name="division" class="form-control @error('division') is-invalid @enderror"
                 value="{{ old('division') }}" placeholder="e.g. Enforcement Division" maxlength="100">
        </div>
        <div class="uc-preview">Office will be saved as: <span class="badge badge-office" id="officePreview">&mdash;</span></div>
      </div>

      <div class="uc-sec mt-4"><b>3</b>Sign-in</div>
      <div class="uc-note">
        <i class="fa-solid fa-key"></i>
        <div>
          <strong>No password to type.</strong> A secure 12-character temporary password is generated and e-mailed to the address above.
          <ul>
            <li>The user must set their own password at first login (at least 12 characters, with a letter and a number).</li>
            <li>If the e-mail does not arrive, the user can request a new one with <em>Forgot password?</em> on the login page.</li>
          </ul>
        </div>
      </div>

      <button type="submit" class="btn btn-denr-primary fw-bold">
        <i class="fa-solid fa-paper-plane me-1"></i>Create &amp; E-mail Credentials
      </button>
      <a href="{{ route('users.index') }}" class="btn btn-light border">Cancel</a>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    var DESC = {
      penro: 'Provincial field staff. Sees the complaints their office filed, holds or handled, and can act on and route them. Cannot mark a complaint Resolved.',
      cenro: 'City / municipal field staff. Sees the complaints their office filed, holds or handled, and can act on and route them. Cannot mark a complaint Resolved.',
      division: 'Division staff (e.g. Enforcement). Sees the complaints their division filed, holds or handled, and can act on and route them. Cannot mark a complaint Resolved.',
      super_admin: 'Regional Office administrator: sees every complaint, manages accounts and the archive, and can mark complaints Resolved.'
    };
    var usernameTouched = document.getElementById('username').value !== '';

    function val(id) { return (document.getElementById(id).value || '').trim(); }
    function role() { var r = document.querySelector('input[name="role"]:checked'); return r ? r.value : 'penro'; }

    function officeName() {
      var r = role();
      if (r === 'super_admin') return 'Regional Office';
      if (r === 'penro') { var p = val('province'); return p ? 'PENRO ' + p : ''; }
      if (r === 'cenro') { var c = val('city').replace(/\s+/g, ' ').replace(/^cenro\s+/i, ''); return c ? 'CENRO ' + c : ''; }
      return val('division').replace(/\s+/g, ' ');
    }

    function refresh() {
      var r = role();
      document.getElementById('office-penro').classList.toggle('d-none', r !== 'penro');
      document.getElementById('office-cenro').classList.toggle('d-none', r !== 'cenro');
      document.getElementById('office-division').classList.toggle('d-none', r !== 'division');
      document.getElementById('roleDesc').textContent = DESC[r] || '';
      document.getElementById('officePreview').textContent = officeName() || '\u2014';
    }

    // Suggest a username from the full name (first name + last name)
    function suggest(name) {
      var parts = name.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/).filter(Boolean);
      return (parts.length > 1 ? parts[0] + parts[parts.length - 1] : (parts[0] || '')).slice(0, 64);
    }

    document.getElementById('full_name').addEventListener('input', function () {
      if (!usernameTouched) document.getElementById('username').value = suggest(this.value);
    });
    document.getElementById('username').addEventListener('input', function () { usernameTouched = this.value !== ''; });

    Array.prototype.forEach.call(document.querySelectorAll('input[name="role"]'), function (r) { r.addEventListener('change', refresh); });
    ['province', 'city', 'division'].forEach(function (id) {
      document.getElementById(id).addEventListener('input', refresh);
      document.getElementById(id).addEventListener('change', refresh);
    });
    refresh();
  })();
</script>
@endpush