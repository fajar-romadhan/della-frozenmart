@extends('layouts.app')
@section('title', 'Tambah Barang Masuk')
@section('page-title', 'Tambah Barang Masuk')

@section('content')
<style>
    /* Premium Styling Overrides */
    .page-title-main {
        font-family: var(--font-display);
        font-weight: 700;
        color: #0f172a;
        font-size: 1.5rem;
    }
    
    .breadcrumb-item a {
        color: var(--color-primary);
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
    }
    
    .breadcrumb-item a:hover {
        color: var(--color-accent);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-card);
        transition: var(--transition);
        margin-bottom: 24px;
    }
    
    .glass-card:hover {
        box-shadow: var(--shadow-card-hover);
        background: rgba(255, 255, 255, 0.8) !important;
    }

    .card-header-custom {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-title {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
        margin: 0;
    }

    .card-body-custom {
        padding: 24px;
    }

    .form-label-custom {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 0.82rem;
        color: #475569;
        margin-bottom: 6px;
    }

    .form-control-custom {
        font-family: var(--font-body);
        font-size: 0.88rem;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background-color: rgba(255, 255, 255, 0.6);
        transition: all 0.2s;
    }

    .form-control-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        background-color: #ffffff;
        outline: none;
    }
    .form-select-custom {
        appearance: none !important;
        -webkit-appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 12px 12px !important;
        padding-right: 40px !important;
    }

    .form-select-custom:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-position: right 14px center !important;
    }

    .form-select-searchable {
        background-image: none !important;
        padding-right: 40px !important;
    }

    /* Product selector wrapper with search icon */
    .product-select-wrapper {
        position: relative;
    }

    .product-select-wrapper .select-search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
        font-size: 1.1rem;
    }
    
    /* Input suffix styling */
    .input-suffix-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-suffix-wrapper input {
        padding-right: 45px;
    }

    .input-suffix {
        position: absolute;
        right: 14px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        pointer-events: none;
    }

    .btn-add-item {
        background-color: transparent;
        color: #2563eb;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 20px;
        border-radius: 8px;
        border: 1.5px solid #2563eb;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-add-item:hover {
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* Table visual upgrades */
    .list-table-card {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-card);
        background: #ffffff;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .list-table {
        width: 100%;
        margin-bottom: 0;
        table-layout: auto;
    }

    .list-table th {
        font-weight: 600;
        font-size: 0.72rem;
        color: #ffffff;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: none;
        padding: 10px 8px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }

    .list-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .list-table tbody tr {
        transition: background-color 0.2s;
    }

    .list-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .btn-delete-item {
        color: #ef4444;
        background: transparent;
        border: none;
        padding: 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-delete-item:hover {
        background-color: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    /* Table Footer Summary */
    .table-summary-row {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-group {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
    }

    .summary-label {
        font-weight: 500;
        color: #64748b;
    }

    .summary-value {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.05rem;
    }

    .keterangan-container {
        position: relative;
    }

    .char-counter {
        position: absolute;
        right: 14px;
        bottom: 10px;
        font-size: 0.72rem;
        color: #94a3b8;
        pointer-events: none;
    }

    .lock-indicator {
        font-size: 0.78rem;
        color: #f59e0b;
        display: none;
        align-items: center;
        gap: 4px;
        margin-top: 6px;
        font-weight: 500;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header Section with Breadcrumb --}}
    <div class="mb-4">
        <h1 class="page-title-main mb-1">Tambah Barang Masuk</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.85rem; font-weight: 500;">
                <li class="breadcrumb-item"><a href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #64748b;">Tambah Barang Masuk</li>
            </ol>
        </nav>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
            <h6 class="fw-bold mb-2"><i class="ph ph-warning-circle-fill me-2" style="vertical-align: middle;"></i>Terdapat kesalahan penginputan:</h6>
            <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="formIncomingGoods" action="{{ route('barang-masuk.store') }}" method="POST">
        @csrf
        
        {{-- Section 1: Informasi Transaksi --}}
        <div class="card glass-card">
            <div class="card-header-custom">
                <i class="ph ph-info text-primary" style="font-size: 1.25rem;"></i>
                <h5 class="card-header-title">Informasi Transaksi</h5>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tanggal_masuk" class="form-label-custom">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control form-control-custom @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                        @error('tanggal_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="lock-indicator" id="dateLockIndicator">
                            <i class="ph ph-lock-key"></i> Kunci aktif karena terdapat item di tabel.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="id_lokasi" class="form-label-custom">ID Lokasi <span class="text-danger">*</span></label>
                        <select name="id_lokasi" id="id_lokasi" class="form-select form-control-custom form-select-custom @error('id_lokasi') is-invalid @enderror" required>
                            <option value="">Pilih Lokasi</option>
                            <option value="FRZ-01" {{ old('id_lokasi') == 'FRZ-01' ? 'selected' : '' }}>FRZ-01 (Freezer 1)</option>
                            <option value="FRZ-02" {{ old('id_lokasi') == 'FRZ-02' ? 'selected' : '' }}>FRZ-02 (Freezer 2)</option>
                            <option value="FRZ-03" {{ old('id_lokasi') == 'FRZ-03' ? 'selected' : '' }}>FRZ-03 (Freezer 3)</option>
                            <option value="RAK-A" {{ old('id_lokasi') == 'RAK-A' ? 'selected' : '' }}>RAK-A (Rak A)</option>
                            <option value="RAK-B" {{ old('id_lokasi') == 'RAK-B' ? 'selected' : '' }}>RAK-B (Rak B)</option>
                            <option value="RAK-C" {{ old('id_lokasi') == 'RAK-C' ? 'selected' : '' }}>RAK-C (Rak C)</option>
                            <option value="RAK-D" {{ old('id_lokasi') == 'RAK-D' ? 'selected' : '' }}>RAK-D (Rak D)</option>
                        </select>
                        @error('id_lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="lock-indicator" id="locationLockIndicator">
                            <i class="ph ph-lock-key"></i> Kunci aktif karena terdapat item di tabel.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Barang Masuk --}}
        <div class="card glass-card">
            <div class="card-header-custom">
                <i class="ph ph-package text-primary" style="font-size: 1.25rem;"></i>
                <h5 class="card-header-title">Detail Barang Masuk</h5>
            </div>
            <div class="card-body-custom">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="supplier_id_select" class="form-label-custom">Supplier <span class="text-danger">*</span></label>
                        <select id="supplier_id_select" class="form-select form-control-custom form-select-custom">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="product_id_select" class="form-label-custom">Produk <span class="text-danger">*</span></label>
                        <div class="product-select-wrapper">
                            <select id="product_id_select" class="form-select form-control-custom form-select-searchable" style="padding-right: 40px;">
                                <option value="">Pilih Produk</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-kode="{{ $p->kode_produk }}" data-satuan="{{ $p->satuan }}">
                                        {{ $p->kode_produk }} - {{ $p->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ph ph-magnifying-glass select-search-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="jumlah_input" class="form-label-custom">Jumlah (pcs) <span class="text-danger">*</span></label>
                        <div class="input-suffix-wrapper">
                            <input type="number" id="jumlah_input" class="form-control form-control-custom w-100" min="1" placeholder="Masukkan jumlah">
                            <span class="input-suffix">pcs</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="harga_beli_input" class="form-label-custom">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" id="harga_beli_input" class="form-control form-control-custom" min="0" placeholder="Masukkan harga satuan">
                    </div>
                </div>
                
                <div class="row g-3 d-none">
                    <div class="col-md-12 keterangan-container">
                        <label for="keterangan_input" class="form-label-custom">Keterangan</label>
                        <input type="text" id="keterangan_input" class="form-control form-control-custom" maxlength="255" placeholder="Masukkan keterangan (opsional)" style="padding-right: 60px;">
                        <span class="char-counter" id="charCounter">0/255</span>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" id="btnAddItem" class="btn-add-item">
                        <i class="ph ph-plus bold" style="font-size: 1rem; vertical-align: middle;"></i> Tambah ke Tabel
                    </button>
                </div>
            </div>
        </div>

        {{-- Section 3: Daftar Barang Masuk --}}
        <div class="card list-table-card">
            <div class="card-header-custom bg-light">
                <i class="ph ph-list-bullets text-primary" style="font-size: 1.25rem;"></i>
                <h5 class="card-header-title">Daftar Barang Masuk</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle list-table" id="tableTempItems">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th class="text-center">TANGGAL</th>
                            <th>ID PRODUK</th>
                            <th>NAMA PRODUK</th>
                            <th>NAMA SUPPLIER</th>
                            <th class="text-center">JUMLAH (PCS)</th>
                            <th class="text-end">HARGA SATUAN (RP)</th>
                            <th class="text-end">TOTAL (RP)</th>
                            <th class="text-center">ID LOKASI</th>
                            <th class="text-center" style="width: 60px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tempItemsBody">
                        <tr id="emptyRowPlaceholder">
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="ph ph-package fs-1 d-block mb-2 text-muted" style="opacity: 0.4;"></i>
                                <div class="fw-bold" style="font-size: 0.95rem; color: #64748b;">Belum ada data</div>
                                <small style="font-size: 0.8rem; color: #94a3b8;">Tambahkan barang untuk melihat daftar di sini.</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            {{-- Summary Footer inside the Card --}}
            <div class="table-summary-row">
                <div class="summary-group">
                    <span class="summary-label">Total Item</span>
                    <span class="summary-value" id="totalItemsText">0</span>
                </div>
                <div class="summary-group">
                    <span class="summary-label">Total (Rp)</span>
                    <span class="summary-value" id="totalNilaiText">0</span>
                </div>
            </div>
        </div>

        {{-- Hidden Container for array submission --}}
        <div id="hiddenInputsContainer"></div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-light shadow-sm" style="border-radius: 8px; font-weight: 600; padding: 10px 24px; border: 1px solid #cbd5e1;">Batal</a>
            <button type="submit" id="btnSubmitForm" class="btn btn-primary shadow-sm" style="border-radius: 8px; font-weight: 600; padding: 10px 24px; background-color: #2563eb; border-color: #2563eb;">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let items = [];

        // Header inputs
        const dateInput = document.getElementById('tanggal_masuk');
        const locationSelect = document.getElementById('id_lokasi');
        const dateLockIndicator = document.getElementById('dateLockIndicator');
        const locationLockIndicator = document.getElementById('locationLockIndicator');
        
        // Item detail inputs
        const supplierSelect = document.getElementById('supplier_id_select');
        const productSelect = document.getElementById('product_id_select');
        const qtyInput = document.getElementById('jumlah_input');
        const priceInput = document.getElementById('harga_beli_input');
        const notesInput = document.getElementById('keterangan_input');
        const charCounter = document.getElementById('charCounter');
        
        // Add action and body
        const btnAddItem = document.getElementById('btnAddItem');
        const tempItemsBody = document.getElementById('tempItemsBody');
        const emptyRowPlaceholder = document.getElementById('emptyRowPlaceholder');
        
        // Summary elements
        const totalItemsText = document.getElementById('totalItemsText');
        const totalNilaiText = document.getElementById('totalNilaiText');
        
        const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
        const form = document.getElementById('formIncomingGoods');
        
        // Character counter trigger
        notesInput.addEventListener('input', function() {
            charCounter.textContent = `${this.value.length}/255`;
        });

        // Format date from YYYY-MM-DD to DD/MM/YYYY for table view
        function formatDateToView(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return dateStr;
        }

        // Format currency helper
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Add Item Logic
        btnAddItem.addEventListener('click', function() {
            const transactionDate = dateInput.value;
            const locationId = locationSelect.value;
            
            const supplierId = supplierSelect.value;
            const supplierName = supplierSelect.options[supplierSelect.selectedIndex].text;
            
            const productId = productSelect.value;
            const productOpt = productSelect.options[productSelect.selectedIndex];
            const productName = productOpt ? productOpt.text : '';
            const productCode = productOpt ? productOpt.getAttribute('data-kode') : '';
            
            const qty = parseInt(qtyInput.value);
            const price = parseFloat(priceInput.value);
            const notes = notesInput.value.trim();

            // Form-level validation
            if (!transactionDate) {
                alert('Silakan pilih tanggal transaksi terlebih dahulu.');
                dateInput.focus();
                return;
            }
            if (!locationId) {
                alert('Silakan pilih lokasi penyimpanan terlebih dahulu.');
                locationSelect.focus();
                return;
            }
            
            // Item-level validation
            if (!supplierId) {
                alert('Silakan pilih supplier.');
                supplierSelect.focus();
                return;
            }
            if (!productId) {
                alert('Silakan pilih produk.');
                productSelect.focus();
                return;
            }
            if (isNaN(qty) || qty < 1) {
                alert('Jumlah unit minimal adalah 1.');
                qtyInput.focus();
                return;
            }
            if (isNaN(price) || price < 0) {
                alert('Harga satuan wajib diisi dan minimal Rp 0.');
                priceInput.focus();
                return;
            }

            // Push to state
            items.push({
                tanggal: transactionDate,
                id_lokasi: locationId,
                product_id: productId,
                product_name: productName,
                product_code: productCode,
                supplier_id: supplierId,
                supplier_name: supplierName,
                jumlah: qty,
                harga_beli: price,
                keterangan: notes
            });

            // Clear item inputs (keep date & location intact)
            supplierSelect.value = '';
            productSelect.value = '';
            qtyInput.value = '';
            priceInput.value = '';
            notesInput.value = '';
            charCounter.textContent = '0/255';

            // Refresh UI
            renderTable();
        });

        // Render Table & Populate Form Hidden Inputs
        function renderTable() {
            // Remove existing rows
            const rows = tempItemsBody.querySelectorAll('.item-row');
            rows.forEach(r => r.remove());

            if (items.length === 0) {
                emptyRowPlaceholder.style.display = '';
                
                // Unlock header fields
                dateInput.disabled = false;
                locationSelect.disabled = false;
                dateLockIndicator.style.display = 'none';
                locationLockIndicator.style.display = 'none';
            } else {
                emptyRowPlaceholder.style.display = 'none';
                
                // Lock header fields to ensure consistency
                dateInput.disabled = true;
                locationSelect.disabled = true;
                dateLockIndicator.style.display = 'inline-flex';
                locationLockIndicator.style.display = 'inline-flex';
            }

            let totalValue = 0;

            // Clear hidden inputs container
            hiddenInputsContainer.innerHTML = '';

            items.forEach((item, index) => {
                const totalRowPrice = item.jumlah * item.harga_beli;
                totalValue += totalRowPrice;

                // Create Table Row HTML matching columns
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.innerHTML = `
                    <td class="text-center fw-semibold text-muted">${index + 1}</td>
                    <td class="text-center nowrap">${formatDateToView(item.tanggal)}</td>
                    <td class="fw-semibold text-secondary text-uppercase">${item.product_code}</td>
                    <td class="fw-bold">${item.product_name.replace(item.product_code + ' - ', '')}</td>
                    <td class="fw-semibold text-secondary">${item.supplier_name}</td>
                    <td class="text-center fw-bold text-success">+${formatNumber(item.jumlah)}</td>
                    <td class="text-end fw-semibold">Rp ${formatNumber(item.harga_beli)}</td>
                    <td class="text-end fw-bold text-dark">Rp ${formatNumber(totalRowPrice)}</td>
                    <td class="text-center"><span class="badge bg-light text-secondary border fw-semibold">${item.id_lokasi}</span></td>
                    <td class="text-center">
                        <button type="button" class="btn-delete-item" data-index="${index}">
                            <i class="ph ph-trash" style="font-size: 1.15rem;"></i>
                        </button>
                    </td>
                `;

                // Bind delete event handler
                tr.querySelector('.btn-delete-item').addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    items.splice(idx, 1);
                    renderTable();
                });

                tempItemsBody.appendChild(tr);

                // Build hidden inputs for standard request post array
                const inputsHtml = `
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][supplier_id]" value="${item.supplier_id}">
                    <input type="hidden" name="items[${index}][jumlah]" value="${item.jumlah}">
                    <input type="hidden" name="items[${index}][harga_beli]" value="${item.harga_beli}">
                    <input type="hidden" name="items[${index}][keterangan]" value="${escapeHtml(item.keterangan)}">
                `;
                hiddenInputsContainer.insertAdjacentHTML('beforeend', inputsHtml);
            });

            // Update Summary Footer values
            totalItemsText.textContent = formatNumber(items.length);
            totalNilaiText.textContent = formatNumber(totalValue);
        }

        // Helper to escape HTML characters inside input values
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Unlock inputs before form submission so PHP backend receives them
        form.addEventListener('submit', function(e) {
            if (items.length === 0) {
                e.preventDefault();
                alert('Harap masukkan minimal 1 barang masuk ke dalam daftar sebelum menyimpan.');
                return;
            }
            
            // Enable inputs right before submit to ensure date & location values are posted
            dateInput.disabled = false;
            locationSelect.disabled = false;
        });

        // Recover from server validation failures if redirect back with old items array
        const oldItems = @json(old('items', []));
        const oldDate = "{{ old('tanggal_masuk') }}";
        const oldLocation = "{{ old('id_lokasi') }}";

        if (Array.isArray(oldItems) && oldItems.length > 0) {
            // Restore date and location selection
            if (oldDate) dateInput.value = oldDate;
            if (oldLocation) locationSelect.value = oldLocation;

            oldItems.forEach(item => {
                // Look up supplier name
                let supName = 'Supplier';
                for (let i = 0; i < supplierSelect.options.length; i++) {
                    if (supplierSelect.options[i].value == item.supplier_id) {
                        supName = supplierSelect.options[i].text;
                        break;
                    }
                }

                // Look up product name and code details
                let prodName = 'Produk';
                let prodCode = '';
                for (let i = 0; i < productSelect.options.length; i++) {
                    if (productSelect.options[i].value == item.product_id) {
                        const opt = productSelect.options[i];
                        prodName = opt.text;
                        prodCode = opt.getAttribute('data-kode') || '';
                        break;
                    }
                }

                items.push({
                    tanggal: dateInput.value,
                    id_lokasi: locationSelect.value,
                    product_id: item.product_id,
                    product_name: prodName,
                    product_code: prodCode,
                    supplier_id: item.supplier_id,
                    supplier_name: supName,
                    jumlah: parseInt(item.jumlah) || 0,
                    harga_beli: parseFloat(item.harga_beli) || 0,
                    keterangan: item.keterangan || ''
                });
            });
            renderTable();
        }
    });
</script>
@endsection
