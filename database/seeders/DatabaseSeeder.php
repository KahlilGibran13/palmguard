<?php

namespace Database\Seeders;

use App\Models\Penyakit;
use App\Models\CiriPenyakit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $penyakit1 = Penyakit::create([
            'nama_penyakit' => 'Ganoderma Butt Rot',
            // 'deskripsi' => 'Penyakit busuk pangkal batang yang disebabkan oleh jamur Ganoderma boninense.',
            'warna_badge' => '#e05252',
            'status' => 'sakit'
        ]);

        $penyakit2 = Penyakit::create([
            'nama_penyakit' => 'Basal Stem Rot',
            // 'deskripsi' => 'Busuk batang dasar yang menyebabkan kematian tanaman secara perlahan.',
            'warna_badge' => '#e05252',
            'status' => 'Sakit'
        ]);

        $penyakit3 = Penyakit::create([
            'nama_penyakit' => 'Crown Disease',
            // 'deskripsi' => 'Kelainan pada perkembangan daun muda.',
            'warna_badge' => '#f5a623',
            'status' => 'Waspada'
        ]);

        $penyakit4 = Penyakit::create([
            'nama_penyakit' => 'Bud Rot',
            // 'deskripsi' => 'Pembusukan titik tumbuh (pucuk) tanaman kelapa sawit.',
            'warna_badge' => '#e05252',
            'status' => 'sakit'
        ]);

        $penyakit5 = Penyakit::create([
            'nama_penyakit' => 'Leaflet Blight',
            // 'deskripsi' => 'Hawar daun akibat infeksi jamur Pestalotiopsis sp.	',
            'warna_badge' => '#f5a623',
            'status' => 'Waspada'
        ]);
        
        $penyakit6 = Penyakit::create([
            'nama_penyakit' => 'Daun Sehat',
            // 'deskripsi' => 'Tidak ditemukan indikasi penyakit apapun pada tanaman.',
            'warna_badge' => '#2d7a4f',
            'status' => 'Sehat'
        ]);

        $ciripenyakit1 = [
            'Daun menguning dari bawah ke atas', 'Muncul tubuh buah jamur di pangkal batang', 'Batang membusuk dari dalam'
        ];


        $ciripenyakit2 = [
            'Daun tombak tidak membuka', 'Pelepah patah dan menggantung', 'Bau busuk dari pangkal batang'
        ];

        $ciripenyakit3 = [
            'Daun muda tidak membuka normal', 'Ujung daun mengering', 'Pertumbuhan terhambat'
        ];

        $ciripenyakit4 = [
            'Daun tombak membusuk', 'Bau tidak sedap dari pucuk', 'Jaringan pucuk berwarna coklat kehitaman'
        ];

        $ciripenyakit5 = [
           'Bercak coklat pada tepi daun', 'Daun mengering dari ujung', 'Bercak meluas ke seluruh daun'
        ];

        $ciripenyakit6 = [
            'Warna hijau cerah merata', 'Tidak ada bercak atau busuk', 'Pertumbuhan normal'
        ];

        foreach ($ciripenyakit1 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit1->id,
                'ciri'        => $c
            ]);
        }
        foreach ($ciripenyakit2 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit2->id,
                'ciri'        => $c
            ]);
        }
        foreach ($ciripenyakit3 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit3->id,
                'ciri'        => $c
            ]);
        }
        foreach ($ciripenyakit4 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit4->id,
                'ciri'        => $c
            ]);
        }
        foreach ($ciripenyakit5 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit5->id,
                'ciri'        => $c
            ]);
        }
        foreach ($ciripenyakit6 as $c) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit6->id,
                'ciri'        => $c
            ]);
        }

        $this->command->info('Seed Penyakit dan Ciri berhasil!');

        User::create([
            'name'     => 'Admin PalmGuard',
            'email'    => 'admin@palmguard.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin'
        ]);
        $this->command->info('Seed Admin User berhasil!');
    }
}
