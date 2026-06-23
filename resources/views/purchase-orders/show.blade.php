@extends('layouts.app')
@section('title', 'Detail Pemesanan')
@section('page-title', 'Detail Pemesanan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1">Pemesanan #{{ $pemesanan_supplier->id }}</h4>
            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($pemesanan_supplier->tanggal_pemesanan)->format('d F Y') }}</p>
        </div>
        @php $statusColors = ['draft'=>'secondary','dipesan'=>'primary','diterima'=>'success','dibatalkan'=>'danger']; @endphp
        <span class="badge bg-{{ $statusColors[$pemesanan_supplier->status_pemesanan] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucfirst($pemesanan_supplier->status_pemesanan) }}</span>
    </div>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Detail Pemesanan</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted" style="width:35%">Produk</td><td class="fw-semibold">{{ $pemesanan_supplier->product->nama_produk ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Supplier</td><td class="fw-semibold">{{ $pemesanan_supplier->supplier->nama_supplier ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Jumlah Pesan</td><td class="fw-bold fs-5">{{ number_format($pemesanan_supplier->jumlah_pesan) }}</td></tr>
                        <tr><td class="text-muted">Tanggal Pemesanan</td><td>{{ \Carbon\Carbon::parse($pemesanan_supplier->tanggal_pemesanan)->format('d F Y') }}</td></tr>
                        <tr><td class="text-muted">Status</td><td><span class="badge bg-{{ $statusColors[$pemesanan_supplier->status_pemesanan] ?? 'secondary' }}">{{ ucfirst($pemesanan_supplier->status_pemesanan) }}</span></td></tr>
                        <tr><td class="text-muted">Keterangan</td><td>{{ $pemesanan_supplier->keterangan ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Dibuat Oleh</td><td>{{ $pemesanan_supplier->user->name ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        @if(auth()->user()->role !== 'owner')
        <div class="col-md-5 mb-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title"><i class="bi bi-gear me-2"></i>Aksi</h5></div>
                <div class="card-body">
                    @if($pemesanan_supplier->status_pemesanan === 'draft')
                        <form action="{{ route('pemesanan.update-status', $pemesanan_supplier) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="dipesan">
                            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-send me-2"></i>Tandai Dipesan</button>
                        </form>
                        <form action="{{ route('pemesanan.update-status', $pemesanan_supplier) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Yakin ingin membatalkan?')"><i class="bi bi-x-circle me-2"></i>Batalkan</button>
                        </form>
                    @elseif($pemesanan_supplier->status_pemesanan === 'dipesan')
                        <form action="{{ route('pemesanan.update-status', $pemesanan_supplier) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="diterima">
                            <button type="submit" class="btn btn-success w-100 mb-2"><i class="bi bi-check-circle me-2"></i>Tandai Diterima</button>
                        </form>
                        <form action="{{ route('pemesanan.update-status', $pemesanan_supplier) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Yakin ingin membatalkan?')"><i class="bi bi-x-circle me-2"></i>Batalkan</button>
                        </form>
                    @elseif($pemesanan_supplier->status_pemesanan === 'diterima')
                        <form action="{{ route('pemesanan.create-incoming', $pemesanan_supplier) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-box-arrow-in-down me-2"></i>Buat Barang Masuk Otomatis</button>
                        </form>
                        <p class="text-muted small mt-2 mb-0">Stok akan otomatis ditambahkan ke produk terkait.</p>
                    @else
                        <div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>Pemesanan ini telah dibatalkan.</div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
