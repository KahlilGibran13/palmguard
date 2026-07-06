@extends('layouts.app')
@section('title', 'Kelola Operator')
@section('content')

{{-- HEADER --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h2 style="color:#6fcf97;font-size:1.2rem;font-weight:700;margin:0">👥 Kelola Jenis Penyakit</h2>
        <p style="color:#666;font-size:0.8rem;margin:0.25rem 0 0">Manajemen jenis penyakit pada PalmGuard</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'"
        class="btn-primary" style="font-size:0.85rem;padding:0.6rem 1.25rem">
        ➕ Tambah Penyakit
    </button>
</div>

{{-- TABEL OPERATOR --}}
<div class="card">
    @forelse($penyakit as $p)
    <div class="user-item" id="penyakit-{{ $p->id }}">
        <div class="user-info">
            <span class="user-name">{{ $p->nama_penyakit }}</span>
        </div>
        <button class="btn-edit-user" onclick="bukaModalUbah({{ $p->load('ciriCiri') }})">
            Ubah
        </button>
        
        <button class="btn-del-user" onclick="hapusPenyakit({{ $p->id }}, '{{ $p->nama_penyakit }}')">
             Hapus
        </button>
    </div>
    @empty
    <div style="text-align:center;padding:3rem 1rem;color:#555">
        <div style="font-size:2.5rem">🌿</div>
        <p style="margin:0.5rem 0 0">Belum ada jenis penyakit</p>
        <p style="font-size:0.8rem;margin-top:0.25rem">Tambahkan jenis penyakit baru dengan tombol di atas</p>
    </div>
    @endforelse
</div>

{{-- MODAL TAMBAH PENYAKIT --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center">
    <div style="background:#0b1a10;border:1px solid #1e3d28;border-radius:16px;padding:32px;width:100%;max-width:420px;margin:1rem">
        <h3 style="color:#6fcf97;margin:0 0 1.5rem;font-size:1rem">➕ Tambah Jenis Penyakit Baru</h3>

        <div id="modalError" style="display:none;background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.3);border-radius:8px;padding:10px 14px;font-size:12px;color:#e05252;margin-bottom:1rem"></div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Nama Penyakit</label>
            <input type="text" id="inp_name" placeholder="Nama penyakit"
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Status Penyakit</label>
            <select id="inp_status" 
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box;cursor:pointer;appearance:none;-webkit-appearance:none;">
                <option value="" disabled selected>Pilih Status...</option>
                <option value="Sehat" style="background:#0b1a10; color:#2d7a4f;">🟢 Sehat</option>
                <option value="Waspada" style="background:#0b1a10; color:#f5a623;">🟡 Waspada</option>
                <option value="Sakit" style="background:#0b1a10; color:#e05252;">🔴 Sakit</option>
            </select>
        </div>

        <div style="margin-bottom:1.5rem">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                <label style="display:block;color:#6b9e7a;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px">Ciri-Ciri Penyakit</label>
                <button type="button" onclick="tambahInputCiri()" style="background:transparent;border:none;color:#1DB954;font-size:0.75rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px">
                    + TAMBAH LIST
                </button>
            </div>
            
            <div id="container-ciri">
            </div>
        </div>

        <div style="display:flex;gap:0.75rem">
            <button onclick="simpanPenyakit()" class="btn-primary" style="flex:1;padding:0.7rem">
                ✅ Simpan
            </button>
            <button onclick="document.getElementById('modalTambah').style.display='none'"
                style="flex:1;padding:0.7rem;background:transparent;border:1px solid #1e3d28;color:#6b9e7a;border-radius:10px;cursor:pointer;font-size:0.88rem">
                Batal
            </button>
        </div>
    </div>
</div>


{{-- MODAL UBAH PENYAKIT --}}
<div id="modalUbah" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center">
    <div style="background:#0b1a10;border:1px solid #1e3d28;border-radius:16px;padding:32px;width:100%;max-width:420px;margin:1rem">
        <h3 style="color:#6fcf97;margin:0 0 1.5rem;font-size:1rem">📝 Ubah Jenis Penyakit</h3>

        <input type="hidden" id="edit_id">

        <div id="edit_modalError" style="display:none;background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.3);border-radius:8px;padding:10px 14px;font-size:12px;color:#e05252;margin-bottom:1rem"></div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Nama Penyakit</label>
            <input type="text" id="edit_inp_name" 
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Status Penyakit</label>
            <select id="edit_inp_status" 
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box;cursor:pointer;">
                <option value="Sehat">🟢 Sehat</option>
                <option value="Waspada">🟡 Waspada</option>
                <option value="Sakit">🔴 Sakit</option>
            </select>
        </div>

        <div style="margin-bottom:1.5rem">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                <label style="display:block;color:#6b9e7a;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px">Ciri-Ciri Penyakit</label>
                <button type="button" onclick="tambahInputCiri('edit')" style="background:transparent;border:none;color:#1DB954;font-size:0.75rem;font-weight:700;cursor:pointer;">
                    + TAMBAH LIST
                </button>
            </div>
            <div id="edit_container-ciri"></div>
        </div>

        <div style="display:flex;gap:0.75rem">
            <button onclick="updatePenyakit()" class="btn-primary" style="flex:1;padding:0.7rem">
                ✅ Simpan
            </button>
            <button onclick="document.getElementById('modalUbah').style.display='none'"
                style="flex:1;padding:0.7rem;background:transparent;border:1px solid #1e3d28;color:#6b9e7a;border-radius:10px;cursor:pointer;font-size:0.88rem">
                Batal
            </button>
        </div>
    </div>
</div>

<style>
.user-item{display:flex;align-items:center;gap:1rem;padding:1rem 0;border-bottom:1px solid #0d1f12}
.user-item:last-child{border-bottom:none}
.user-avatar{width:44px;height:44px;background:#1a3d22;border:1px solid #2d7a4f;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#6fcf97;font-weight:700;font-size:1.1rem;flex-shrink:0}
.user-info{flex:1;min-width:0}
.user-name{display:block;color:#e0e0e0;font-weight:600;font-size:0.9rem}
.user-email{display:block;color:#666;font-size:0.78rem;font-family:'IBM Plex Mono',monospace;margin-top:0.15rem}
.user-meta{display:flex;flex-direction:column;align-items:flex-end;gap:0.3rem;flex-shrink:0}
.badge-operator{background:rgba(74,158,255,0.15);color:#4a9eff;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:600}
.btn-del-user{background:rgba(224,82,82,0.1);color:#e05252;border:1px solid rgba(224,82,82,0.3);padding:0.4rem 0.75rem;border-radius:8px;cursor:pointer;font-size:0.78rem;flex-shrink:0}
.btn-del-user:hover{background:rgba(224,82,82,0.2)}
.btn-edit-user{background:rgba(111,207,151,0.1);color:#6fcf97;border:1px solid rgba(111,207,151,0.3);padding:0.4rem 0.75rem;border-radius:8px;cursor:pointer;font-size:0.78rem;flex-shrink:0}
.btn-edit-user:hover{background:rgba(111,207,151,0.2)}
.btn-primary{background:linear-gradient(135deg,#2d7a4f,#1a4d2e);color:#fff;border:none;padding:0.7rem 2rem;border-radius:10px;cursor:pointer;font-size:0.95rem;font-weight:600}
</style>

<script>

function bukaModalUbah(data) {
    // 1. Reset container ciri-ciri
    const container = document.getElementById('edit_container-ciri');
    container.innerHTML = '';

    // 2. Isi Nama, ID, dan Status
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_inp_name').value = data.nama_penyakit;
    document.getElementById('edit_inp_status').value = data.status;

    // 3. Loop ciri-ciri untuk dimasukkan ke list textbox
    // 'data.ciri_penyakit' adalah relasi hasMany dari Laravel
    if (data.ciri_ciri && data.ciri_ciri.length > 0) {
        data.ciri_ciri.forEach((item, index) => {
            const nomorUrut = index + 1;
            const div = document.createElement('div');
            div.className = 'ciri-item';
            div.style.display = 'flex'; div.style.gap = '8px'; div.style.marginBottom = '8px';
            
            div.innerHTML = `
                <input type="text" name="edit_ciri_ciri[]" value="${item.ciri}" placeholder="Ciri ke-${nomorUrut}..."
                    style="flex:1;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:10px;font-size:0.88rem;outline:none;">
                <button type="button" onclick="this.parentElement.remove()" style="color:#e05252; background:none; border:none; cursor:pointer; font-weight:bold;">×</button>
            `;
            container.appendChild(div);
        });
    }

    document.getElementById('modalUbah').style.display = 'flex';
}

function tambahInputCiri(mode = 'tambah') {
    const containerId = (mode === 'edit') ? 'edit_container-ciri' : 'container-ciri';
    const inputName = (mode === 'edit') ? 'edit_ciri_ciri[]' : 'ciri_ciri[]';
    
    const container = document.getElementById(containerId);
    
    const nomorUrut = container.children.length + 1;
    
    // Buat wrapper div
    const div = document.createElement('div');
    div.className = 'ciri-item';
    div.style.display = 'flex';
    div.style.gap = '8px';
    div.style.marginBottom = '8px';
    
    // Buat input baru
    div.innerHTML = `
       <input type="text" name="${inputName}" placeholder="Ciri ke-${nomorUrut}..."
            style="flex:1;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:10px;font-size:0.88rem;outline:none;box-sizing:border-box">
        <button type="button" onclick="hapusCiri(this, '${mode}')" 
            style="background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.2);color:#e05252;border-radius:10px;padding:0 12px;cursor:pointer;font-weight:bold">
            ×
        </button>
    `;
    
    container.appendChild(div);
}

function hapusCiri(btn, mode) {
    const containerId = (mode === 'edit') ? 'edit_container-ciri' : 'container-ciri';
    const container = document.getElementById(containerId);
    
    btn.parentElement.remove();
    
    // Rapikan ulang placeholder setelah ada yang dihapus
    const items = container.querySelectorAll('.ciri-item input');
    items.forEach((input, index) => {
        input.placeholder = `Ciri ke-${index + 1}...`;
    });
}

async function simpanPenyakit() {
    const namaPenyakit = document.getElementById('inp_name').value;
    const statusPenyakit = document.getElementById('inp_status').value;
    const ciriCiri = document.querySelectorAll('input[name="ciri_ciri[]"]');

    if (!namaPenyakit) {
        tampilkanError("Nama penyakit tidak boleh kosong!");
        return;
    }

    if (!statusPenyakit) {
        tampilkanError("Status penyakit tidak boleh kosong!");
        return;
    }

    let daftarCiri = [];
    ciriCiri.forEach(input => {
        if (input.value.trim() !== "") {
            daftarCiri.push(input.value.trim());
        }
    });

    if (daftarCiri.length === 0) {
        tampilkanError("Minimal harus ada satu ciri-ciri yang diisi!");
        return;
    }

    try {
        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('nama', namaPenyakit);
        fd.append('status', statusPenyakit);
        fd.append('ciri_ciri', JSON.stringify(daftarCiri));
        console.log('FormData:', [...fd.entries()]);

        const res  = await fetch('{{ route("penyakits.store") }}', {method:'POST', body:fd});
        console.log('HTTP Status:', res.status);
        if (!res.ok) {
            const text = await res.text();
            console.error('Response:', text);
            throw new Error('HTTP ' + res.status);
        }
        
        const data = await res.json();

        if (data.success) {
            alert('Penyakit berhasil disimpan!');
            document.getElementById('modalTambah').style.display='none';
            location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan penyakit.');
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
}

async function updatePenyakit() {
    const id = document.getElementById('edit_id').value;
    const namaPenyakit = document.getElementById('edit_inp_name').value;
    const statusPenyakit = document.getElementById('edit_inp_status').value;
    const ciriCiri = document.querySelectorAll('input[name="edit_ciri_ciri[]"]');
    
     if (!namaPenyakit) {
        tampilkanError("Nama penyakit tidak boleh kosong!");
        return;
    }

    if (!statusPenyakit) {
        tampilkanError("Status penyakit tidak boleh kosong!");
        return;
    }

    let daftarCiri = [];
    ciriCiri.forEach(input => {
        if (input.value.trim() !== "") {
            daftarCiri.push(input.value.trim());
        }
    });

    if (daftarCiri.length === 0) {
        tampilkanError("Minimal harus ada satu ciri-ciri yang diisi!");
        return;
    }

    try {
        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'PUT');
        fd.append('nama', namaPenyakit);
        fd.append('status', statusPenyakit);
        fd.append('ciri_ciri', JSON.stringify(daftarCiri));
        console.log('FormData:', [...fd.entries()]);

        const res  = await fetch('{{ route("penyakits.update", ":id") }}'.replace(':id', id), {method:'POST', body:fd});
        console.log('HTTP Status:', res.status);
        if (!res.ok) {
            const text = await res.text();
            console.error('Response:', text);
            throw new Error('HTTP ' + res.status);
        }
        
        const data = await res.json();

        if (data.success) {
            alert('Penyakit berhasil diubah!');
            document.getElementById('modalUbah').style.display='none';
            location.reload();
        } else {
            alert(data.message || 'Gagal mengubah penyakit.');
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
    
}

function tampilkanError(pesan) {
    const errDiv = document.getElementById('modalError');
    errDiv.innerText = pesan;
    errDiv.style.display = 'block';
}

async function hapusPenyakit(id, name) {
    if (!confirm(`Hapus penyakit "${name}"?`)) return;

    const res = await fetch(`/penyakits/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    });

    const data = await res.json();
    if (data.success) {
        document.getElementById('penyakit-' + id).remove();
    } else {
        alert(data.message);
    }
}
</script>

@endsection