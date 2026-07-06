@extends('layouts.app')
@section('title', 'Detail Deteksi')
@section('content')

@php
    $badgeColor = $detection->status === 'sehat' ? '#1DB954' : ($detection->status === 'sakit' ? '#e05252' : '#f5a623');
    $statusBg   = $detection->status === 'sehat' ? 'rgba(29,185,84,0.15)' : ($detection->status === 'sakit' ? 'rgba(224,82,82,0.15)' : 'rgba(245,166,35,0.15)');
@endphp

<div class="card" style="margin-bottom:1.5rem">

    {{-- HEADER --}}
    <div class="detail-header">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <div style="width:40px;height:40px;background:var(--green-dim);border:1px solid var(--green);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"></div>
            <div>
                <h3 style="color:var(--text);font-size:1rem;font-weight:700;margin:0">Detail Hasil Deteksi</h3>
                <span style="color:var(--muted);font-size:0.75rem;font-family:'IBM Plex Mono',monospace">
                    {{ $detection->created_at->format('d M Y, H:i') }}
                </span>
            </div>
        </div>
        <span style="background:{{ $statusBg }};color:{{ $badgeColor }};padding:0.35rem 1rem;border-radius:50px;font-size:0.8rem;font-weight:700;border:1px solid {{ $badgeColor }}40;white-space:nowrap">
            {{ strtoupper($detection->status) }}
        </span>
    </div>

    {{-- SECTION --}}
    <div class="section-label"> Informasi Foto Daun</div>

    {{-- GRID --}}
    <div class="detail-content">

        {{-- FOTO --}}
        <div>
            <img src="{{ Storage::url($detection->image_path) }}"
                style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:1px solid var(--border);display:block"
                alt="Foto Daun">

            {{-- METRIK --}}
            <div style="margin-top:1rem;background:var(--bg3);border-radius:10px;padding:0.75rem;display:flex;flex-direction:column;gap:0.75rem">

                {{-- Akurasi (Confidence) --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem">
                        <span style="color:var(--muted);font-size:0.75rem">Tingkat Akurasi</span>
                        <span style="color:var(--green);font-weight:700;font-size:0.9rem">
                            {{ number_format($detection->confidence, 1) }}%
                        </span>
                    </div>
                    <div style="background:var(--border);border-radius:50px;height:6px;overflow:hidden">
                        <div style="width:{{ $detection->confidence }}%;height:100%;background:var(--green);border-radius:50px"></div>
                    </div>
                </div>

                {{-- Precision --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem">
                        <span style="color:var(--muted);font-size:0.75rem">Precision</span>
                        <span style="color:var(--green);font-weight:700;font-size:0.9rem">66.2%</span>
                    </div>
                    <div style="background:var(--border);border-radius:50px;height:6px;overflow:hidden">
                        <div style="width:66.2%;height:100%;background:var(--green);border-radius:50px"></div>
                    </div>
                </div>

                {{-- Recall --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem">
                        <span style="color:var(--muted);font-size:0.75rem">Recall</span>
                        <span style="color:var(--green);font-weight:700;font-size:0.9rem">66.3%</span>
                    </div>
                    <div style="background:var(--border);border-radius:50px;height:6px;overflow:hidden">
                        <div style="width:66.3%;height:100%;background:var(--green);border-radius:50px"></div>
                    </div>
                </div>

                {{-- F1-Score --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem">
                        <span style="color:var(--muted);font-size:0.75rem">F1-Score</span>
                        <span style="color:var(--green);font-weight:700;font-size:0.9rem">66.2%</span>
                    </div>
                    <div style="background:var(--border);border-radius:50px;height:6px;overflow:hidden">
                        <div style="width:66.2%;height:100%;background:var(--green);border-radius:50px"></div>
                    </div>
                </div>

                {{-- mAP@50 --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem">
                        <span style="color:var(--muted);font-size:0.75rem">mAP@50</span>
                        <span style="color:var(--green);font-weight:700;font-size:0.9rem">66.4%</span>
                    </div>
                    <div style="background:var(--border);border-radius:50px;height:6px;overflow:hidden">
                        <div style="width:66.4%;height:100%;background:var(--green);border-radius:50px"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- DETAIL --}}
        <div style="min-width:0">

            {{-- Nama --}}
            <div style="background:var(--bg3);border-radius:12px;padding:1rem;margin-bottom:0.75rem;border:1px solid var(--border)">
                <span style="color:var(--muted);font-size:0.75rem">Nama Penyakit</span>
                <div style="color:var(--text);font-size:1.2rem;font-weight:700;overflow-wrap:break-word">
                    {{ $detection->disease_name }}
                </div>
            </div>

            {{-- GRID INFO --}}
            <div class="info-grid-2">

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label">Status</span>
                    <div style="margin-top:4px">
                        <span style="background:{{ $statusBg }};color:{{ $badgeColor }};padding:3px 10px;border-radius:50px;font-size:0.75rem;font-weight:600;display:inline-block">
                            {{ ucfirst($detection->status) }}
                        </span>
                    </div>
                </div>

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label">Sumber</span>
                    <div class="value">{{ ucfirst($detection->source) }}</div>
                </div>

            </div>

            {{-- LOKASI --}}
            @if($detection->latitude)
            <div class="lokasi-grid-2">

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label"> Nama Pohon</span>
                    <div class="value" style="overflow-wrap:break-word">{{ $detection->foto_lokasi_nama ?? '-' }}</div>
                </div>

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label">Latitude</span>
                    <div class="value mono" style="overflow-wrap:break-word">{{ $detection->latitude }}</div>
                </div>

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label">Longitude</span>
                    <div class="value mono" style="overflow-wrap:break-word">{{ $detection->longitude }}</div>
                </div>

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label"> Suhu</span>
                    <div class="value">{{ $latest['temp'] ?? '-' }} °C</div>
                </div>

                <div style="background:var(--bg3);padding:0.8rem;border-radius:10px;border:1px solid var(--border);min-width:0">
                    <span class="label"> Kelembaban</span>
                    <div class="value">{{ $latest['humidity'] ?? '-' }}%</div>
                </div>

            </div>
            @endif

            {{-- DESKRIPSI --}}
            @if($detection->description)
            <div style="background:var(--bg3);padding:0.9rem;border-radius:10px;border:1px solid var(--border)">
                <span class="label"> Deskripsi</span>
                <p style="margin-top:5px;color:var(--muted);font-size:0.82rem;line-height:1.6">
                    {{ $detection->description }}
                </p>
            </div>
            @endif

        </div>
    </div>

    {{-- MAP --}}
    @if($detection->latitude && $detection->longitude)
    <div style="margin-bottom:1.5rem">
        <div style="color:var(--green);font-weight:600;margin-bottom:0.5rem">📍 Peta Lokasi</div>
        <div id="map-detail" style="height:280px;border-radius:12px;border:1px solid var(--border)"></div>
    </div>
    @endif

    {{-- ACTION --}}
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;border-top:1px solid var(--border);padding-top:1rem">
        <a href="{{ route('detect.pdf', $detection->id) }}" class="btn-primary" style="text-align:center">📄 Unduh PDF</a>
        <a href="{{ route('riwayat') }}" class="btn-secondary" style="text-align:center">← Kembali</a>
    </div>

</div>

{{-- MAP SCRIPT --}}
@if($detection->latitude && $detection->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map-detail')
        .setView([{{ $detection->latitude }}, {{ $detection->longitude }}], 17);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 19
    }).addTo(map);

    const status = "{{ $detection->status }}";
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

    L.marker([{{ $detection->latitude }}, {{ $detection->longitude }}], { icon })
        .addTo(map)
        .bindPopup("{{ $detection->disease_name }}")
        .openPopup();

    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif

<style>
/* ── Header: wrap di layar sempit ── */
.detail-header{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);
    flex-wrap:wrap;gap:0.75rem;
}

/* ── Foto + Detail: 2 kolom di desktop, stack di mobile/tablet ── */
.detail-content{
    display:grid;
    grid-template-columns:240px 1fr;
    gap:1.5rem;
    margin-bottom:1.5rem;
}

/* ── Status / Sumber ── */
.info-grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0.75rem;
    margin-bottom:0.75rem;
}

/* ── Nama Pohon / Lat / Lng / Suhu / Kelembaban ── */
.lokasi-grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0.75rem;
    margin-bottom:0.75rem;
}

@media (max-width: 900px) {
    .detail-content{
        grid-template-columns:1fr;
    }
}

@media (max-width: 480px) {
    .lokasi-grid-2{
        grid-template-columns:1fr;
    }
    .detail-header{
        align-items:flex-start;
    }
    .card a.btn-primary,
    .card a.btn-secondary{
        flex:1;
        min-width:140px;
    }
}
</style>

@endsection