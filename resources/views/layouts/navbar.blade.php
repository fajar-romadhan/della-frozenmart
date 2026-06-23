{{-- Top Navbar (Glassmorphic) --}}
<header class="topbar" id="mainNavbar">
    <div class="d-flex align-items-center gap-3">
        {{-- Hamburger (mobile) --}}
        <button class="topbar-hamburger d-lg-none" id="btnHamburger" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <i class="ph ph-list"></i>
        </button>

        {{-- Brand Name --}}
        <span class="topbar-brand">Della Frozen Mart</span>

        {{-- Nav Tabs --}}
        <nav class="topbar-nav d-none d-md-flex">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        </nav>
    </div>

    <div class="topbar-right">
        {{-- Search --}}
        <div class="topbar-search-wrap d-none d-lg-flex">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" class="topbar-search" placeholder="Cari menu atau produk..." readonly>
        </div>

        {{-- Notification Bell + Dropdown --}}
        <div class="notif-dropdown-wrap" id="notifDropdownWrap">
            <button type="button" class="topbar-notif" id="navNotifBell" title="Notifikasi" onclick="toggleNotifDropdown(event)">
                <i class="ph ph-bell"></i>
                @php
                    $navUnreadCount = auth()->user()->notifications()->where('status_baca', false)->count();
                @endphp
                @if($navUnreadCount > 0)
                    <span class="topbar-notif-badge">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span>
                @endif
            </button>

            {{-- Dropdown Widget --}}
            <div class="notif-dropdown" id="notifDropdown">
                {{-- Header --}}
                <div class="notif-dropdown-header">
                    <h6 class="notif-dropdown-title">Notifikasi Stok</h6>
                    <a href="{{ route('notifikasi.read-all') }}" class="notif-dropdown-mark-read"
                       onclick="event.preventDefault(); document.getElementById('formMarkAllRead').submit();">
                        Tandai semua sebagai dibaca
                    </a>
                    <form id="formMarkAllRead" action="{{ route('notifikasi.read-all') }}" method="POST" style="display:none;">@csrf</form>
                </div>

                {{-- Body --}}
                <div class="notif-dropdown-body" id="notifDropdownBody">
                    {{-- Loading Skeleton --}}
                    <div class="notif-dropdown-loading" id="notifDropdownLoading">
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-40"></div>
                                <div class="notif-skeleton-line w-70"></div>
                            </div>
                        </div>
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-50"></div>
                                <div class="notif-skeleton-line w-60"></div>
                            </div>
                        </div>
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-35"></div>
                                <div class="notif-skeleton-line w-80"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Content (populated by JS) --}}
                    <div class="notif-dropdown-list" id="notifDropdownList" style="display: none;"></div>

                    {{-- Empty State --}}
                    <div class="notif-dropdown-empty" id="notifDropdownEmpty" style="display: none;">
                        <i class="ph ph-check-circle"></i>
                        <p>Tidak ada notifikasi stok</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="notif-dropdown-footer">
                    <a href="{{ route('notifikasi.index') }}">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <div class="topbar-user" id="navUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="topbar-user-info d-none d-sm-block">
                    <div class="topbar-user-name">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="topbar-user-role">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div>
                </div>
                <i class="ph ph-caret-down" style="font-size: 12px; color: var(--text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navUserDropdown">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold" style="font-size: 0.88rem;">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email ?? '' }}</div>
                    <span class="badge mt-1
                        @if(auth()->user()->role === 'admin') badge-role-admin
                        @elseif(auth()->user()->role === 'manager') badge-role-manager
                        @else badge-role-owner
                        @endif" style="font-size: 0.68rem;">
                        {{ ucfirst(auth()->user()->role ?? 'admin') }}
                    </span>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" id="formLogout">
                        @csrf
                        <button type="submit" class="dropdown-item py-2" id="btnLogout">
                            <i class="ph ph-sign-out me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
