<?php

namespace App\Http\Controllers;

use App\Models\Penyakit;
use App\Models\CiriPenyakit;
use Illuminate\Http\Request;

class PenyakitController extends Controller
{
    public function index()
    {
        $penyakit = Penyakit::latest()->get();
        return view('pages.penyakit', compact('penyakit'));
    }

    public function store(Request $request)
    {
        if ($request->has('ciri_ciri')) {
            $request->merge([
                'ciri_ciri' => json_decode($request->ciri_ciri, true),
            ]);
        }

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'status'    => 'required|string|max:255',
            'ciri_ciri' => 'required|array|min:1',
            'ciri_ciri.*' => 'required|string',
        ], [
            'nama.required' => 'Nama penyakit wajib diisi.',
            'status.required' => 'Status penyakit wajib diisi.',
            'ciri_ciri.required' => 'Minimal isi satu ciri penyakit.',
        ]);

        $penyakit = Penyakit::create([
            'nama_penyakit' => $validated['nama'],
            'status'        => $validated['status'],
            'warna_badge'   => $this->getWarnaBadge($validated['status'])
        ]);

        foreach ($validated['ciri_ciri'] as $item) {
            CiriPenyakit::create([
                'id_penyakit' => $penyakit->id,
                'ciri'        => $item,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Penyakit berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $penyakit = Penyakit::findOrFail($id);
        
        if ($request->has('ciri_ciri')) {
            $request->merge([
                'ciri_ciri' => json_decode($request->ciri_ciri, true),
            ]);
        }

        $request->validate([
            'nama' => 'required|string',
            'status' => 'required|in:Sehat,Waspada,Sakit',
            'ciri_ciri' => 'required|array'
        ]);

        $penyakit->update([
            'nama_penyakit' => $request->nama,
            'status' => $request->status,
            'warna_badge'   => $this->getWarnaBadge($request->status)
        ]);

        $penyakit->ciriCiri()->delete();
        foreach ($request->ciri_ciri as $c) {
            $penyakit->ciriCiri()->create(['ciri' => $c]);
        }

        return response()->json(['success' => true, 'message' => 'Penyakit berhasil diperbarui.']);
    }

    public function destroy($id)
    {
       $penyakit = Penyakit::findOrFail($id);
        $penyakit->ciriCiri()->delete();
        $penyakit->delete();
        return response()->json(['success' => true, 'message' => 'Penyakit berhasil dihapus.']);
    }

    private function getWarnaBadge($status)
    {
        switch (strtolower($status)) {
            case 'sakit':
                return '#e05252';
            case 'waspada':
                return '#f5a623';
            case 'sehat':
                return '#2d7a4f';
            default:
                return '#6c757d'; 
        }
    }
}