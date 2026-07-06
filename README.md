# 🌿 PalmGuard
## Pendeteksian Penyakit Daun Kelapa Sawit Berbasis Citra Digital

---

## 📋 Deskripsi
Aplikasi web berbasis Laravel untuk mendeteksi penyakit daun kelapa sawit menggunakan model YOLOv8 yang dikembangkan oleh Tim Sistem Cerdas, dengan dataset dari Tim Rekayasa Data (Roboflow).

---

## 👥 Pembagian Tim
| Tim | Tugas |
|-----|-------|
| **RPL (repo ini)** | Laravel, MySQL, Blade, DomPDF, integrasi API |
| **Sistem Cerdas** | Training YOLOv8, deploy Flask/FastAPI API |
| **Rekayasa Data** | Dataset Roboflow, labeling, augmentasi |
| **Keamanan Informasi** | Activity log, autentikasi, enkripsi |

---

## 🛠️ Stack Teknologi
- **Framework**: Laravel 10
- **Database**: MySQL (XAMPP)
- **Template**: Blade
- **PDF**: barryvdh/laravel-dompdf
- **HTTP Client**: Guzzle (panggil API YOLOv8)
- **AI**: YOLOv8 API Python (Tim Sistem Cerdas) → `POST http://localhost:5000/predict`

---

## 🚀 Instalasi

```bash
# 1. Clone / copy project
cd C:\laravel\palmguard

# 2. Install dependencies
composer install

# 3. Copy .env dan setting database
cp .env.example .env
# Edit DB_DATABASE=palmguard_db

# 4. Generate key
php artisan key:generate

# 5. Buat database palmguard_db di phpMyAdmin

# 6. Migrasi database
php artisan migrate

# 7. Link storage
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

Buka: `http://localhost:8000`

---

## 📡 Integrasi API YOLOv8

### Request ke API Python:
```
POST http://localhost:5000/predict
Content-Type: multipart/form-data
Body: image (file)
```

### Response yang diharapkan dari Tim Sistem Cerdas:
```json
{
    "disease": "Ganoderma Butt Rot",
    "status": "sakit",
    "confidence": 89.5,
    "description": "Terdeteksi penyakit busuk pangkal batang...",
    "bounding_box": {
        "x": 120,
        "y": 85,
        "width": 340,
        "height": 280
    }
}
```

### Status yang valid:
- `sehat` → SEHAT- `sakit` → Terinfeksi penyakit
- `waspada` → Perlu pemantauan

### Label penyakit dari Roboflow (Tim Rekayasa Data):
- Ganoderma Butt Rot
- Basal Stem Rot
- Crown Disease
- Bud Rot
- Leaflet Blight
- SEHAT
> ⚠️ **Catatan**: Jika API Python belum aktif, sistem otomatis menggunakan mode simulasi (fallback).

---

## 🗄️ Struktur Database

### Tabel: `detections`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| filename | string | Nama file gambar |
| image_path | string | Path file di storage |
| disease_name | string | Nama penyakit dari YOLOv8 |
| status | enum | sehat / sakit / waspada |
| description | text | Keterangan penyakit |
| confidence | decimal | Confidence score (0-100) |
| bounding_box | json | Koordinat bbox dari YOLOv8 |
| yolo_raw | json | Raw response JSON dari API |
| file_size | string | Ukuran file |
| source | enum | upload / kamera |
| created_at | timestamp | Tanggal & jam deteksi |

---

## 📁 Struktur File Laravel

```
app/
├── Http/Controllers/
│   └── DetectionController.php   ← Controller utama
├── Models/
│   └── Detection.php             ← Model Eloquent
database/
└── migrations/
    └── ..._create_detections_table.php
resources/views/
├── layouts/
│   └── app.blade.php             ← Layout utama
├── pages/
│   ├── dashboard.blade.php       ← Upload + hasil deteksi
│   ├── deteksi.blade.php         ← Detail + katalog penyakit
│   └── riwayat.blade.php         ← History + hapus + CSV
└── pdf/
    └── report.blade.php          ← Template PDF laporan
routes/
└── web.php                       ← Semua route aplikasi
```

---

## 🌐 Routes

| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/` | Dashboard |
| GET | `/deteksi` | Halaman Deteksi Penyakit |
| GET | `/riwayat` | Riwayat Deteksi |
| POST | `/detect` | Upload & proses gambar |
| DELETE | `/detect/{id}` | Hapus satu data |
| DELETE | `/detect-all` | Hapus semua data |
| GET | `/detect/{id}/pdf` | Download PDF laporan |
| GET | `/riwayat/export` | Export CSV riwayat |
