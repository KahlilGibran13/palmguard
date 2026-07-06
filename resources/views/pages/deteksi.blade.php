@extends('layouts.app')
@section('title', 'Deteksi Penyakit')
@section('content')

@if($latest)
@php
    $badgeColor = $latest->status === 'sehat' ? '#1DB954' : ($latest->status === 'sakit' ? '#e05252' : '#f5a623');
    $statusBg   = $latest->status === 'sehat' ? 'rgba(29,185,84,0.15)' : ($latest->status === 'sakit' ? 'rgba(224,82,82,0.15)' : 'rgba(245,166,35,0.15)');
@endphp

<div class="card" style="margin-bottom:1.5rem">

    {{-- HEADER --}}
    <div class="deteksi-header">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <div style="width:40px;height:40px;background:var(--green-dim);border:1px solid var(--green);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"></div>
            <div>
                <h3 style="color:var(--text);font-size:1rem;font-weight:700;margin:0">Hasil Analisis Terakhir</h3>
                <span style="color:var(--muted);font-size:0.75rem">{{ $latest->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <span style="background:{{ $statusBg }};color:{{ $badgeColor }};padding:0.35rem 1rem;border-radius:50px;font-size:0.8rem;font-weight:700;border:1px solid {{ $badgeColor }}40;white-space:nowrap">
            {{ strtoupper($latest->status) }}
        </span>
    </div>

    {{-- CONTENT --}}
    <div class="deteksi-content">

        {{-- FOTO --}}
        <div>
            <img src="{{ Storage::url($latest->image_path) }}"
                 style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:1px solid var(--border);display:block">

            <div style="margin-top:1rem;background:var(--bg3);border-radius:10px;padding:0.75rem;display:flex;flex-direction:column;gap:0.75rem">

                {{-- Akurasi (Confidence) --}}
                <div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:0.75rem;color:var(--muted)">Akurasi</span>
                        <span style="color:var(--green);font-weight:700">{{ number_format($latest->confidence,1) }}%</span>
                    </div>
                    <div style="background:var(--border);height:6px;border-radius:10px;margin-top:5px">
                        <div style="width:{{ $latest->confidence }}%;height:100%;background:var(--green);border-radius:10px"></div>
                    </div>
                </div>

                {{-- Precision --}}
                <div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:0.75rem;color:var(--muted)">Precision</span>
                        <span style="color:var(--green);font-weight:700">66.2%</span>
                    </div>
                    <div style="background:var(--border);height:6px;border-radius:10px;margin-top:5px">
                        <div style="width:66.2%;height:100%;background:var(--green);border-radius:10px"></div>
                    </div>
                </div>

                {{-- Recall --}}
                <div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:0.75rem;color:var(--muted)">Recall</span>
                        <span style="color:var(--green);font-weight:700">66.3%</span>
                    </div>
                    <div style="background:var(--border);height:6px;border-radius:10px;margin-top:5px">
                        <div style="width:66.3%;height:100%;background:var(--green);border-radius:10px"></div>
                    </div>
                </div>

                {{-- F1-Score --}}
                <div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:0.75rem;color:var(--muted)">F1-Score</span>
                        <span style="color:var(--green);font-weight:700">66.2%</span>
                    </div>
                    <div style="background:var(--border);height:6px;border-radius:10px;margin-top:5px">
                        <div style="width:66.2%;height:100%;background:var(--green);border-radius:10px"></div>
                    </div>
                </div>

                {{-- mAP@50 --}}
                <div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:0.75rem;color:var(--muted)">mAP@50</span>
                        <span style="color:var(--green);font-weight:700">66.4%</span>
                    </div>
                    <div style="background:var(--border);height:6px;border-radius:10px;margin-top:5px">
                        <div style="width:66.4%;height:100%;background:var(--green);border-radius:10px"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- DETAIL --}}
        <div style="display:flex;flex-direction:column;gap:0.75rem;min-width:0">
            <div class="modern-card">
                <span class="label">Nama Penyakit</span>
                <div class="title">{{ $latest->disease_name }}</div>
            </div>

            <div class="modern-grid-2">
                <div class="modern-card">
                    <span class="label">Status</span>
                    <span class="badge-modern" style="background:{{ $statusBg }};color:{{ $badgeColor }}">
                        {{ ucfirst($latest->status) }}
                    </span>
                </div>
                <div class="modern-card">
                    <span class="label">Sumber</span>
                    <div class="value">{{ ucfirst($latest->source) }}</div>
                </div>
            </div>

            <div class="modern-grid-3">
                <div class="modern-card">
                    <span class="label"> Nama Pohon</span>
                    <div class="value" style="overflow-wrap:break-word">{{ $latest->foto_lokasi_nama ?? '-' }}</div>
                </div>
                <div class="modern-card">
                    <span class="label">Lat</span>
                    <div class="value mono" style="overflow-wrap:break-word">{{ $latest->latitude ?? '-' }}</div>
                </div>
                <div class="modern-card">
                    <span class="label">Lng</span>
                    <div class="value mono" style="overflow-wrap:break-word">{{ $latest->longitude ?? '-' }}</div>
                </div>
            </div>

            @if($latest->description)
            <div class="modern-card">
                <span class="label">Deskripsi</span>
                <p class="desc">{{ $latest->description }}</p>
            </div>
            @endif

        </div>
    </div>

    {{-- PETA (FULL WIDTH SESUAI GAMBAR) --}}
    @if($latest->latitude && $latest->longitude)
    <div style="margin-top:1rem">
        <div style="color:var(--green);font-weight:600;margin-bottom:0.5rem">📍 Peta Lokasi</div>
        <div id="map-foto" style="width:100%;height:300px;border-radius:12px;border:1px solid var(--border)"></div>
    </div>
    @endif

</div>

@endif

{{-- KATALOG --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Katalog Penyakit</h3>
    </div>

    <div class="katalog-grid">
        @foreach($katalog as $k)
        <div class="katalog-card">
            <div class="katalog-header">
                <span>{{ $k['nama_penyakit'] }}</span>
                <span class="badge badge-{{ strtolower($k['status']) }}">{{ $k['status'] }}</span>
            </div>
            <ul class="katalog-ciri">
                @if(isset($k->ciriCiri) && count($k->ciriCiri) > 0)
                    @foreach($k['ciriCiri'] as $c)
                    <li>{{ $c['ciri'] }}</li>
                    @endforeach
                @else
                    <li>Tidak ada ciri-ciri tercatat</li>
                @endif
            </ul>
        </div>
        @endforeach
    </div>
</div>

{{-- LEAFLET --}}
@if($latest && $latest->latitude && $latest->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map-foto').setView([{{ $latest->latitude }}, {{ $latest->longitude }}], 18);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 19
    }).addTo(map);

    const status = "{{ $latest->status }}";
    const warna = status === 'sakit' ? '#e05252' : status === 'waspada' ? '#f5a623' : '#2d7a4f';

    const icon = L.divIcon({
        html: `<div style="
            background:${warna};
            width:14px;height:14px;
            border-radius:50%;
            border:2px solid #fff;
            box-shadow:0 0 8px ${warna}
        "></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        className: ''
    });

    L.marker([{{ $latest->latitude }}, {{ $latest->longitude }}], { icon })
        .addTo(map)
        .bindPopup('{{ $latest->disease_name }}')
        .openPopup();

    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif

<style>
.modern-card{background:#161616;border:1px solid rgba(255,255,255,0.05);border-radius:12px;padding:0.9rem;min-width:0;}
.modern-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;}
.modern-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;}
.label{font-size:0.7rem;color:#9ca3af;}
.title{font-size:1.2rem;font-weight:700;color:#fff;overflow-wrap:break-word;}
.value{font-size:0.9rem;color:#e5e7eb;}
.desc{font-size:0.8rem;color:#9ca3af;}
.badge-modern{padding:4px 10px;border-radius:999px;font-size:0.75rem;display:inline-block;margin-top:2px;}
.katalog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:1rem;}
.katalog-card{background:#161616;border:1px solid #222;border-radius:12px;padding:1rem;}

/* ── Header hasil analisis: wrap di layar sempit ── */
.deteksi-header{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);
    flex-wrap:wrap;gap:0.75rem;
}

/* ── Foto + Detail: 2 kolom di desktop, stack di mobile/tablet ── */
.deteksi-content{
    display:grid;
    grid-template-columns:240px 1fr;
    gap:1.5rem;
    margin-bottom:1.5rem;
}

@media (max-width: 900px) {
    .deteksi-content{
        grid-template-columns:1fr;
    }
}

@media (max-width: 480px) {
    .modern-grid-2{
        grid-template-columns:1fr 1fr;
        gap:0.5rem;
    }
    .modern-grid-3{
        grid-template-columns:1fr;
        gap:0.5rem;
    }
    .deteksi-header{
        align-items:flex-start;
    }
    .katalog-grid{
        grid-template-columns:1fr;
    }
}
</style>

@endsection