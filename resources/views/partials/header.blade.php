<header>
    <div class="app-hdr">
        <div class="container-fluid max-ww-container px-3 px-md-4">
            <div class="hdr-card">
                <div class="hdr-brand">
                    <img src="https://thumb.wikimedia.org/wikipedia/commons/thumb/0/0f/Seal_of_the_Department_of_Environment_and_Natural_Resources.svg/500px-Seal_of_the_Department_of_Environment_and_Natural_Resources.svg.png"
                         alt="DENR Seal" class="hdr-seal">
                    <div class="hdr-brand-text">
                        <div class="hdr-title">DENR CALABARZON</div>
                        <div class="hdr-sys">RDAC PORTAL</div>
                        <div class="hdr-sub">RDAC Reporting &amp; Complaints Tracking System</div>
                    </div>
                </div>

                @auth
                {{-- Hamburger: only visible below 1024px (see theme.css) --}}
                <button type="button" class="hdr-toggle" id="hdrToggle"
                        aria-label="Open menu" aria-controls="hdrMenu" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="hdr-backdrop" id="hdrBackdrop"></div>

                <div class="hdr-tools" id="hdrMenu">
                    <div class="hdr-user">
                        <span class="hdr-dot"></span>
                        <div>
                            <div class="hdr-name">{{ auth()->user()->full_name }}</div>
                            <div class="hdr-meta">{{ auth()->user()->role->label() }} &bull; {{ auth()->user()->office->name }}</div>
                        </div>
                    </div>
                    <div class="hdr-actions">
                        <a href="{{ route('profile.edit') }}" class="hdr-btn">
                            <i class="fa-solid fa-user"></i><span>Profile</span>
                        </a>
                        <a href="{{ route('password.edit') }}" class="hdr-btn">
                            <i class="fa-solid fa-key"></i><span>Password</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hdr-btn hdr-btn-danger">
                                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

            </div>
        </div>
    </div>

 @auth
 <script>
  (function () {
    if (window.__hdrMenuReady) return;   // never attach twice
    window.__hdrMenuReady = true;

    var card = document.querySelector('.hdr-card');
    var btn = document.getElementById('hdrToggle');
    var backdrop = document.getElementById('hdrBackdrop');
    if (!card || !btn) return;

    function setOpen(open) {
      card.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      btn.innerHTML = '<i class="fa-solid ' + (open ? 'fa-xmark' : 'fa-bars') + '"></i>';
    }

    btn.addEventListener('click', function () {
        setOpen(!card.classList.contains('open'));
        });
        if (backdrop) backdrop.addEventListener('click', function () { setOpen(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.addEventListener('resize', function () { if (window.innerWidth >= 1200) setOpen(false); });
    })();
 </script>
 @endauth
</header>