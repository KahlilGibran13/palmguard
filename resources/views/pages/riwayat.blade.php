@extends('layouts.app')
@section('title', 'Riwayat Deteksi')
@section('content')

{{-- ═══ STAT MINI ═══ --}}
<div class="stats-mini">
    <div class="stat-mini-item">
        <span class="stat-mini-val">{{ $total }}</span>
        <span class="stat-mini-lbl">Total</span>
    </div>
    <div class="stat-mini-item" style="color:#6fcf97">
        <span class="stat-mini-val">{{ $totalSehat }}</span>
        <span class="stat-mini-lbl">Sehat</span>
    </div>
    <div class="stat-mini-item" style="color:#e05252">
        <span class="stat-mini-val">{{ $totalSakit }}</span>
        <span class="stat-mini-lbl">Sakit</span>
    </div>
    <div class="stat-mini-item" style="color:#f5a623">
        <span class="stat-mini-val">{{ $totalWaspada }}</span>
        <span class="stat-mini-lbl">Waspada</span>
    </div>
</div>

{{-- ═══ TOOLBAR ═══ --}}
<div class="card" style="margin-bottom:1.25rem">
    <div class="toolbar">
        <form method="GET" action="{{ route('riwayat') }}" style="flex:1;display:flex;gap:0.5rem">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari penyakit, lokasi..."
                style="flex:1;background:#0a1a0e;border:1px solid #1e3a28;color:#e0e0e0;padding:0.55rem 0.9rem;border-radius:8px;font-size:0.88rem">
            <button type="submit" class="btn-search">🔍</button>
        </form>
        <a href="{{ route('riwayat.export') }}" class="btn-export">📥 Export CSV</a>

        @canany(['manage-by-admin', 'manage-by-operator'])
        <button class="btn-danger" onclick="hapusAll()">🗑️ Hapus Semua</button>
        @endcanany
    </div>
</div>

{{-- ═══ TABEL RIWAYAT ═══ --}}
<div class="card">
    @forelse($detections as $d)
    <div class="riwayat-item" id="item-{{ $d->id }}">

        {{-- Foto Daun --}}
        <div class="riwayat-foto-wrap">
            <img src="{{ Storage::url($d->image_path) }}" class="riwayat-foto" alt="Daun">
            <span class="source-badge">{{ $d->source === 'kamera' ? '📷' : '📁' }}</span>
        </div>

        {{-- Info Utama --}}
        <div class="riwayat-main">
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                @if($d->pohon)
                <span class="disease-name">{{ $d->pohon->nama_pohon }}</span>
                @endif
                <span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span>
                <span class="conf-badge">{{ number_format($d->confidence, 1) }}%</span>
            </div>

            <div class="meta-row">
                <span>{{ $d->created_at->format('d M Y, H:i') }}</span>
                @if($d->disease_name)
                <span>{{ $d->disease_name }}</span>
                @endif
            </div>
        </div>

        {{-- Aksi --}}
        <div class="riwayat-aksi">
            <a href="{{ route('detect.pdf', $d->id) }}"
               class="btn-aksi btn-pdf" title="Unduh PDF">
                📄 Unduh PDF
            </a>
            @canany(['manage-by-admin', 'manage-by-operator'])
            <button class="btn-aksi btn-del"
                onclick="hapusSatu({{ $d->id }})"
                title="Hapus">
                🗑️ Hapus
            </button>
            @endcanany
            <a href="{{ route('detect.show', $d->id) }}"
               class="btn-aksi btn-pdf"
               style="text-decoration:none;font-size:0.78rem;background:#1a3d22;color:#4a9eff;border:1px solid #4a9eff;padding:0.35rem 0.85rem;border-radius:6px;display:inline-block">
                🔍 Detail
            </a>
        </div>

    </div>
    @empty
    <div class="empty-state">
        <div style="font-size:2.5rem">📋</div>
        <p style="color:#666;margin:0.5rem 0 0">Belum ada riwayat deteksi</p>
        <a href="{{ route('dashboard') }}" style="color:#6fcf97;font-size:0.85rem">Mulai deteksi →</a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($detections->hasPages())
<div class="pagination-wrapper">

    <div class="pagination-info">
        Showing {{ $detections->firstItem() }}
        to {{ $detections->lastItem() }}
        of {{ $detections->total() }} results
    </div>

    <div class="pagination-links">

        {{-- Previous --}}
        @if ($detections->onFirstPage())
            <span class="page-btn disabled">‹ Previous</span>
        @else
            <a href="{{ $detections->previousPageUrl() }}" class="page-btn">
                ‹ Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($detections->getUrlRange(1, $detections->lastPage()) as $page => $url)

            @if ($page == $detections->currentPage())
                <span class="page-number active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page-number">{{ $page }}</a>
            @endif

        @endforeach

        {{-- Next --}}
        @if ($detections->hasMorePages())
            <a href="{{ $detections->nextPageUrl() }}" class="page-btn">
                Next ›
            </a>
        @else
            <span class="page-btn disabled">Next ›</span>
        @endif

    </div>

    <div class="pagination-page">
        Halaman {{ $detections->currentPage() }}
        dari {{ $detections->lastPage() }}
    </div>

</div>
@endif

<style>
.stats-mini{display:flex;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap}
.stat-mini-item{background:#0d1f12;border:1px solid #1e3a28;border-radius:10px;padding:0.75rem 1.25rem;display:flex;flex-direction:column;align-items:center;min-width:80px}
.stat-mini-val{font-size:1.5rem;font-weight:700;color:#e0e0e0}
.stat-mini-lbl{font-size:0.75rem;color:#666;margin-top:0.1rem}
/* PAGINATION */
.pagination-wrapper{
    margin-top:30px;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:14px;
}

.pagination-info{
    color:#8b8b8b;
    font-size:0.85rem;
}

.pagination-links{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    justify-content:center;
}

.page-btn,
.page-number{
    min-width:42px;
    height:42px;
    padding:0 16px;
    border-radius:10px;
    text-decoration:none;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#101c13;
    border:1px solid #1e3a28;
    color:#d9d9d9;
    font-size:0.9rem;
    font-weight:600;
    transition:all .25s ease;
}

.page-btn:hover,
.page-number:hover{
    background:#16301f;
    border-color:#2d7a4f;
    color:#6fcf97;
}

.page-number.active{
    background:#6d28d9;
    border-color:#6d28d9;
    color:white;
    box-shadow:0 0 15px rgba(109,40,217,.35);
}

.page-btn.disabled{
    opacity:.4;
    pointer-events:none;
}

.pagination-page{
    color:#666;
    font-size:0.8rem;
}

/* FIX SVG ICON PAGINATION BESAR */
.pagination svg{
    width:18px !important;
    height:18px !important;
}
.toolbar{display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap}
.btn-search{background:#1a3d22;border:1px solid #2d7a4f;color:#6fcf97;padding:0.55rem 0.9rem;border-radius:8px;cursor:pointer;font-size:0.9rem}
.btn-export{background:#1a3d22;border:1px solid #2d7a4f;color:#6fcf97;padding:0.55rem 0.9rem;border-radius:8px;cursor:pointer;font-size:0.85rem;text-decoration:none;white-space:nowrap}
.btn-danger{background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.3);color:#e05252;padding:0.55rem 0.9rem;border-radius:8px;cursor:pointer;font-size:0.85rem;white-space:nowrap}
.riwayat-item{display:flex;gap:1rem;align-items:flex-start;padding:1rem 0;border-bottom:1px solid #0d1f12}
.riwayat-item:last-child{border-bottom:none}
.riwayat-foto-wrap{position:relative;flex-shrink:0}
.riwayat-foto{width:72px;height:72px;object-fit:cover;border-radius:10px;border:2px solid #1e3a28;display:block}
.source-badge{position:absolute;bottom:-4px;right:-4px;background:#060f0a;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;border:1px solid #1e3a28}
.riwayat-main{flex:1;min-width:0}
.disease-name{color:#e0e0e0;font-weight:700;font-size:0.95rem}
.conf-badge{background:#1a3d22;color:#6fcf97;padding:0.15rem 0.5rem;border-radius:20px;font-size:0.75rem;font-weight:600}
.meta-row{display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:0.3rem;color:#666;font-size:0.78rem}
.riwayat-aksi{display:flex;flex-direction:column;gap:0.4rem;flex-shrink:0}
.btn-aksi{padding:0.4rem 0.75rem;border-radius:8px;cursor:pointer;font-size:0.78rem;font-weight:600;text-decoration:none;text-align:center;border:none;white-space:nowrap}
.btn-pdf{background:#1a3d22;color:#6fcf97;border:1px solid #2d7a4f}
.btn-del{background:rgba(224,82,82,0.1);color:#e05252;border:1px solid rgba(224,82,82,0.3)}
.btn-del:hover{background:rgba(224,82,82,0.2)}
.badge{padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:600}
.badge-sehat{background:rgba(111,207,151,0.15);color:#6fcf97}
.badge-sakit{background:rgba(224,82,82,0.15);color:#e05252}
.badge-waspada{background:rgba(245,166,35,0.15);color:#f5a623}
.empty-state{text-align:center;padding:3rem 1rem}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function hapusSatu(id) {
    const konfirmasi = await Swal.fire({
        text: 'Hapus data ini?',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        background: '#0d1f12',
        color: '#e0e0e0',
        confirmButtonColor: '#e05252',
        cancelButtonColor: '#1e3a28',
        buttonsStyling: true,
        width: '300px',
    });

    if (konfirmasi.isConfirmed) {
        const res = await fetch(`/detect/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const item = document.getElementById('item-' + id);
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            setTimeout(() => item.remove(), 300);
        }
    }
}

async function hapusAll() {
    const konfirmasi = await Swal.fire({
        text: 'Hapus semua data riwayat?',
        showCancelButton: true,
        confirmButtonText: 'Hapus Semua',
        cancelButtonText: 'Batal',
        background: '#0d1f12',
        color: '#e0e0e0',
        confirmButtonColor: '#e05252',
        cancelButtonColor: '#1e3a28',
        width: '300px',
    });

    if (konfirmasi.isConfirmed) {
        const res = await fetch('/detect-all', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) location.reload();
    }
}
</script>
@endsection