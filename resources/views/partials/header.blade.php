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
                <div class="hdr-tools" id="hdrMenu">
                    <div class="hdr-user">
                        <span class="hdr-dot"></span>
                        <div>
                            <div class="hdr-name">{{ auth()->user()->full_name }}</div>
                            <div class="hdr-meta">{{ auth()->user()->role->label() }} &bull; {{ auth()->user()->office->name }}</div>
                        </div>
                    </div>
                    <div class="hdr-actions">
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
</header>