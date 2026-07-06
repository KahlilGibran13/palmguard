@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- STAT CARDS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Total Deteksi</span>
            <span class="stat-value">{{ $total }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Daun Sehat</span>
            <span class="stat-value" style="color:#6fcf97">{{ $totalSehat }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Terinfeksi</span>
            <span class="stat-value" style="color:#e05252">{{ $totalSakit }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Waspada</span>
            <span class="stat-value" style="color:#f5a623">{{ $totalWaspada }}</span>
        </div>
    </div>
</div>

{{--
    FIX #1 (Keamanan): Sembunyikan card upload untuk manager pakai @if, bukan CSS display:none.
    CSS display:none tidak mencegah manager submit request langsung ke server.
    Pastikan route detect.store juga ada pengecekan role di controller.
--}}
@if(!auth()->user()->isManager())
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <span class="card-icon"></span>
        <h3 class="card-title">Deteksi Penyakit Daun</h3>
    </div>

    <div class="tab-container">
        <button class="tab-btn active" onclick="switchTab('upload')">Upload File</button>
        <button class="tab-btn" onclick="switchTab('kamera')">Kamera</button>
    </div>

    {{-- Tab Upload --}}
    <div id="tab-upload" class="tab-content active">
        {{--
            FIX #2 (Bug): accept="image/jpg" bukan MIME type yang valid.
            Gunakan image/jpeg dan tambahkan ekstensi sebagai fallback.
        --}}
        <div class="dropzone" id="dropzone" onclick="document.getElementById('fileInput').click()">
            <p style="color:#ccc;font-size:0.95rem;margin:0">Klik atau seret gambar daun kelapa sawit ke sini</p>
            <p style="color:#666;font-size:0.8rem;margin-top:0.3rem">Format: JPG, PNG · Maks: 5MB</p>
            <input type="file" id="fileInput" accept=".jpg,.jpeg,.png,image/jpeg,image/png" style="display:none">
        </div>
        <div id="previewWrap" style="display:none;text-align:center;margin-top:1rem">
            <img id="previewImg" style="max-height:220px;border-radius:10px;border:2px solid #2d7a4f;max-width:100%">
        </div>
    </div>

    {{-- Tab Kamera --}}
    <div id="tab-kamera" class="tab-content">
        <div style="text-align:center">
            <video id="video" autoplay playsinline muted style="width:100%;max-width:480px;border-radius:10px;border:2px solid #2d7a4f;display:none"></video>
            <canvas id="snapshot" style="display:none;"></canvas>
            <div id="camPreviewWrap" style="display:none;margin-top:0.5rem">
                <img id="camPreview" style="max-height:220px;border-radius:10px;border:2px solid #6fcf97;max-width:100%">
            </div>
            <div style="margin-top:1rem;display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap">
                <button class="btn-primary" onclick="startCamera()">📷 Aktifkan Kamera</button>
                <button class="btn-secondary" onclick="capturePhoto()" id="btnCapture" style="display:none">📸 Ambil Foto</button>
            </div>
        </div>
    </div>

    {{-- Form Lokasi --}}
    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #1e3a28">
        <p style="color:#6fcf97;font-size:0.85rem;font-weight:600;margin:0 0 1rem">
            📍 Informasi Lokasi <span style="color:#555;font-weight:400">(opsional)</span>
        </p>
        <div class="location-grid">

            {{--
                FIX #3 (Performa): 500 option di-render server setiap load halaman.
                Idealnya diganti dengan AJAX search, tapi untuk sekarang tetap dipertahankan.
                Tandai sebagai technical debt.
            --}}
            <div>
                <label style="display:block;color:#777;font-size:0.78rem;margin-bottom:0.3rem">Nama Pohon</label>
                <select id="foto_lokasi_nama"
                    style="width:100%;background:#0a1a0e;border:1px solid #1e3a28;color:#e0e0e0;padding:0.45rem 0.6rem;border-radius:7px;font-size:0.83rem;box-sizing:border-box;cursor:pointer;appearance:none;-webkit-appearance:none">
                    <option value="">— Pilih Pohon —</option>
                    @for($i = 1; $i <= 500; $i++)
                        <option value="Pohon {{ $i }}">Pohon {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label style="display:block;color:#777;font-size:0.78rem;margin-bottom:0.3rem">Koordinat Foto Daun</label>
                <div class="koordinat-row">
                    <input type="number" id="foto_latitude" step="any" placeholder="Latitude..."
                        style="background:#0a1a0e;border:1px solid #1e3a28;color:#e0e0e0;padding:0.45rem 0.6rem;border-radius:7px;font-size:0.83rem;box-sizing:border-box">
                    <input type="number" id="foto_longitude" step="any" placeholder="Longitude..."
                        style="background:#0a1a0e;border:1px solid #1e3a28;color:#e0e0e0;padding:0.45rem 0.6rem;border-radius:7px;font-size:0.83rem;box-sizing:border-box">
                </div>
                <span id="gps_status" style="color:#6fcf97;font-size:0.7rem;margin-top:0.2rem;display:block">📡 Mendeteksi lokasi GPS...</span>
            </div>

        </div>
    </div>

    <div style="margin-top:1.5rem;text-align:center">
        <button class="btn-primary" style="padding:0.8rem 3rem;font-size:1rem;width:100%;max-width:320px" id="btnDetect" onclick="runDetect()">
            🔍 Mulai Deteksi
        </button>
    </div>

    <div id="loadingBox" style="display:none;text-align:center;padding:2rem">
        <div class="spinner"></div>
        <p style="color:#6fcf97;margin-top:0.75rem">Sedang menganalisis gambar...</p>
    </div>

    <div id="resultBox" style="display:none;margin-top:1.5rem"></div>
</div>
@endif

{{-- PETA SEMUA DETEKSI --}}
@if($detections->count() > 0)
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <span class="card-icon">📍</span>
        <h3 class="card-title">Peta Lokasi Deteksi</h3>
    </div>
    <div id="map-dashboard" style="width:100%;height:380px;border-radius:12px;border:1px solid #1e3a28;margin-top:0.75rem"></div>
</div>

{{-- Leaflet dimuat sekali saja, digabung jadi satu blok --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map-dashboard').setView([-6.2088, 106.8456], 12);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 19
    }).addTo(map);

    const markers = [
        @foreach($detections as $d)
        @if($d->latitude && $d->longitude)
        {
            lat: {{ $d->latitude }},
            lng: {{ $d->longitude }},
            {{--
                FIX #4 (Keamanan): nama_pohon bisa mengandung karakter berbahaya.
                Gunakan @json() supaya value di-escape dengan benar, bukan string biasa.
            --}}
            nama_pohon: @json(optional($d->pohon)->nama_pohon ?? '-'),
            url: "{{ route('detect.show', $d->id) }}",
            status: "{{ $d->status }}"
        },
        @endif
        @endforeach
    ];

    const bounds = [];

    markers.forEach(function(d) {
        const warna = d.status === 'sakit' ? '#e05252' : d.status === 'waspada' ? '#f5a623' : '#2d7a4f';

        const icon = L.divIcon({
            html: `<div style="background:${warna};width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 8px ${warna}"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
            className: ''
        });

        const marker = L.marker([d.lat, d.lng], { icon }).addTo(map);

        {{-- FIX #5 (Keamanan): Gunakan textContent bukan innerHTML untuk nama_pohon di popup --}}
        const popupDiv = document.createElement('div');
        popupDiv.style.cssText = 'font-family:sans-serif;min-width:140px';

        const namaEl = document.createElement('strong');
        namaEl.style.color = '#2d7a4f';
        namaEl.textContent = '🌴 ' + d.nama_pohon;

        const linkEl = document.createElement('a');
        linkEl.href = d.url;
        linkEl.style.cssText = 'display:inline-block;margin-top:6px;background:#2d7a4f;color:#fff;padding:4px 10px;border-radius:6px;text-decoration:none;font-size:12px';
        linkEl.textContent = '🔍 Lihat Detail';

        popupDiv.appendChild(namaEl);
        popupDiv.appendChild(document.createElement('br'));
        popupDiv.appendChild(linkEl);

        marker.bindPopup(popupDiv);
        bounds.push([d.lat, d.lng]);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif

{{-- CHARTS --}}
<div class="charts-grid">
    <div class="card" style="min-height:340px">
        <div class="card-header">
            <span class="card-icon"></span>
            <h3 class="card-title">Distribusi Penyakit</h3>
        </div>
        @if($distribusi->count() > 0)
        <div style="position:relative;height:240px;display:flex;align-items:center;justify-content:center">
            <canvas id="pieChart"></canvas>
        </div>
        <div id="pieLegend" style="margin-top:0.75rem;display:flex;flex-wrap:wrap;gap:0.4rem;justify-content:center"></div>
        @else
        <div style="text-align:center;color:#555;padding:3rem 1rem;font-size:0.88rem">Belum ada data deteksi</div>
        @endif
    </div>

    <div class="card" style="min-height:340px">
        <div class="card-header">
            <span class="card-icon"></span>
            <h3 class="card-title">Tren Deteksi (30 Hari)</h3>
        </div>
        @if($trendData->count() > 0)
        <div style="position:relative;height:260px">
            <canvas id="lineChart"></canvas>
        </div>
        @else
        <div style="text-align:center;color:#555;padding:3rem 1rem;font-size:0.88rem">Belum ada data tren</div>
        @endif
    </div>
</div>

{{-- RIWAYAT TERAKHIR --}}
<div class="card" style="margin-top:1.5rem">
    <div class="card-header">
        <span class="card-icon"></span>
        <h3 class="card-title">Deteksi Terakhir</h3>
        <a href="{{ route('riwayat') }}" style="margin-left:auto;color:#6fcf97;text-decoration:none;font-size:0.85rem">Lihat Semua →</a>
    </div>
    @forelse($detections as $d)
    <div class="riwayat-row">
        <img src="{{ Storage::url($d->image_path) }}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;flex-shrink:0" alt="Foto deteksi">
        <div style="flex:1;min-width:0">
            <span style="display:block;color:#e0e0e0;font-size:0.9rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                {{ $d->disease_name }}
            </span>
            <span style="display:block;color:#555;font-size:0.75rem;margin-top:0.15rem">
                {{ $d->created_at->format('d M Y, H:i') }}
                @if($d->foto_lokasi_nama) · 🌴 {{ $d->foto_lokasi_nama }} @endif
            </span>
        </div>
        <span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span>
        {{-- FIX #6 (Edge case): Tambah fallback jika confidence null --}}
        <span style="color:#6fcf97;font-size:0.85rem;font-weight:600">
            {{ $d->confidence !== null ? $d->confidence : '-' }}%
        </span>
    </div>
    @empty
    <div style="text-align:center;color:#555;padding:2rem;font-size:0.88rem">Belum ada riwayat deteksi</div>
    @endforelse
</div>

<style>
.card-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.5rem;
}
.card-icon {
    font-size: 1.2rem;
    margin: 0;
    padding: 0;
    width: auto;
    line-height: 1;
    display: inline-flex;
    align-items: center;
}
.card-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text);
}
.card-header a {
    margin-left: auto;
}
.badge {
    padding: 0.25rem 0.65rem;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 600;
}
.badge-sehat   { background: rgba(29,185,84,0.15);  color: #1DB954; }
.badge-sakit   { background: rgba(224,82,82,0.15);  color: #e05252; }
.badge-waspada { background: rgba(245,166,35,0.15); color: #f5a623; }

/* ── Form Lokasi: grid otomatis stack kalau sempit ── */
.location-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
}

/* ── Koordinat lat/lng: berdampingan di desktop, stack di HP ── */
.koordinat-row {
    display: flex;
    gap: 0.4rem;
}
.koordinat-row input {
    width: 50%;
}

/* ── Charts: 2 kolom di desktop, 1 kolom di HP ── */
.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* ── Riwayat item ── */
.riwayat-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #0d1f12;
}

@media (max-width: 900px) {
    .charts-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .koordinat-row {
        flex-direction: column;
    }
    .koordinat-row input {
        width: 100%;
    }
    .riwayat-row {
        flex-wrap: wrap;
    }
    .riwayat-row > span:last-child {
        margin-left: auto;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
// ─── Tab ────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-btn, .tab-content').forEach(el => el.classList.remove('active'));
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
    if (tab !== 'kamera') stopCamera();
}

// ─── Preview Upload ──────────────────────────────────────────────────────────
const fileInput = document.getElementById('fileInput');

if (fileInput) {
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewWrap').style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
}

// ─── Drag & Drop ─────────────────────────────────────────────────────────────
const dz = document.getElementById('dropzone');

if (dz) {
    dz.addEventListener('dragover', e => {
        e.preventDefault();
        dz.style.borderColor = '#6fcf97';
    });

    dz.addEventListener('dragleave', () => {
        dz.style.borderColor = '#2d7a4f';
    });

    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.style.borderColor = '#2d7a4f';

        const file = e.dataTransfer.files[0];

        if (file) {
            document.getElementById('fileInput').files = e.dataTransfer.files;
            document.getElementById('fileInput')
                .dispatchEvent(new Event('change'));
        }
    });
}

// ─── Kamera ──────────────────────────────────────────────────────────────────
let stream = null;
let capturedBlob = null;
let lastBlobUrl = null;

async function startCamera() {
    try {
        // Minta izin dulu supaya label kamera muncul (bukan "Camera 0", dll)
        await navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(s => s.getTracks().forEach(t => t.stop()));

        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(d => d.kind === 'videoinput');

        if (videoDevices.length === 0) {
            alert('Tidak ada kamera yang ditemukan.');
            return;
        }

        // Kalau hanya 1 kamera, langsung pakai tanpa tanya
        if (videoDevices.length === 1) {
            await openCamera(videoDevices[0].deviceId);
            return;
        }

        // Lebih dari 1 kamera: tampilkan pilihan
        showCameraSelector(videoDevices);

    } catch (e) {
        let pesan = e.name === 'NotAllowedError' ? 'Izin kamera ditolak.' :
                    e.name === 'NotFoundError'   ? 'Tidak ada kamera ditemukan.' :
                    e.name === 'NotReadableError' ? 'Kamera sedang dipakai aplikasi lain.' :
                    e.name + ': ' + e.message;
        alert('Gagal akses kamera: ' + pesan);
    }
}

function showCameraSelector(devices) {
    // Hapus selector lama kalau ada
    document.getElementById('camSelectorWrap')?.remove();

    const wrap = document.createElement('div');
    wrap.id = 'camSelectorWrap';
    wrap.style.cssText = 'margin-top:1rem;text-align:center';

    const label = document.createElement('label');
    label.style.cssText = 'display:block;color:#777;font-size:0.78rem;margin-bottom:0.4rem';
    label.textContent = 'Pilih kamera:';

    const select = document.createElement('select');
    select.id = 'camSelect';
    select.style.cssText = 'background:#0a1a0e;border:1px solid #1e3a28;color:#e0e0e0;padding:0.45rem 0.8rem;border-radius:7px;font-size:0.83rem;cursor:pointer;margin-right:0.5rem';

    devices.forEach((d, i) => {
        const opt = document.createElement('option');
        opt.value = d.deviceId;
        opt.textContent = d.label || `Kamera ${i + 1}`;
        select.appendChild(opt);
    });

    const btnPakai = document.createElement('button');
    btnPakai.className = 'btn-primary';
    btnPakai.textContent = 'Pakai Kamera Ini';
    btnPakai.onclick = async () => {
        wrap.remove();
        await openCamera(select.value);
    };

    wrap.appendChild(label);
    wrap.appendChild(select);
    wrap.appendChild(btnPakai);

    // Sisipkan setelah tombol "Aktifkan Kamera"
    document.getElementById('video').parentElement.appendChild(wrap);
}

async function openCamera(deviceId) {
    stopCamera(); // stop stream lama dulu

    stream = await navigator.mediaDevices.getUserMedia({
        video: { deviceId: { exact: deviceId } },
        audio: false
    });

    const v = document.getElementById('video');
    v.srcObject = stream;
    await v.play();
    v.style.display = 'block';
    document.getElementById('btnCapture').style.display = 'inline-block';
}

function capturePhoto() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('snapshot');
    if (video.readyState < 2) { alert('Video belum siap, tunggu sebentar.'); return; }
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    canvas.toBlob(blob => {
        if (!blob) { alert('Gagal mengambil foto, coba lagi.'); return; }
        capturedBlob = blob;
        if (lastBlobUrl) URL.revokeObjectURL(lastBlobUrl);
        lastBlobUrl = URL.createObjectURL(blob);
        document.getElementById('camPreview').src = lastBlobUrl;
        document.getElementById('camPreviewWrap').style.display = 'block';
    }, 'image/jpeg', 0.95);
}

function stopCamera() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    const v = document.getElementById('video');
    v.srcObject = null;
    v.style.display = 'none';
    document.getElementById('btnCapture').style.display = 'none';
    document.getElementById('camSelectorWrap')?.remove();
}

// ─── Deteksi ─────────────────────────────────────────────────────────────────
async function runDetect() {
    const isKamera = document.getElementById('tab-kamera').classList.contains('active');
    const fi = document.getElementById('fileInput');

    if (isKamera && !capturedBlob) { alert('Ambil foto terlebih dahulu.'); return; }
    if (!isKamera && !fi.files[0]) { alert('Pilih gambar terlebih dahulu.'); return; }

    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    fd.append('source', isKamera ? 'kamera' : 'upload');
    fd.append(
        'image',
        isKamera ? capturedBlob : fi.files[0],
        isKamera ? 'kamera_' + Date.now() + '.jpg' : fi.files[0].name
    );

    ['foto_latitude', 'foto_longitude', 'foto_lokasi_nama'].forEach(id => {
        const val = document.getElementById(id)?.value;
        if (val) fd.append(id, val);
    });

    document.getElementById('loadingBox').style.display = 'block';
    document.getElementById('resultBox').style.display = 'none';
    document.getElementById('btnDetect').disabled = true;

    try {
        const res = await fetch('{{ route("detect.store") }}', { method: 'POST', body: fd });

        if (!res.ok) {
            const text = await res.text();
            throw new Error('HTTP ' + res.status + ' — ' + text.substring(0, 100));
        }

        const data = await res.json();

        document.getElementById('loadingBox').style.display = 'none';
        document.getElementById('btnDetect').disabled = false;

        if (data.success) {
            const d = data.detection;

            // FIX #9 (Edge case): Fallback jika confidence null/NaN
            const confidenceText = (d.confidence !== null && !isNaN(d.confidence))
                ? parseFloat(d.confidence).toFixed(1) + '%'
                : 'N/A';

            const bc = d.status === 'sehat' ? '#6fcf97' : d.status === 'sakit' ? '#e05252' : '#f5a623';

            // FIX #10 (Keamanan XSS): Jangan pakai innerHTML langsung untuk data dari server.
            // Bangun elemen DOM secara programatik, set via textContent.
            const resultBox = document.getElementById('resultBox');
            resultBox.innerHTML = ''; // kosongkan dulu

            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'background:#0a1a0e;border:1px solid #1e3a28;border-radius:12px;padding:1.25rem';

            // Header
            const header = document.createElement('div');
            header.style.cssText = 'display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;flex-wrap:wrap';
            const judulEl = document.createElement('span');
            judulEl.style.cssText = 'font-size:1rem;font-weight:700;color:#e0e0e0';
            judulEl.textContent = 'Hasil Deteksi';
            const badgeEl = document.createElement('span');
            badgeEl.style.cssText = `background:${bc}22;color:${bc};padding:0.2rem 0.7rem;border-radius:20px;font-size:0.78rem;font-weight:600`;
            badgeEl.textContent = d.status.toUpperCase();
            header.appendChild(judulEl);
            header.appendChild(badgeEl);

            // Body: gambar + info
            const body = document.createElement('div');
            body.style.cssText = 'display:flex;gap:1rem;flex-wrap:wrap';
            const imgEl = document.createElement('img');
            imgEl.src = data.image_url;
            imgEl.style.cssText = 'width:130px;height:130px;object-fit:cover;border-radius:10px;border:2px solid #2d7a4f';
            imgEl.alt = 'Gambar deteksi';

            const infoDiv = document.createElement('div');
            infoDiv.style.cssText = 'flex:1;min-width:140px';
            infoDiv.innerHTML =
                '<p style="color:#666;font-size:0.78rem;margin:0 0 0.2rem">Penyakit</p>' +
                '<p id="res-disease" style="color:#e0e0e0;font-weight:700;margin:0 0 0.75rem"></p>' +
                '<p style="color:#666;font-size:0.78rem;margin:0 0 0.2rem">Akurasi</p>' +
                '<p id="res-confidence" style="color:#6fcf97;font-weight:700;font-size:1.2rem;margin:0"></p>';

            body.appendChild(imgEl);
            body.appendChild(infoDiv);

            // Deskripsi
            const descEl = document.createElement('p');
            descEl.style.cssText = 'color:#777;font-size:0.83rem;margin-top:0.75rem;line-height:1.5';

            // Link detail
            const linkEl = document.createElement('a');
            linkEl.href = '{{ route("deteksi") }}';
            linkEl.style.cssText = 'display:inline-block;margin-top:0.75rem;color:#6fcf97;font-size:0.83rem;text-decoration:none';
            linkEl.textContent = 'Lihat detail lengkap →';

            wrapper.appendChild(header);
            wrapper.appendChild(body);
            wrapper.appendChild(descEl);
            wrapper.appendChild(linkEl);
            resultBox.appendChild(wrapper);

            // Isi teks via textContent (aman dari XSS)
            document.getElementById('res-disease').textContent = d.disease_name;
            document.getElementById('res-confidence').textContent = confidenceText;
            descEl.textContent = d.description;

            resultBox.style.display = 'block';
        }

    } catch (e) {
        document.getElementById('loadingBox').style.display = 'none';
        document.getElementById('btnDetect').disabled = false;
        alert('Gagal melakukan deteksi: ' + e.message);
    }
}

// ─── Charts ──────────────────────────────────────────────────────────────────

// FIX #11 (Edge case): Warna pie chart diperluas jadi 10 supaya tidak undefined
// jika ada lebih dari 6 jenis penyakit.
@if($distribusi->count() > 0)
const pieColors = ['#6fcf97','#e05252','#f5a623','#4a9eff','#b47fdb','#ff7f7f','#40c4ff','#ffab40','#69f0ae','#ea80fc'];
const pieLabels = @json($distribusi->pluck('disease_name'));
const pieData   = @json($distribusi->pluck('total'));

new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: pieLabels,
        datasets: [{
            data: pieData,
            backgroundColor: pieColors.slice(0, pieData.length),
            borderColor: '#060f0a',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} deteksi` } }
        }
    }
});

const legend = document.getElementById('pieLegend');
pieLabels.forEach((label, i) => {
    const item = document.createElement('div');
    item.style.cssText = 'display:flex;align-items:center;gap:0.35rem;font-size:0.75rem;color:#aaa';
    const dot = document.createElement('span');
    dot.style.cssText = `width:11px;height:11px;border-radius:50%;background:${pieColors[i]};display:inline-block;flex-shrink:0`;
    item.appendChild(dot);
    item.appendChild(document.createTextNode(`${label} (${pieData[i]})`));
    legend.appendChild(item);
});
@endif

@if($trendData->count() > 0)
const trendLabels = @json($trendData->pluck('tanggal'));
const trendValues = @json($trendData->pluck('total'));

new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Deteksi',
            data: trendValues,
            borderColor: '#6fcf97',
            backgroundColor: 'rgba(111,207,151,0.08)',
            borderWidth: 2,
            pointBackgroundColor: '#6fcf97',
            pointRadius: 4,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { ticks: { color: '#666', font: { size: 10 } }, grid: { color: '#0d1f12' } },
            y: { ticks: { color: '#666', stepSize: 1 }, grid: { color: '#0d1f12' }, beginAtZero: true }
        },
        plugins: {
            legend: { labels: { color: '#777', font: { size: 11 } } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.raw} deteksi` } }
        }
    }
});
@endif

// ─── Auto GPS (hanya untuk non-manager) ──────────────────────────────────────
// FIX #12 (Edge case): GPS tidak perlu jalan untuk manager karena form deteksi

window.addEventListener('load', function () {
    const latEl    = document.getElementById('foto_latitude');
    const lngEl    = document.getElementById('foto_longitude');
    const statusEl = document.getElementById('gps_status');
    if (!latEl || !lngEl || !statusEl) return; // manager: elemen tidak ada, skip

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                latEl.value    = pos.coords.latitude.toFixed(7);
                lngEl.value    = pos.coords.longitude.toFixed(7);
                statusEl.textContent = '✅ Lokasi terdeteksi otomatis';
                statusEl.style.color = '#6fcf97';
            },
            function () {
                statusEl.textContent = '⚠️ GPS gagal — isi koordinat manual';
                statusEl.style.color = '#f5a623';
            }
        );
    } else {
        statusEl.textContent = '⚠️ Browser tidak support GPS — isi manual';
        statusEl.style.color = '#f5a623';
    }
});
</script>

@endsection