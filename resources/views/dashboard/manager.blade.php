@extends('layouts.app')
@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary mb-1">Selamat datang kembali, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Ulasan operasional, analisis persediaan, dan safety stock</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-success">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Penjualan Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($totalPenjualanBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-primary">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-receipt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Transaksi</span>
                    <h3 class="stat-value mb-0">{{ number_format($jumlahTransaksiBulanIni ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-danger">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-shield-warning"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Harus Dipesan (Order)</span>
                    <h3 class="stat-value mb-0 text-danger">{{ $produkHarusDipesan->count() }} Produk</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-warning">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-clock" style="color: #1e293b;"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Terakhir Dianalisis</span>
                    <h3 class="stat-value mb-0" style="font-size: 1rem;">
                        {{ $analisisTerbaru->first() ? $analisisTerbaru->first()->created_at->diffForHumans() : '-' }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
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

            <!-- 2. Actionable Urgent Order List -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-danger"><i class="ph ph-bell-ringing text-danger me-1"></i> Produk Mendesak Harus Segera Dipesan</h5>
                    <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-sm btn-light py-1" style="font-size: 0.8rem">Semua Order</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Stok Saat Ini</th>
                                    <th class="text-center">Min. Stok</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkHarusDipesan->take(4) as $item)
                                    <tr>
                                        <td><span class="badge bg-light text-dark fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                        <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                                        <td class="text-center fw-bold text-danger">{{ number_format($item->stok_saat_ini, 0, ',', '.') }}</td>
                                        <td class="text-center text-muted">{{ number_format($item->product->stok_minimum ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('pemesanan-supplier.create', ['product_id' => $item->product_id]) }}" class="btn btn-sm btn-danger py-1" style="font-size: 0.78rem">
                                                <i class="ph ph-shopping-cart-simple"></i> Buat Order
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ph ph-check-circle text-success fs-1 mb-2"></i>
                                            <p class="mb-0 fw-semibold">Seluruh stok produk berada di atas batas aman.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Tables Row: Safety Stock & Updates -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="ph ph-chart-line-up text-primary me-1"></i> Analisis ROP Terbaru</h6>
                            <a href="{{ route('analisis.index') }}" class="btn btn-sm btn-light py-1" style="font-size: 0.78rem">Detail</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive border-0">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">ROP</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($analisisTerbaru->take(5) as $analisis)
                                            <tr>
                                                <td class="fw-semibold">{{ $analisis->product->nama_produk ?? '-' }}</td>
                                                <td class="text-center">{{ number_format($analisis->reorder_point, 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    @if($analisis->status_stok === 'Aman')
                                                        <span class="badge bg-success">Aman</span>
                                                    @elseif($analisis->status_stok === 'Warning')
                                                        <span class="badge bg-warning text-dark">Warning</span>
                                                    @else
                                                        <span class="badge bg-danger">Order</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Belum ada analisis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="ph ph-calendar-blank text-primary me-1"></i> Update Stok Terakhir</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive border-0">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Stok</th>
                                            <th>Diupdate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($produkStokDiperbarui->take(5) as $prod)
                                            <tr>
                                                <td class="fw-semibold">{{ $prod->nama_produk }}</td>
                                                <td class="text-center fw-bold">{{ number_format($prod->stok_saat_ini, 0, ',', '.') }}</td>
                                                <td>{{ $prod->updated_at->diffForHumans() }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
            <!-- 1. Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-lightning text-primary me-1"></i> Akses Cepat Manager</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('import-penjualan.index') }}" class="btn btn-outline-success text-start p-3 rounded-3 d-flex align-items-center">
                            <i class="ph ph-upload-simple fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Import Data Penjualan</h6>
                                <small class="text-muted">Import transaksi penjualan bulanan</small>
                            </div>
                        </a>
                        <a href="{{ route('import-faktur.index') }}" class="btn btn-outline-primary text-start p-3 rounded-3 d-flex align-items-center">
                            <i class="ph ph-file-earmark-spreadsheet fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Import Faktur Pembelian</h6>
                                <small class="text-muted">Upload faktur barang masuk</small>
                            </div>
                        </a>
                        <a href="{{ route('analisis.index') }}" class="btn btn-outline-info text-start p-3 rounded-3 d-flex align-items-center">
                            <i class="ph ph-graph fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Safety Stock & ROP</h6>
                                <small class="text-muted">Hitung safety stock & reorder point</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Store Profile Widget -->
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
                        <div class="store-products-services">
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
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Toko</label>
                            <input type="text" class="form-control" id="editStoreName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cabang / Unit</label>
                            <input type="text" class="form-control" id="editStoreBranch" required>
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
        branch: "Cabang 2 – Lawang Kidul (Cabang Baru)",
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
        updateText('.store-branch-val', store.branch);
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
    const ctx = document.getElementById('salesChart').getContext('2d');
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

                salesChart = new Chart(ctx, {
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
});
</script>
@endpush
