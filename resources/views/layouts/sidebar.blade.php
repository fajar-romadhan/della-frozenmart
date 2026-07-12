{{-- Sidebar Navigation (Permanently Expanded 220px) --}}
<nav class="sidebar" id="appSidebar">
    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
        <div class="logo-icon-wrapper">
            <i class="ph ph-warehouse"></i>
        </div>
        <div class="sidebar-logo-text-wrapper">
            <span class="logo-title">Sistem Persediaan</span>
            <span class="logo-subtitle">Frozen Food</span>
        </div>
    </a>

    {{-- Scrollable Menu Container --}}
    <div class="sidebar-menu-scroll">
    <div class="sidebar-menu">
        @php $role = auth()->user()->role ?? 'admin'; @endphp

        {{-- ============================================================ --}}
        {{-- MENU UTAMA --}}
        {{-- ============================================================ --}}
        <div class="sidebar-category">
            <span class="category-text">Menu Utama</span>
            <div class="category-line"></div>
        </div>
        
        <a href="{{ route('dashboard') }}" class="sidebar-icon {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ph ph-house"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        {{-- ============================================================ --}}
        {{-- MASTER DATA (Admin Only) --}}
        {{-- ============================================================ --}}
        @if($role === 'admin')
            <div class="sidebar-category">
                <span class="category-text">Master Data</span>
                <div class="category-line"></div>
            </div>

            <a href="{{ route('produk.index') }}" class="sidebar-icon {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                <i class="ph ph-package"></i>
                <span class="sidebar-text">Kelola Data Produk</span>
            </a>

            <a href="{{ route('supplier.index') }}" class="sidebar-icon {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                <i class="ph ph-truck"></i>
                <span class="sidebar-text">Kelola Supplier</span>
            </a>
        @endif

        {{-- ============================================================ --}}
        {{-- TRANSAKSI (Admin Only) --}}
        {{-- ============================================================ --}}
        @if($role === 'admin')
            <div class="sidebar-category">
                <span class="category-text">Transaksi</span>
                <div class="category-line"></div>
            </div>

            <a href="{{ route('barang-masuk.index') }}" class="sidebar-icon {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
                <i class="ph ph-arrow-circle-down"></i>
                <span class="sidebar-text">Barang Masuk</span>
            </a>

            <a href="{{ route('barang-keluar.index') }}" class="sidebar-icon {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
                <i class="ph ph-arrow-circle-up"></i>
                <span class="sidebar-text">Barang Keluar</span>
            </a>

            <a href="{{ route('stok-opname.index') }}" class="sidebar-icon {{ request()->routeIs('stok-opname.*') ? 'active' : '' }}">
                <i class="ph ph-clipboard-text"></i>
                <span class="sidebar-text">Stok Opname</span>
            </a>


        @endif


        {{-- ============================================================ --}}
        {{-- ANALISA --}}
        {{-- ============================================================ --}}
        <div class="sidebar-category">
            <span class="category-text">Analisa</span>
            <div class="category-line"></div>
        </div>

        @php
            $isAnalisaActive = request()->routeIs('analisis.*') || request()->routeIs('notifikasi.*') || request()->routeIs('status-stok*') || request()->routeIs('peramalan.*');
            $unreadCount = auth()->user()->notifications()->where('status_baca', false)->count();
        @endphp
        <div class="sidebar-dropdown">
            <button class="sidebar-icon sidebar-dropdown-toggle {{ $isAnalisaActive ? 'active' : '' }}" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseAnalisa" 
                    aria-expanded="{{ $isAnalisaActive ? 'true' : 'false' }}">
                <i class="ph ph-chart-bar"></i>
                <span class="sidebar-text">Analisa Persediaan</span>
                <i class="ph ph-caret-down dropdown-arrow"></i>
                @if($unreadCount > 0)
                    <span class="sidebar-notif-badge parent-notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>
            <div class="collapse {{ $isAnalisaActive ? 'show' : '' }}" id="collapseAnalisa">
                <div class="sidebar-submenu-list">
                    @if($role === 'admin' || $role === 'manager')
                        <a href="{{ route('analisis.index') }}" class="sidebar-submenu-item {{ request()->routeIs('analisis.*') ? 'active' : '' }}">
                            <i class="ph ph-circle"></i>
                            <span class="sidebar-text">Analisa Persediaan</span>
                        </a>
                    @endif

                    @if($role === 'owner')
                        <a href="{{ route('status-stok') }}" class="sidebar-submenu-item {{ request()->routeIs('status-stok*') ? 'active' : '' }}">
                            <i class="ph ph-circle"></i>
                            <span class="sidebar-text">Status Stok</span>
                        </a>
                    @endif

                    @if($role === 'admin' || $role === 'manager' || $role === 'owner')
                        <a href="{{ route('peramalan.index') }}" class="sidebar-submenu-item {{ request()->routeIs('peramalan.*') ? 'active' : '' }}">
                            <i class="ph ph-circle"></i>
                            <span class="sidebar-text">Peramalan Stok</span>
                        </a>
                    @endif

                    <a href="{{ route('notifikasi.index') }}" class="sidebar-submenu-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                        <i class="ph ph-circle"></i>
                        <span class="sidebar-text">Notifikasi Stok</span>
                        @if($unreadCount > 0)
                            <span class="sidebar-notif-badge" style="position: relative; top: 0; right: 0; margin-left: auto; border: none; transform: none;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- LAPORAN --}}
        {{-- ============================================================ --}}
        <div class="sidebar-category">
            <span class="category-text">Laporan</span>
            <div class="category-line"></div>
        </div>

        <a href="{{ route('laporan.barang-masuk') }}" class="sidebar-icon {{ request()->routeIs('laporan.barang-masuk') ? 'active' : '' }}">
            <i class="ph ph-file-text"></i>
            <span class="sidebar-text">Laporan Barang Masuk</span>
        </a>

        @if($role === 'admin' || $role === 'owner' || $role === 'manager')
            <a href="{{ route('laporan.barang-keluar') }}" class="sidebar-icon {{ request()->routeIs('laporan.barang-keluar') ? 'active' : '' }}">
                <i class="ph ph-file-text"></i>
                <span class="sidebar-text">Laporan Barang Keluar</span>
            </a>
        @endif

        @if($role === 'owner')
            <a href="{{ route('laporan.penjualan') }}" class="sidebar-icon {{ request()->routeIs('laporan.penjualan') ? 'active' : '' }}">
                <i class="ph ph-shopping-cart"></i>
                <span class="sidebar-text">Laporan Penjualan</span>
            </a>
        @else
            <a href="{{ route('pemesanan-supplier.index') }}" class="sidebar-icon {{ request()->routeIs('pemesanan-supplier.*') ? 'active' : '' }}">
                <i class="ph ph-truck"></i>
                <span class="sidebar-text">Laporan Pemesanan Produk</span>
            </a>
        @endif
    </div>
    </div>

    {{-- Bottom: Logout --}}
    <div class="sidebar-bottom">
        <form action="{{ route('logout') }}" method="POST" id="sidebarLogout" style="width: 100%;">
            @csrf
            <button type="submit" class="sidebar-logout-card">
                <i class="ph ph-sign-out"></i>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</nav>
