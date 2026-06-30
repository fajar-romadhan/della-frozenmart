@extends('layouts.app')
@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary mb-1">Selamat datang kembali, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Ringkasan eksekutif, analisis penjualan harian, dan kesehatan operasional</p>
        </div>
    </div>

    <!-- Executive Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-primary">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-trend-up"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Penjualan Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($penjualanBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-info">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-clock-counter-clockwise"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Penjualan Bulan Lalu</span>
                    <h3 class="stat-value mb-0">{{ number_format($penjualanBulanLalu ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-success">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-download-simple"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Barang Masuk Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($barangMasukBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-danger">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-warning-octagon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Stok Perlu Dipesan (ROP)</span>
                    <h3 class="stat-value mb-0 text-danger">{{ $statusOverview['order'] ?? 0 }} Produk</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="row">
        <!-- Left Side (8 Columns) -->
        <div class="col-lg-8">
            <!-- 1. Statistik Penjualan (AJAX Chart) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-chart-bar text-primary me-1"></i> Tren Penjualan Harian & Bulanan</h5>
                    <div class="chart-controls">
                        <!-- Month Filter -->
                        <select id="filterSalesMonth" class="chart-select">
                            <option value="">Sepanjang Tahun</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <!-- Year Filter -->
                        <select id="filterSalesYear" class="chart-select">
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 2. Top Selling Products Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-crown text-primary me-1"></i> 5 Produk Terlaris (Bulan Ini)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkTerlaris as $item)
                                    <tr>
                                        <td><span class="badge bg-light text-dark fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                        <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                                        <td>{{ $item->product->category->nama_kategori ?? '-' }}</td>
                                        <td class="text-center fw-bold text-success">{{ number_format($item->total_terjual, 0, ',', '.') }} {{ $item->product->satuan ?? 'pcs' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. ROP Recommendations Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-danger"><i class="ph ph-shopping-cart text-danger me-1"></i> Daftar Rekomendasi Pemesanan Ulang (ROP)</h5>
                    <a href="{{ route('status-stok') }}" class="btn btn-sm btn-outline-danger">Detail Status Stok</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Stok Saat Ini</th>
                                    <th class="text-center">Safety Stock</th>
                                    <th class="text-center">Reorder Point (ROP)</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkPerluDipesan as $item)
                                    <tr>
                                        <td><span class="badge bg-light text-dark fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                        <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                                        <td class="text-center fw-bold text-danger">{{ number_format($item->stok_saat_ini, 0, ',', '.') }}</td>
                                        <td class="text-center text-muted">{{ number_format($item->safety_stock, 0, ',', '.') }}</td>
                                        <td class="text-center text-muted">{{ number_format($item->reorder_point, 0, ',', '.') }}</td>
                                        <td class="text-center"><span class="badge bg-danger">ORDER</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="ph ph-check-circle text-success fs-1 d-block mb-2"></i>
                                            Semua stok aman. Tidak ada produk yang memerlukan pemesanan mendesak.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($produkPerluDipesan->hasPages())
                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted" style="font-size: 0.85rem;">
                            Menampilkan {{ $produkPerluDipesan->firstItem() }} - {{ $produkPerluDipesan->lastItem() }} dari {{ $produkPerluDipesan->total() }} produk
                        </div>
                        <div>
                            {{ $produkPerluDipesan->appends(request()->except('rop_page'))->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 4. Aktivitas Terakhir -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-clock-counter-clockwise text-primary me-1"></i> Aktivitas Terakhir</h5>
                </div>
                <div class="card-body p-0">
                    <div style="padding: 10px 16px 20px 16px;">
                        <div class="activity-timeline">
                            @forelse($aktivitasTerbaru as $act)
                                <div class="activity-item">
                                    <div class="activity-badge bg-{{ $act['warna'] }}">
                                        <i class="{{ $act['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-header">
                                            <span class="activity-title">{{ $act['judul'] }}</span>
                                        </div>
                                        <p class="activity-desc">{{ $act['deskripsi'] }}</p>
                                        <div class="activity-meta">
                                            <span class="activity-user">
                                                <i class="ph ph-user"></i> {{ $act['pengguna'] }}
                                            </span>
                                            <span class="activity-time">
                                                <i class="ph ph-calendar-blank"></i> {{ $act['waktu']->translatedFormat('d M Y, H:i') }} ({{ $act['waktu']->diffForHumans() }})
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">Belum ada aktivitas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side (4 Columns) -->
        <div class="col-lg-4">
            <!-- 1. Doughnut Chart: Stock Status Overview -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-pie-chart text-primary me-1"></i> Komposisi Status Stok</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="position: relative; height:220px; width:220px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 w-100">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="ph ph-circle-fill text-success me-2"></i> Aman</span>
                            <span class="fw-bold">{{ $statusOverview['aman'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="ph ph-circle-fill text-warning me-2"></i> Warning</span>
                            <span class="fw-bold">{{ $statusOverview['warning'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="ph ph-circle-fill text-danger me-2"></i> Order</span>
                            <span class="fw-bold text-danger">{{ $statusOverview['order'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Store Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-storefront text-primary me-1"></i> Profil Toko</h5>
                    <button class="btn btn-sm btn-light py-1" style="font-size: 0.8rem" data-bs-toggle="modal" data-bs-target="#modalEditStore">
                        <i class="ph ph-pencil-simple"></i> Edit
                    </button>
                </div>
                <div class="card-body">
                    <div class="store-profile-card">
                        <div class="store-logo-wrapper">
                            <div class="store-logo">DF</div>
                            <span class="store-status-badge">
                                <span class="pulse-status"></span> Operasional
                            </span>
                        </div>
                        <h5 class="fw-bold store-name-val mb-0 mt-2">Della Frozen Mart</h5>
                        <div class="store-branch-badge store-branch-val">-</div>
                        <div class="store-rating-badge"><i class="ph ph-star-fill"></i> <span class="store-rating-val">-</span> Google Rating</div>
                        <p class="text-muted small store-tagline-val mb-3">-</p>

                        <div class="store-info-details">
                            <div class="store-info-item">
                                <i class="ph ph-map-pin"></i>
                                <div class="store-info-text">
                                    <span class="store-info-label">Alamat Utama</span>
                                    <span class="store-address-val">-</span>
                                </div>
                            </div>
                            <div class="store-info-item">
                                <i class="ph ph-phone"></i>
                                <div class="store-info-text">
                                    <span class="store-info-label">Nomor Telepon</span>
                                    <span class="store-phone-val">-</span>
                                </div>
                            </div>
                            <div class="store-info-item">
                                <i class="ph ph-clock"></i>
                                <div class="store-info-text">
                                    <span class="store-info-label">Jam Buka</span>
                                    <span class="store-hours-val">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Grid -->
                        <div class="store-socials-grid">
                            <a href="https://instagram.com/della_frozen_mart" target="_blank" class="store-social-item instagram">
                                <i class="ph ph-instagram-logo"></i>
                                <div class="store-social-info">
                                    <span class="store-social-label">Instagram</span>
                                    <span class="store-instagram-val store-social-val">-</span>
                                    <span class="store-instagram-detail-val store-social-detail">-</span>
                                </div>
                            </a>
                            <a href="https://tiktok.com/@dellafrozenmart" target="_blank" class="store-social-item tiktok">
                                <i class="ph ph-tiktok-logo"></i>
                                <div class="store-social-info">
                                    <span class="store-social-label">TikTok</span>
                                    <span class="store-tiktok-val store-social-val">-</span>
                                </div>
                            </a>
                            <a href="#" class="store-social-item facebook">
                                <i class="ph ph-facebook-logo"></i>
                                <div class="store-social-info">
                                    <span class="store-social-label">Facebook</span>
                                    <span class="store-facebook-val store-social-val">-</span>
                                </div>
                            </a>
                            <a href="#" class="store-social-item shopee">
                                <i class="ph ph-shopping-bag"></i>
                                <div class="store-social-info">
                                    <span class="store-social-label">Shopee</span>
                                    <span class="store-shopee-val store-social-val">-</span>
                                </div>
                            </a>
                        </div>

                        <!-- Products and Services Info -->
                        <div class="store-products-services d-none">
                            <div class="store-ps-item">
                                <span class="store-ps-title"><i class="ph ph-package text-primary"></i> Katalog Produk</span>
                                <span class="store-products-val store-ps-content">-</span>
                            </div>
                            <div class="store-ps-item">
                                <span class="store-ps-title"><i class="ph ph-certificate text-primary"></i> Layanan Kemitraan</span>
                                <span class="store-services-val store-ps-content">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>
@endsection

@push('modals')
<!-- Modal Edit Profil Toko -->
<div class="modal fade" id="modalEditStore" tabindex="-1" aria-labelledby="modalEditStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-card-hover);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalEditStoreLabel"><i class="ph ph-storefront text-primary me-1"></i> Edit Profil Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-0">
                <form id="formEditStore">
                    <!-- Section 1: Informasi Utama -->
                    <h6 class="fw-bold text-primary mb-3"><i class="ph ph-info me-1"></i> Informasi Utama</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Nama Toko</label>
                            <input type="text" class="form-control" id="editStoreName" required>
                        </div>
                        <div class="col-md-6 d-none">
                            <label class="form-label small fw-bold">Cabang / Unit</label>
                            <input type="text" class="form-control" id="editStoreBranch">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Slogan / Deskripsi Singkat</label>
                        <input type="text" class="form-control" id="editStoreTagline" required>
                    </div>

                    <hr class="my-3" style="opacity: 0.1">

                    <!-- Section 2: Kontak & Operasional -->
                    <h6 class="fw-bold text-primary mb-3"><i class="ph ph-map-pin-line me-1"></i> Kontak & Operasional</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Toko</label>
                        <textarea class="form-control" id="editStoreAddress" rows="2" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Telepon</label>
                            <input type="text" class="form-control" id="editStorePhone" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jam Kerja / Buka</label>
                            <input type="text" class="form-control" id="editStoreHours" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Google Rating</label>
                            <input type="text" class="form-control" id="editStoreRating" required>
                        </div>
                    </div>

                    <hr class="my-3" style="opacity: 0.1">

                    <!-- Section 3: Media Sosial & E-commerce -->
                    <h6 class="fw-bold text-primary mb-3"><i class="ph ph-share-network me-1"></i> Media Sosial & E-commerce</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold">Instagram Username</label>
                            <input type="text" class="form-control" id="editStoreInstagram" placeholder="@username">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold">Detail Instagram (Followers / Post)</label>
                            <input type="text" class="form-control" id="editStoreInstagramDetail" placeholder="contoh: 4.170 followers, 893 postingan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">TikTok Username</label>
                            <input type="text" class="form-control" id="editStoreTiktok" placeholder="@username">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Facebook Page Name</label>
                            <input type="text" class="form-control" id="editStoreFacebook">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Shopee Store Name</label>
                            <input type="text" class="form-control" id="editStoreShopee">
                        </div>
                    </div>

                    <hr class="my-3" style="opacity: 0.1">

                    <!-- Section 4: Produk & Layanan -->
                    <div class="d-none">
                        <h6 class="fw-bold text-primary mb-3"><i class="ph ph-package me-1"></i> Katalog & Kemitraan</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">Produk Yang Dijual (Pisahkan dengan koma)</label>
                                <textarea class="form-control" id="editStoreProducts" rows="2" placeholder="Nugget, sosis, kompor portable..."></textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">Layanan Lainnya</label>
                                <textarea class="form-control" id="editStoreServices" rows="2" placeholder="Grosir, kemitraan..."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveStoreProfile">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. LocalStorage Store Profile Handler
    const defaultProfile = {
        name: "Della Frozen Mart",
        branch: "",
        tagline: "Sosis, Bakso, Nugget, Bumbu, & Alat Grill Premium",
        address: "Jln. Kiemas Lawang Kidul, Tanjung Enim (sebelah Es Teh Nusantara / depan RM Kamang Indah)",
        phone: "+62 857-8368-7636",
        hours: "08.00 Pagi – 21.30 Malam",
        rating: "5.0/5",
        instagram: "@della_frozen_mart",
        instagramDetail: "4.170 followers, 893 postingan",
        tiktok: "@dellafrozenmart",
        facebook: "Della Frozen Mart",
        shopee: "dellafrozenmart26",
        products: "Nugget, bakso, sosis, saos, bumbu, frozen food (Sony, Ciomy, Kylafood, Roker, dll) & alat grill (grill pan, kompor portable)",
        services: "Grosir & Reseller Resmi Frozen Food Lokal"
    };

    function loadStoreProfile() {
        const saved = JSON.parse(localStorage.getItem('store_profile'));
        const store = saved ? { ...defaultProfile, ...saved } : defaultProfile;
        
        const updateText = (selector, val) => {
            document.querySelectorAll(selector).forEach(el => el.textContent = val || '-');
        };
        
        updateText('.store-name-val', store.name);
        
        // Handle branch badge display dynamically
        document.querySelectorAll('.store-branch-badge').forEach(el => {
            if (store.branch && store.branch.trim() !== '' && store.branch !== '-') {
                el.textContent = store.branch;
                el.style.display = 'inline-block';
            } else {
                el.style.display = 'none';
            }
        });
        updateText('.store-tagline-val', store.tagline);
        updateText('.store-address-val', store.address);
        updateText('.store-phone-val', store.phone);
        updateText('.store-hours-val', store.hours);
        updateText('.store-rating-val', store.rating);
        updateText('.store-instagram-val', store.instagram);
        updateText('.store-instagram-detail-val', store.instagramDetail);
        updateText('.store-tiktok-val', store.tiktok);
        updateText('.store-facebook-val', store.facebook);
        updateText('.store-shopee-val', store.shopee);
        updateText('.store-products-val', store.products);
        updateText('.store-services-val', store.services);

        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = val || '';
        };
        
        setVal('editStoreName', store.name);
        setVal('editStoreBranch', store.branch);
        setVal('editStoreTagline', store.tagline);
        setVal('editStoreAddress', store.address);
        setVal('editStorePhone', store.phone);
        setVal('editStoreHours', store.hours);
        setVal('editStoreRating', store.rating);
        setVal('editStoreInstagram', store.instagram);
        setVal('editStoreInstagramDetail', store.instagramDetail);
        setVal('editStoreTiktok', store.tiktok);
        setVal('editStoreFacebook', store.facebook);
        setVal('editStoreShopee', store.shopee);
        setVal('editStoreProducts', store.products);
        setVal('editStoreServices', store.services);
    }

    const saveBtn = document.getElementById('btnSaveStoreProfile');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const getVal = (id) => {
                const el = document.getElementById(id);
                return el ? el.value : '';
            };
            const updated = {
                name: getVal('editStoreName'),
                branch: getVal('editStoreBranch'),
                tagline: getVal('editStoreTagline'),
                address: getVal('editStoreAddress'),
                phone: getVal('editStorePhone'),
                hours: getVal('editStoreHours'),
                rating: getVal('editStoreRating'),
                instagram: getVal('editStoreInstagram'),
                instagramDetail: getVal('editStoreInstagramDetail'),
                tiktok: getVal('editStoreTiktok'),
                facebook: getVal('editStoreFacebook'),
                shopee: getVal('editStoreShopee'),
                products: getVal('editStoreProducts'),
                services: getVal('editStoreServices')
            };
            localStorage.setItem('store_profile', JSON.stringify(updated));
            loadStoreProfile();

            // Hide modal
            const modalEl = document.getElementById('modalEditStore');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }

    loadStoreProfile();

    // 2. Sales Chart AJAX Filter Handler (Dynamic)
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    let salesChart;

    const filterYear = document.getElementById('filterSalesYear');
    const filterMonth = document.getElementById('filterSalesMonth');

    function fetchSalesData() {
        const year = filterYear.value;
        const month = filterMonth.value;
        let url = `{{ route('dashboard.sales-data') }}?year=${year}`;
        if (month) {
            url += `&month=${month}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(resData => {
                const labels = resData.labels;
                const dataPoints = resData.data;

                if (salesChart) {
                    salesChart.destroy();
                }

                salesChart = new Chart(ctxSales, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Produk Terjual (pcs)',
                            data: dataPoints,
                            backgroundColor: 'rgba(91, 141, 238, 0.85)',
                            hoverBackgroundColor: 'rgba(59, 109, 217, 0.95)',
                            borderRadius: 6,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(100, 110, 140, 0.08)'
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Error loading sales chart data:", err));
    }

    if (filterYear && filterMonth) {
        filterYear.addEventListener('change', fetchSalesData);
        filterMonth.addEventListener('change', fetchSalesData);
        fetchSalesData();
    }

    // 3. Status Doughnut Chart (Static representation)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Warning', 'Order'],
            datasets: [{
                data: [
                    {{ $statusOverview['aman'] ?? 0 }},
                    {{ $statusOverview['warning'] ?? 0 }},
                    {{ $statusOverview['order'] ?? 0 }}
                ],
                backgroundColor: ['#10b981', '#fbbf24', '#f87171'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = context.raw;
                            const dataset = context.dataset;
                            const total = dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                            return label + value + ' (' + percentage + ')';
                        }
                    }
                }
            },
            cutout: '72%'
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Clean up pagination in ROP recommendations table */
    .rop-pagination nav .flex-sm-fill.d-sm-flex > div:first-child {
        display: none !important;
    }
    .rop-pagination nav .flex-sm-fill.d-sm-flex {
        justify-content: flex-end !important;
        margin: 0;
    }
    .rop-pagination .pagination {
        margin-bottom: 0;
    }
    .rop-pagination nav {
        width: 100%;
    }
</style>
@endpush
