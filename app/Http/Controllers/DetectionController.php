<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\Penyakit;
use App\Models\CiriPenyakit;
use App\Models\KondisiKebun;
use App\Models\Pohon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class DetectionController extends Controller
{
    // ═══════════════════════════════════════
    // MAPPING NAMA CLASS MODEL → NAMA DISPLAY
    // Sesuai hasil diagnosis model best.pt Maul:
    // [0] Bercak Daun
    // [1] Fusarium Wilt
    // [2] Magnesium Deficiency
    // [3] Manganese Deficiency
    // [4] Potassium Deficiency
    // [5] Rachis Blight
    // [6] SEHAT    // ═══════════════════════════════════════
    private const CLASS_STATUS_MAP = [
        'bercak daun'           => 'sakit',
        'fusarium wilt'         => 'sakit',
        'magnesium deficiency'  => 'waspada',
        'manganese deficiency'  => 'waspada',
        'potassium deficiency'  => 'waspada',
        'rachis blight'         => 'sakit',
        'SEHAT'            => 'sehat',
        // fallback nama lama (jaga-jaga kalau API Maul pakai nama lain)
        'leaf spot'             => 'sakit',
        'sehat'                 => 'sehat',
        'healthy'               => 'sehat',
    ];

    // ═══════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════
    public function dashboard()
    {
        $detections = Detection::with('pohon')->latest()->take(50)->get();
        $total        = Detection::count();
        $totalSehat   = Detection::where('status', 'sehat')->count();
        $totalSakit   = Detection::where('status', 'sakit')->count();
        $totalWaspada = Detection::where('status', 'waspada')->count();

        $distribusi = Detection::selectRaw('disease_name, count(*) as total')
            ->groupBy('disease_name')
            ->orderByDesc('total')
            ->get();

        $trendData = Detection::selectRaw('DATE(created_at) as tanggal, count(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $data_lokasi = Pohon::whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->latest()
                            ->get();

        return view('pages.dashboard', compact(
            'detections', 'total', 'totalSehat',
            'totalSakit', 'totalWaspada', 'distribusi', 'trendData',
            'data_lokasi'
        ));
    }

    // ═══════════════════════════════════════
    // HALAMAN DETEKSI PENYAKIT
    // ═══════════════════════════════════════
    public function deteksi()
    {
        $latest = Detection::with('pohon.kebun')->latest()->first();

        $apiweather = ['temp' => null, 'humidity' => null];

        if ($latest && $latest->pohon && $latest->pohon->kebun) {
            $apiweather = [
                'temp'     => $latest->pohon->kebun->suhu ?? null,
                'humidity' => $latest->pohon->kebun->kelembapan ?? null,
            ];
        }

        $data_lokasi = Pohon::whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->latest()
                            ->get();

        $katalog = Penyakit::with('ciriCiri')->get();

        return view('pages.deteksi', compact('latest', 'apiweather', 'katalog', 'data_lokasi'));
    }

    // ═══════════════════════════════════════
    // HALAMAN RIWAYAT
    // ═══════════════════════════════════════
    public function riwayat(Request $request)
    {
        $query = Detection::with('pohon')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('disease_name', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%')
                ->orWhereHas('pohon', function ($q2) use ($request) {
                    $q2->where('nama_pohon', 'like', '%' . $request->search . '%');
                });
            });
        }

        $detections   = $query->paginate(30);
        $total        = Detection::count();
        $totalSehat   = Detection::where('status', 'sehat')->count();
        $totalSakit   = Detection::where('status', 'sakit')->count();
        $totalWaspada = Detection::where('status', 'waspada')->count();

        return view('pages.riwayat', compact(
            'detections', 'total', 'totalSehat', 'totalSakit', 'totalWaspada'
        ));
    }

    // ═══════════════════════════════════════
    // PROSES DETEKSI (UPLOAD / KAMERA)
    // ═══════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'image'  => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'source' => 'required|in:upload,kamera',
        ], [
            'image.required' => 'Gambar wajib dipilih.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus JPG atau PNG.',
            'image.max'      => 'Ukuran gambar maksimal 5MB.',
        ]);

        $file      = $request->file('image');
        $filename  = time() . '_' . $file->getClientOriginalName();
        $path      = $file->storeAs('detections', $filename, 'public');
        $fileSize  = round($file->getSize() / 1024, 2) . ' KB';

        // ── Coba API YOLOv11, jika gagal pakai demo realistis ──
        $result = $this->callYOLOv8($file->getRealPath(), $filename);
        // Kalau API return annotated image, simpan itu — bukan gambar original
        if (!empty($result['annotated_image'])) {
            $annotatedFilename = 'detections/annotated_' . time() . '_' . $filename;
            $imageData = base64_decode($result['annotated_image']);
            Storage::disk('public')->put($annotatedFilename, $imageData);
            $path = $annotatedFilename; // override path ke yang annotated
        }

        $weather = $this->getApiOpenWeatherMap($request->foto_latitude, $request->foto_longitude);

        if ($weather['status'] === 'success') {
            $kebun_suhu = $weather['temp'];
            $kebun_kelembapan = $weather['humidity'];
        } else {
            $kebun_suhu = 0;
            $kebun_kelembapan = 0;
        }

        $pohon = Pohon::firstOrCreate(
            [
                'nama_pohon' => $request->foto_lokasi_nama,
                'latitude'   => $request->foto_latitude,
                'longitude'  => $request->foto_longitude,
            ]
        );

        $kondisiKebun = KondisiKebun::create([
            'id_pohon'   => $pohon->id,
            'suhu'       => $kebun_suhu,
            'kelembapan' => $kebun_kelembapan,
        ]);

        // ── Cari master data penyakit berdasarkan nama ──
        // Support nama Indonesia (dari model Maul) dan nama lama
        $penyakit = null;
        if ($result !== null) {
            $namaPenyakit = $result['disease_name'] ?? null;
            if ($namaPenyakit) {
                // Coba match exact dulu, kalau tidak ketemu coba LIKE
                $penyakit = Penyakit::where('nama_penyakit', $namaPenyakit)->first()
                    ?? Penyakit::where('nama_penyakit', 'like', '%' . $namaPenyakit . '%')->first();
            }
        }

        $detection = Detection::create([
            'id_pohon'         => $pohon->id,
            'id_penyakit'      => $penyakit ? $penyakit->id : null,
            'filename'         => $filename,
            'image_path'       => $path,
            'disease_name'     => $result['disease_name'] ?? null,
            'status'           => $result['status'] ?? null,
            'description'      => $result['description'] ?? null,
            'confidence'       => $result['confidence'] ?? null,
            'bounding_box'     => json_encode($result['bounding_box'] ?? null),
            'yolo_raw'         => json_encode($result['raw'] ?? null),
            'file_size'        => $fileSize,
            'source'           => $request->source,
            'latitude'         => $request->foto_latitude  ?: null,
            'longitude'        => $request->foto_longitude ?: null,
        ]);

        // Debug render check
        try {
            view('pages.deteksi', [
                'latest'  => $detection,
                'katalog' => [],
            ])->render();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success'   => true,
            'detection' => $detection,
            'image_url' => Storage::url($path),
            'message'   => 'Deteksi berhasil!',
            'mode'      => $result['mode'], // 'api', 'not_detected', atau 'demo'
        ]);
    }

    // ═══════════════════════════════════════
    // WEATHER API
    // ═══════════════════════════════════════
    private function getApiOpenWeatherMap($lat, $lon)
    {
        $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => env('API_WEATHER_KEY'),
            'units' => 'metric',
        ]);

        if ($response->successful()) {
            $weather = $response->json();
            return [
                'status'   => 'success',
                'temp'     => $weather['main']['temp'] ?? null,
                'humidity' => $weather['main']['humidity'] ?? null,
                'desc'     => $weather['weather'][0]['description'] ?? null,
                'raw'      => $weather,
            ];
        }

        return [
            'status'  => 'error',
            'message' => 'Gagal mengambil data cuaca',
        ];
    }

    // ═══════════════════════════════════════
    // ENDPOINT PREDICT (opsional, untuk test langsung)
    // ═══════════════════════════════════════
    public function predict(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $image = $request->file('image');

        try {
            $response = Http::timeout(10)
                ->attach(
                    'image',
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )
                ->post(config('services.yolo.url', 'https://Bram.pythonanywhere.com/predict'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengakses API YOLO',
                'error'   => $e->getMessage(),
            ], 500);
        }

        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal mengakses API YOLO',
                'error'   => $response->body(),
            ], 500);
        }

        return response()->json($response->json());
    }

    // ═══════════════════════════════════════
    // INTEGRASI API YOLOv11 (Python / Maul)
    // ═══════════════════════════════════════
    private function callYOLOv8($imagePath, $filename)
{
    try {
        $response = Http::timeout(15)
            ->attach('image', file_get_contents($imagePath), $filename)
            ->post(config('services.yolo.url', 'https://Bram.pythonanywhere.com/predict'));
        
        if ($response->failed()) {
            return $this->demoDetection();
        }

        $data = $response->json() ?? [];

        // ── Handle not_detected dari API ──
        if (isset($data['status']) && $data['status'] === 'not_detected') {
            return [
                'disease_name'    => 'Tidak Terdeteksi',
                'status'          => 'tidak_terdeteksi',
                'description'     => $data['message'] ?? 'Model tidak menemukan penyakit pada gambar ini.',
                'confidence'      => 0,
                'bounding_box'    => null,
                'annotated_image' => $data['annotated_image'] ?? null,
                'raw'             => $data,
                'mode'            => 'not_detected',
            ];
        }

        // ── Parse nama penyakit ──
        $diseaseName = $data['disease_name']
            ?? $data['disease']
            ?? $data['class']
            ?? $data['label']
            ?? null;

        // ── Handle "Tidak Terdeteksi" dari Maul ──
        if (strtolower(trim($diseaseName ?? '')) === 'tidak terdeteksi') {
            return [
                'disease_name'    => 'Tidak Terdeteksi',
                'status'          => 'tidak_terdeteksi',
                'description'     => $data['description'] ?? 'Model tidak menemukan penyakit pada gambar ini.',
                'confidence'      => 0,
                'bounding_box'    => null,
                'annotated_image' => $data['annotated_image'] ?? null,
                'raw'             => $data,
                'mode'            => 'not_detected',
            ];
        }

        // ── Handle nama kosong ──
        if (empty($diseaseName)) {
            return [
                'disease_name'    => 'Tidak Terdeteksi',
                'status'          => 'tidak_terdeteksi',
                'description'     => 'Model tidak menemukan objek yang dikenali pada gambar.',
                'confidence'      => 0,
                'bounding_box'    => null,
                'annotated_image' => $data['annotated_image'] ?? null,
                'raw'             => $data,
                'mode'            => 'not_detected',
            ];
        }

        // ── Normalize confidence ──
        $confidence = $data['confidence'] ?? $data['score'] ?? 0;
        if (is_numeric($confidence) && $confidence <= 1.0) {
            $confidence = $confidence * 100;
        }

        // ── Status selalu dari CLASS_STATUS_MAP, bukan dari API ──
        $status = $this->resolveStatus($diseaseName);

        return [
            'disease_name'    => $diseaseName,
            'status'          => $status,
            'description'     => $data['description'] ?? $this->resolveDescription($diseaseName),
            'confidence'      => round((float) $confidence, 2),
            'bounding_box'    => $data['bounding_box'] ?? $data['bbox'] ?? null,
            'annotated_image' => $data['annotated_image'] ?? null, // ← fix utama
            'raw'             => $data,
            'mode'            => 'api',
        ];

    } catch (\Exception $e) {
        return $this->demoDetection();
    }
}

    // ═══════════════════════════════════════
    // HELPER: Tentukan status dari nama class
    // ═══════════════════════════════════════
    private function resolveStatus(string $diseaseName): string
    {
        $key = strtolower(trim($diseaseName));
        return self::CLASS_STATUS_MAP[$key] ?? 'sakit'; // default sakit kalau tidak dikenali
    }

    // ═══════════════════════════════════════
    // HELPER: Deskripsi default per penyakit
    // (dipakai kalau API Maul tidak return description)
    // ═══════════════════════════════════════
    private function resolveDescription(string $diseaseName): string
    {
        $desc = [
            'Bercak Daun'           => 'Terdeteksi gejala Bercak Daun (Leaf Spot). Ditemukan bercak-bercak tidak normal pada permukaan daun. Lakukan pemeriksaan lebih lanjut dan pertimbangkan penggunaan fungisida.',
            'Fusarium Wilt'         => 'Terdeteksi indikasi Fusarium Wilt. Infeksi jamur Fusarium menyebabkan layu dan perubahan warna daun. Segera lakukan penanganan untuk mencegah penyebaran ke pohon lain.',
            'Magnesium Deficiency'  => 'Terdeteksi kekurangan Magnesium. Ditandai dengan daun menguning dimulai dari tepi. Tambahkan pupuk Magnesium (dolomit/kieserit) sesuai dosis anjuran.',
            'Manganese Deficiency'  => 'Terdeteksi kekurangan Mangan. Daun menunjukkan gejala klorosis antar tulang daun. Lakukan pemupukan Mangan dan periksa pH tanah.',
            'Potassium Deficiency'  => 'Terdeteksi kekurangan Kalium. Ditandai dengan bercak oranye kecoklatan pada daun tua. Tambahkan pupuk KCl atau MOP sesuai rekomendasi.',
            'Rachis Blight'         => 'Terdeteksi gejala Rachis Blight. Ditemukan kerusakan pada rachis (tangkai daun). Pantau perkembangan dan konsultasikan dengan ahli perkebunan.',
            'SEHAT'            => 'Tidak ditemukan indikasi penyakit. Kondisi daun tampak normal dengan warna dan tekstur yang sehat. Pertahankan kondisi perawatan yang ada.',
        ];

        return $desc[$diseaseName]
            ?? $desc[ucwords(strtolower($diseaseName))]
            ?? 'Hasil deteksi dari model YOLOv11. Lakukan pemeriksaan lapangan untuk konfirmasi lebih lanjut.';
    }

    // ═══════════════════════════════════════
    // MODE DEMO (saat API YOLOv11 belum siap / tidak bisa diakses)
    // CATATAN: Method ini TIDAK dihapus bahkan setelah API siap.
    // Ini adalah fallback otomatis kalau koneksi ke API Maul gagal.
    // ═══════════════════════════════════════
    private function demoDetection()
    {
        // Nama class disesuaikan dengan model best.pt Maul
        $penyakit = [
            [
                'name'       => 'Fusarium Wilt',
                'status'     => 'sakit',
                'confidence' => [82, 91],
                'desc'       => 'Terdeteksi indikasi kuat penyakit Fusarium Wilt. Ditemukan pola kerusakan jaringan daun yang khas akibat infeksi jamur Fusarium oxysporum. Segera lakukan penanganan untuk mencegah penyebaran.',
                'bbox'       => ['x' => 45,  'y' => 38,  'width' => 320, 'height' => 290],
            ],
            [
                'name'       => 'Bercak Daun',
                'status'     => 'sakit',
                'confidence' => [78, 89],
                'desc'       => 'Terdeteksi gejala Bercak Daun (Leaf Spot). Pola bercak tidak normal mengindikasikan infeksi jamur atau bakteri. Diperlukan pemeriksaan lanjutan di lapangan.',
                'bbox'       => ['x' => 60,  'y' => 55,  'width' => 280, 'height' => 260],
            ],
            [
                'name'       => 'Magnesium Deficiency',
                'status'     => 'waspada',
                'confidence' => [74, 85],
                'desc'       => 'Terdeteksi gejala kekurangan Magnesium. Daun menguning dari tepi menunjukkan defisiensi mineral. Segera lakukan pemupukan Magnesium.',
                'bbox'       => ['x' => 80,  'y' => 70,  'width' => 240, 'height' => 220],
            ],
            [
                'name'       => 'Manganese Deficiency',
                'status'     => 'waspada',
                'confidence' => [71, 83],
                'desc'       => 'Terdeteksi gejala kekurangan Mangan. Klorosis antar tulang daun mengindikasikan defisiensi Mangan. Periksa pH tanah dan lakukan pemupukan.',
                'bbox'       => ['x' => 55,  'y' => 48,  'width' => 300, 'height' => 270],
            ],
            [
                'name'       => 'Potassium Deficiency',
                'status'     => 'waspada',
                'confidence' => [71, 83],
                'desc'       => 'Terdeteksi gejala kekurangan Kalium. Bercak oranye kecoklatan pada daun tua menandakan defisiensi Kalium. Tambahkan pupuk KCl sesuai dosis.',
                'bbox'       => ['x' => 55,  'y' => 48,  'width' => 300, 'height' => 270],
            ],
            [
                'name'       => 'Rachis Blight',
                'status'     => 'sakit',
                'confidence' => [75, 88],
                'desc'       => 'Terdeteksi gejala Rachis Blight. Ditemukan kerusakan pada rachis daun. Lakukan pemeriksaan menyeluruh dan konsultasikan dengan ahli perkebunan.',
                'bbox'       => ['x' => 55,  'y' => 48,  'width' => 300, 'height' => 270],
            ],
            [
                'name'       => 'daun sehat',
                'status'     => 'sehat',
                'confidence' => [88, 97],
                'desc'       => 'Tidak ditemukan indikasi penyakit pada daun yang dianalisis. Kondisi daun tampak normal dengan warna dan tekstur yang sehat. Pertahankan kondisi perawatan yang ada.',
                'bbox'       => ['x' => 100, 'y' => 90,  'width' => 200, 'height' => 180],
            ],
        ];

        $pick       = $penyakit[array_rand($penyakit)];
        $confidence = rand($pick['confidence'][0], $pick['confidence'][1]);

        return [
            'disease_name' => $pick['name'],
            'status'       => $pick['status'],
            'description'  => '[MODE DEMO] ' . $pick['desc'],
            'confidence'   => $confidence,
            'bounding_box' => $pick['bbox'],
            'raw'          => null,
            'mode'         => 'demo',
        ];
    }

    // ═══════════════════════════════════════
    // DETAIL DETEKSI
    // ═══════════════════════════════════════
    public function show($id)
    {
        $detection = Detection::findOrFail($id);

        $latest = ['temp' => null, 'humidity' => null];

        if ($detection && $detection->pohon && $detection->pohon->kebun) {
            $latest = [
                'temp'     => $detection->pohon->kebun->suhu ?? null,
                'humidity' => $detection->pohon->kebun->kelembapan ?? null,
            ];
        }

        return view('pages.detail', [
            'detection' => $detection,
            'latest'    => $latest,
        ]);
    }

    // ═══════════════════════════════════════
    // HAPUS SATU DATA
    // ═══════════════════════════════════════
    public function destroy($id)
    {
        $detection = Detection::findOrFail($id);
        Storage::disk('public')->delete($detection->image_path);
        if ($detection->kebun_foto_path) {
            Storage::disk('public')->delete($detection->kebun_foto_path);
        }
        $detection->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    // ═══════════════════════════════════════
    // HAPUS SEMUA DATA
    // ═══════════════════════════════════════
    public function destroyAll()
    {
        $detections = Detection::all();
        foreach ($detections as $d) {
            Storage::disk('public')->delete($d->image_path);
            if ($d->kebun_foto_path) {
                Storage::disk('public')->delete($d->kebun_foto_path);
            }
        }
        Detection::truncate();

        return response()->json(['success' => true, 'message' => 'Semua data berhasil dihapus.']);
    }

    // ═══════════════════════════════════════
    // DOWNLOAD PDF
    // ═══════════════════════════════════════
    public function downloadPdf($id)
    {
        $detection = Detection::with('pohon')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.report', compact('detection'));

        $namaPohon = optional($detection->pohon)->nama_pohon
                    ? str_replace(' ', '_', $detection->pohon->nama_pohon)
                    : 'deteksi';

        return $pdf->download('laporan_' . $namaPohon . '.pdf');
    }

    // ═══════════════════════════════════════
    // EXPORT CSV
    // ═══════════════════════════════════════
    public function exportCsv()
    {
        $detections = Detection::with(['pohon.kebun'])->latest()->get();
        $filename   = 'riwayat_deteksi_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($detections) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Nama File',
                'Nama Penyakit',
                'Status',
                'Confidence (%)',
                'Nama Pohon',
                'Latitude',
                'Longitude',
                'Suhu (°C)',
                'Kelembapan (%)',
                'Ukuran File',
                'Sumber',
                'Tanggal',
                'Jam',
            ]);

            foreach ($detections as $d) {
                fputcsv($file, [
                    $d->id,
                    $d->filename,
                    $d->disease_name,
                    $d->status,
                    $d->confidence,
                    optional($d->pohon)->nama_pohon ?? '-',
                    optional($d->pohon)->latitude ?? '-',
                    optional($d->pohon)->longitude ?? '-',
                    optional($d->pohon->kebun)->suhu ?? '-',
                    optional($d->pohon->kebun)->kelembapan ?? '-',
                    $d->file_size,
                    $d->source,
                    $d->created_at->format('d/m/Y'),
                    $d->created_at->format('H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

} // ← penutup class DetectionController