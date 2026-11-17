@extends('layouts.app')

@section('title', 'File Complaint - DENR-4A RDAC Portal')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-xl-9">
    <h4 class="fw-bold mb-1" style="color: var(--navy);">File an Environmental Complaint</h4>
    <p class="text-muted mb-4">Fields marked * are required.</p>

    @if ($errors->any())
      <div class="alert alert-danger small py-2">
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
    @csrf

      {{-- 1. Complainant --}}
      <div class="card p-4 mb-3">
        <div class="form-step"><b>1</b><h6>Complainant</h6></div>

        <label class="form-label fw-semibold" for="complainant_name">Display name</label>
        <input type="text" id="complainant_name" name="complainant_name" class="form-control"
               value="{{ old('complainant_name') }}" maxlength="100" required>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1" @checked(old('is_anonymous'))>
          <label class="form-check-label small text-muted" for="is_anonymous">File anonymously (hide the name in official reports)</label>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="complainant_email">Complainant e-mail <span class="text-muted fw-normal">(optional)</span></label>
            <input type="email" id="complainant_email" name="complainant_email" class="form-control"
                   value="{{ old('complainant_email') }}" placeholder="e.g. juan@email.com" maxlength="100" autocapitalize="off" spellcheck="false">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="complainant_phone">Complainant mobile number <span class="text-muted fw-normal">(optional)</span></label>
            <input type="text" id="complainant_phone" name="complainant_phone" class="form-control"
                   value="{{ old('complainant_phone') }}" placeholder="e.g. 09171234567" maxlength="16" inputmode="tel">
          </div>
        </div>
        <div class="small text-muted mt-2">Used only to send status updates and to contact the complainant. It is not shown in the PDF or to offices that do not hold the complaint.</div>
      </div>

      {{-- 2. Where and what --}}
      <div class="card p-4 mb-3">
        <div class="form-step"><b>2</b><h6>Where and what</h6></div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold" for="subject">Subject of complaint *</label>
            <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}"
                   placeholder="Short title, e.g. Illegal dumping near Mayapa river" maxlength="150" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="target_office_id">Route to office *</label>
            <select id="target_office_id" name="target_office_id" class="form-select" required>
              <option value="">Select office...</option>
              @foreach ($offices as $office)
                <option value="{{ $office->id }}" @selected((string) old('target_office_id') === (string) $office->id)>{{ $office->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="complaint_type_id">Nature of complaint *</label>
            <select id="complaint_type_id" name="complaint_type_id" class="form-select" required>
              @foreach ($types as $type)
                <option value="{{ $type->id }}" @selected((string) old('complaint_type_id') === (string) $type->id)>{{ $type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label fw-semibold" for="barangay">Barangay *</label>
            <input type="text" id="barangay" name="barangay" class="form-control" value="{{ old('barangay') }}" placeholder="e.g. Mayapa" maxlength="100" required>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label fw-semibold" for="city">City / Municipality *</label>
            <input type="text" id="city" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. Calamba City" maxlength="100" required>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label fw-semibold" for="province">Province *</label>
            <select id="province" name="province" class="form-select" required>
              <option value="">Select...</option>
              @foreach ($provinces as $province)
                <option value="{{ $province }}" @selected(old('province') === $province)>{{ $province }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- 3. Details --}}
      <div class="card p-4 mb-3">
        <div class="form-step"><b>3</b><h6>Details</h6></div>
        <label class="form-label fw-semibold" for="description">Incident narrative *</label>
        <textarea id="description" name="description" class="form-control" rows="5" maxlength="5000"
                  placeholder="Provide complete facts: what happened, when, who is involved..." required>{{ old('description') }}</textarea>
        <div class="small text-muted text-end mt-1"><span id="descCount">0</span> / 5000</div>

        <label class="form-label fw-semibold mt-3">Attachment <span class="text-muted fw-normal">(optional)</span></label>
        <div id="dropzone" class="dz" tabindex="0" role="button" aria-label="Choose a file">
          <i class="fa-solid fa-cloud-arrow-up d-block"></i>
          <div class="fw-semibold">Drag a file here or click to browse</div>
          <div class="small text-muted">PDF, JPG or PNG, up to 10 MB</div>
        </div>
        <input type="file" id="fileInput" name="attachment" class="d-none"
               accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
        <div id="filePreview" class="dz-preview d-none"></div>
      </div>

      {{-- 4. Privacy --}}
      <div class="card p-4 mb-3">
        <div class="form-step"><b>4</b><h6>Privacy</h6></div>
        <details class="small text-muted mb-2">
          <summary class="fw-bold">Privacy Notice (Data Privacy Act of 2012)</summary>
          <p class="mt-2 mb-0">
            DENR Region 4-A collects the details in this form (name, optional e-mail and mobile number, incident location, narrative and attachment) only to receive,
            route, investigate and resolve environmental complaints. Access is limited to authorized DENR personnel and the offices
            handling the complaint. Records are kept for the period required by law and agency rules. You may ask to see or correct
            your data. Filing anonymously hides the complainant's name in official reports.
            Questions: Data Protection Officer, DENR Region 4-A (dpo@gmail.com).
          </p>
        </details>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required>
          <label class="form-check-label small" for="privacy_consent">I have read the Privacy Notice and, if I am filing for another person, I have informed them of it. *</label>
        </div>
      </div>

      <button type="submit" class="btn btn-denr-primary btn-lg w-100 fw-bold">
        <i class="fa-solid fa-paper-plane me-2"></i>Submit Complaint
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // --- "File anonymously" checkbox and the character counter ---
  (function () {
    var anon = document.getElementById('is_anonymous');
    var name = document.getElementById('complainant_name');
    function syncAnon(clear) {
      if (anon.checked) { name.value = 'Anonymous'; name.readOnly = true; }
      else { name.readOnly = false; if (clear && name.value === 'Anonymous') name.value = ''; }
    }
    anon.addEventListener('change', function () { syncAnon(true); });
    syncAnon(false);

    var desc = document.getElementById('description');
    var cnt = document.getElementById('descCount');
    function count() { cnt.textContent = desc.value.length; }
    desc.addEventListener('input', count);
    count();
  })();

  // --- Attachment box with a preview ---
  (function () {
    var inp = document.getElementById('fileInput');
    var dz = document.getElementById('dropzone');
    var prev = document.getElementById('filePreview');
    var url = '';

    if (!inp || !dz || !prev) {
      console.error('Attachment box not found: check the HTML block in card 3 (ids: dropzone, fileInput, filePreview).');
      return;
    }

    function allowed(f) {
      if (['application/pdf', 'image/jpeg', 'image/png'].indexOf(f.type) === -1) {
        Swal.fire({ icon: 'warning', title: 'File type not allowed', text: 'Please attach a PDF, JPG or PNG file.', confirmButtonColor: '#099C55' });
        return false;
      }
      if (f.size > 10 * 1024 * 1024) {
        Swal.fire({ icon: 'warning', title: 'File too large', text: 'The attachment must be 10 MB or smaller.', confirmButtonColor: '#099C55' });
        return false;
      }
      return true;
    }

    function render() {
      if (url) { URL.revokeObjectURL(url); url = ''; }
      var f = inp.files && inp.files[0];
      if (f && !allowed(f)) { inp.value = ''; f = null; }
      dz.classList.toggle('d-none', !!f);
      prev.classList.toggle('d-none', !f);
      prev.innerHTML = '';
      if (!f) return;

      var thumb;
      if (f.type.indexOf('image/') === 0) {
        url = URL.createObjectURL(f);
        thumb = document.createElement('img'); thumb.src = url; thumb.alt = '';
      } else {
        thumb = document.createElement('div'); thumb.className = 'dz-ico';
        thumb.innerHTML = '<i class="fa-solid fa-file-pdf"></i>';
      }
      var info = document.createElement('div'); info.className = 'flex-grow-1 small'; info.style.minWidth = '0';
      var nm = document.createElement('div'); nm.className = 'fw-semibold text-truncate'; nm.textContent = f.name;
      var sz = document.createElement('div'); sz.className = 'text-muted'; sz.textContent = (f.size / 1048576).toFixed(2) + ' MB';
      info.appendChild(nm); info.appendChild(sz);
      var rm = document.createElement('button'); rm.type = 'button'; rm.className = 'btn btn-sm btn-outline-secondary';
      rm.innerHTML = '<i class="fa-solid fa-xmark"></i>'; rm.title = 'Remove file';
      rm.onclick = function () { inp.value = ''; render(); };
      prev.appendChild(thumb); prev.appendChild(info); prev.appendChild(rm);
    }

    inp.addEventListener('change', render);
    dz.addEventListener('click', function () { inp.click(); });
    dz.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); inp.click(); } });
    ['dragenter', 'dragover'].forEach(function (n) { dz.addEventListener(n, function (e) { e.preventDefault(); dz.classList.add('drag'); }); });
    ['dragleave', 'drop'].forEach(function (n) { dz.addEventListener(n, function (e) { e.preventDefault(); dz.classList.remove('drag'); }); });
    dz.addEventListener('drop', function (e) { if (e.dataTransfer.files.length) { inp.files = e.dataTransfer.files; render(); } });
  })();
</script>
@endpush