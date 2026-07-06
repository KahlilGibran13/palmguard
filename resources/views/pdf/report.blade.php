<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size:12px; color:#1a2b1e; background:#fff; padding:30px; }

  .header { display:flex; align-items:center; border-bottom:3px solid #2d7a4f; padding-bottom:16px; margin-bottom:20px; }
  .logo-box { width:50px;height:50px;background:#2d7a4f;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:14px;flex-shrink:0; }
  .logo-box span { font-size:24px; }
  .header-title h1 { font-size:20px;font-weight:bold;color:#1a2b1e;letter-spacing:-0.5px; }
  .header-title p { font-size:10px;color:#5a8a6a;margin-top:2px; }

  .doc-title { font-size:15px;font-weight:bold;color:#2d7a4f;margin-bottom:4px; }
  .doc-sub { font-size:10px;color:#888;margin-bottom:20px; }

  table.info-table { width:100%;border-collapse:collapse;margin-bottom:20px; }
  table.info-table td { padding:8px 12px;font-size:11px;border:1px solid #dce8e0; }
  table.info-table td:first-child { width:160px;font-weight:bold;background:#f0f7f3;color:#2d7a4f; }

  .section-title { font-size:12px;font-weight:bold;color:#2d7a4f;border-left:3px solid #2d7a4f;padding-left:8px;margin:20px 0 10px; }

  .result-box { background:#f0f7f3;border:1px solid #b2d8bf;border-radius:8px;padding:16px;margin-bottom:20px; }
  .result-disease { font-size:18px;font-weight:bold;color:#c0392b;margin-bottom:6px; }
  .result-disease.sehat { color:#2d7a4f; }
  .conf-bar-bg { width:200px;height:8px;background:#dce8e0;border-radius:4px;overflow:hidden;display:inline-block;vertical-align:middle;margin:0 8px; }
  .conf-bar-fill { height:100%;background:#f5a623;border-radius:4px; }
  .conf-bar-fill.sehat { background:#2d7a4f; }

  .ciri-list { padding-left:0;list-style:none; }
  .ciri-list li { padding:5px 0 5px 14px;font-size:11px;color:#3a5a45;border-bottom:1px solid #eef5f1; position:relative; }
  .ciri-list li::before { content:'›'; position:absolute;left:0;color:#2d7a4f;font-weight:bold; }

  .recommend-box { background:#fff8e6;border:1px solid #f5cc7a;border-radius:8px;padding:14px;margin-bottom:20px; }
  .recommend-box.safe { background:#f0f7f3;border-color:#b2d8bf; }
  .recommend-title { font-size:11px;font-weight:bold;color:#d4860a;margin-bottom:6px; }
  .recommend-title.safe { color:#2d7a4f; }
  .recommend-text { font-size:11px;color:#5a5a5a;line-height:1.6; }

  .footer { border-top:1px solid #dce8e0;padding-top:12px;margin-top:24px;display:flex;justify-content:space-between;font-size:9px;color:#999; }
  .img-preview { max-width:160px;max-height:160px;border-radius:8px;border:1px solid #dce8e0; }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  {{-- <div class="logo-box"><span>🌿</span></div> --}}
  <div class="header-title">
    <h1>PalmGuard</h1>
    <p>Sistem Deteksi Penyakit Daun Kelapa Sawit — Capstone Project 2026</p>
  </div>
</div>

<div class="doc-title">LAPORAN HASIL DETEKSI PENYAKIT</div>
<div class="doc-sub">Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</div>

<!-- INFO UMUM -->
<div class="section-title">Informasi Deteksi</div>
<table class="info-table">
  <tr><td>ID Deteksi</td><td>#{{ str_pad($detection->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
  <tr><td>Nama Pohon</td><td>{{ optional($detection->pohon)->nama_pohon ?? '-' }}</td></tr>
  <tr><td>Ukuran File</td><td>{{ $detection->file_size }}</td></tr>
  <tr><td>Sumber</td><td>{{ ucfirst($detection->source) }}</td></tr>
  <tr><td>Tanggal Upload</td><td>{{ $detection->created_at->format('d F Y') }}</td></tr>
  <tr><td>Jam</td><td>{{ $detection->created_at->format('H:i:s') }}</td></tr>
  <tr><td>Hari</td><td>{{ $detection->created_at->translatedFormat('l') }}</td></tr>
</table>

<!-- HASIL DETEKSI -->
<div class="section-title">Hasil Analisis</div>
<div class="result-box">

  <div class="result-disease {{ $detection->status === 'sehat' ? 'sehat' : '' }}">
    {{ $detection->disease_name }}
  </div>

  <div style="margin-bottom:8px;font-size:11px;color:#5a8a6a;">{{ $detection->description }}</div>

  <div style="display:flex;align-items:center;font-size:11px;">
    <span style="font-weight:bold;color:#5a5a5a;">Confidence:</span>
    <span class="conf-bar-bg">
      <span class="conf-bar-fill {{ $detection->status === 'sehat' ? 'sehat' : '' }}" style="width:{{ $detection->confidence }}%"></span>
    </span>
    <span style="font-weight:bold;color:#d4860a;">{{ $detection->confidence }}%</span>
  </div>

  <div style="margin-top:8px;">
    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:bold;
      {{ $detection->status === 'sehat' ? 'background:#d4edda;color:#155724;' : ($detection->status === 'waspada' ? 'background:#fff3cd;color:#856404;' : 'background:#f8d7da;color:#721c24;') }}">
      {{ $detection->status_label }}
    </span>
  </div>

  {{-- Foto Daun --}}
  @if($detection->image_path)
  <div style="margin-top:12px;">
    <div style="font-size:10px;font-weight:bold;color:#5a5a5a;margin-bottom:6px;">Foto Daun:</div>
    <img 
      src="{{ storage_path('app/public/' . $detection->image_path) }}" 
      class="img-preview"
    />
  </div>
  @endif

</div>

<!-- CIRI-CIRI PENYAKIT -->
@php
$ciriMap = [
  'Ganoderma Butt Rot' => ['Daun berwarna kuning pucat lalu coklat, dimulai dari pelepah tua','Batang bagian bawah membusuk dan lunak ketika ditekan','Muncul tubuh buah jamur coklat kemerahan di pangkal batang','Pertumbuhan tanaman terhenti, tajuk mengecil'],
  'Basal Stem Rot' => ['Daun-daun tua menguning dan layu secara progresif ke atas','Jaringan batang bagian bawah berubah warna coklat gelap','Bau busuk terdeteksi di sekitar pangkal batang','Akar membusuk dan tanaman mudah tumbang'],
  'Crown Disease' => ['Leaflet patah, menggantung, dan tidak terbuka sempurna','Pelepah daun muda terlihat keriput dan tidak normal','Penyakit bersifat genetis, lebih umum pada bibit tertentu','Kondisi membaik setelah tanaman dewasa'],
  'Bud Rot' => ['Daun termuda (spear leaf) membusuk dan mudah dicabut','Titik tumbuh mengeluarkan bau busuk menyengat','Warna jaringan pucuk berubah coklat kehitaman','Terjadi pembusukan basah di bagian tengah tajuk'],
  'Leaflet Blight' => ['Bercak coklat keabu-abuan muncul di tepi leaflet','Bercak berkembang menyebabkan ujung daun mengering','Jamur Pestalotiopsis sp. sebagai penyebab utama','Sering muncul saat kelembapan tinggi'],
  'Daun Sehat' => ['Warna daun hijau segar dan merata tanpa bercak','Leaflet terbuka penuh dan tidak ada yang patah','Tidak ada tubuh buah jamur di sekitar pelepah','Pertumbuhan tajuk dan pelepah baru berjalan normal'],
];
$ciri = $ciriMap[$detection->disease_name] ?? [];
@endphp

@if(count($ciri))
<div class="section-title">Ciri-Ciri Penyakit: {{ $detection->disease_name }}</div>
<ul class="ciri-list">
  @foreach($ciri as $c)
  <li>{{ $c }}</li>
  @endforeach
</ul>
@endif

<!-- REKOMENDASI -->
<div class="section-title">Rekomendasi Tindakan</div>
<div class="recommend-box {{ $detection->status === 'sehat' ? 'safe' : '' }}">
  <div class="recommend-title {{ $detection->status === 'sehat' ? 'safe' : '' }}">
    {{ $detection->status === 'sehat' ? '✓ Tanaman Sehat' : ($detection->status === 'waspada' ? '⚠ Perlu Pemantauan' : '⚠ Perlu Penanganan Segera') }}
  </div>
  <div class="recommend-text">
    @if($detection->status === 'sehat')
      Tanaman dalam kondisi baik. Lakukan pemantauan rutin setiap 2 minggu. Pastikan pemupukan dan drainase lahan terjaga dengan baik untuk mencegah infeksi.
    @elseif($detection->status === 'waspada')
      Ditemukan indikasi awal penyakit. Segera lakukan pemeriksaan visual langsung di lapangan. Isolasi tanaman yang terinfeksi dan konsultasikan dengan ahli agronomi. Catat perkembangan gejala secara berkala.
    @else
      Tanaman terdeteksi terinfeksi penyakit. Segera hubungi ahli agronomi atau petugas perkebunan. Lakukan tindakan pengendalian sesuai protokol (fungisida / pemangkasan / isolasi). Dokumentasikan lokasi tanaman untuk pemetaan sebaran penyakit.
    @endif
  </div>
</div>

<!-- FOOTER -->
<div class="footer">
  <span>PalmGuard v1.0 · Capstone Project 2026</span>
  <span>Laporan ID: #{{ str_pad($detection->id, 5, '0', STR_PAD_LEFT) }} · {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
