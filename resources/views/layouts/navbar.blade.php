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
        <div class="position-relative d-none d-lg-flex" style="width: 280px;">
            <div class="topbar-search-wrap w-100">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" id="globalSearchInput" class="topbar-search w-100" placeholder="Cari menu atau produk..." autocomplete="off">
            </div>
            
            {{-- Search Results Dropdown --}}
            <div class="global-search-results" id="globalSearchResults" style="display: none;">
                <!-- Filled dynamically by JS -->
            </div>
        </div>

        <style>
            .global-search-results {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(0, 0, 0, 0.08);
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                margin-top: 8px;
                max-height: 350px;
                overflow-y: auto;
                z-index: 9999;
                padding: 6px;
            }
            
            .global-search-group {
                margin-bottom: 6px;
            }
            
            .global-search-group-title {
                font-size: 0.68rem;
                font-weight: 700;
                text-transform: uppercase;
                color: #94a3b8;
                padding: 4px 10px;
                letter-spacing: 0.05em;
            }
            
            .global-search-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                color: #334155;
                text-decoration: none;
                border-radius: 8px;
                font-size: 0.8rem;
                transition: all 0.15s;
            }
            
            .global-search-item:hover {
                background-color: #f1f5f9;
                color: #0f172a;
            }
            
            .global-search-item i {
                font-size: 1rem;
                color: #64748b;
            }
            
            .global-search-no-results {
                padding: 12px;
                text-align: center;
                color: #64748b;
                font-size: 0.8rem;
            }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearchInput');
            const resultsContainer = document.getElementById('globalSearchResults');
            let searchTimeout = null;

            if (searchInput && resultsContainer) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        resultsContainer.innerHTML = '';
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`/global-search?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                let html = '';
                                let hasResults = false;

                                if (data.menus && data.menus.length > 0) {
                                    hasResults = true;
                                    html += '<div class="global-search-group">';
                                    html += '<div class="global-search-group-title">Menu / Halaman</div>';
                                    data.menus.forEach(menu => {
                                        html += `<a href="${menu.url}" class="global-search-item">
                                            <i class="ph ${menu.icon}"></i>
                                            <span>${menu.name}</span>
                                        </a>`;
                                    });
                                    html += '</div>';
                                }

                                if (data.products && data.products.length > 0) {
                                    hasResults = true;
                                    html += '<div class="global-search-group">';
                                    html += '<div class="global-search-group-title">Produk</div>';
                                    data.products.forEach(prod => {
                                        html += `<a href="${prod.url}" class="global-search-item">
                                            <i class="ph ${prod.icon}"></i>
                                            <span>${prod.name}</span>
                                        </a>`;
                                    });
                                    html += '</div>';
                                }

                                if (!hasResults) {
                                    html = '<div class="global-search-no-results">Tidak ada menu atau produk yang cocok</div>';
                                }

                                resultsContainer.innerHTML = html;
                                resultsContainer.style.display = 'block';
                            })
                            .catch(err => {
                                console.error('Error global search:', err);
                            });
                    }, 300);
                });

                // Close search results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                        resultsContainer.style.display = 'none';
                    }
                });

                // Re-show results on focus if there is input
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2 && resultsContainer.children.length > 0) {
                        resultsContainer.style.display = 'block';
                    }
                });
            }
        });
        </script>

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
                    <h6 class="notif-dropdown-title">
                        Notifikasi Stok
                        <span class="status-indicator-dot" id="notifStatusDot" style="display: {{ $navUnreadCount > 0 ? 'inline-block' : 'none' }};"></span>
                    </h6>
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
                    <a class="dropdown-item py-2" href="{{ route('password.change') }}">
                        <i class="ph ph-key me-2"></i>Ganti Password
                    </a>
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
