@extends('layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')

{{-- HEADER --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h2 style="color:#6fcf97;font-size:1.2rem;font-weight:700;margin:0">👥 Kelola Pengguna</h2>
        <p style="color:#666;font-size:0.8rem;margin:0.25rem 0 0">Manajemen akun Operator & Manager PalmGuard</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'"
        class="btn-primary" style="font-size:0.85rem;padding:0.6rem 1.25rem">
        ➕ Tambah Pengguna
    </button>
</div>

{{-- TABEL PENGGUNA --}}
<div class="card">
    @forelse($users as $u)
    <div class="user-item" id="user-{{ $u->id }}">
        <div class="user-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
        <div class="user-info">
            <span class="user-name">{{ $u->name }}</span>
            <span class="user-email">{{ $u->email }}</span>
        </div>
        <div class="user-meta">
            @if($u->role === 'operator')
                <span class="badge badge-operator">Operator</span>
            @elseif($u->role === 'manager')
                <span class="badge badge-manager">Manager</span>
            @endif
            <span style="color:#555;font-size:0.75rem;font-family:'IBM Plex Mono',monospace">
                {{ $u->created_at->format('d M Y') }}
            </span>
        </div>
        <button class="btn-del-user" onclick="hapusUser({{ $u->id }}, '{{ $u->name }}')">
            🗑️ Hapus
        </button>
    </div>
    @empty
    <div style="text-align:center;padding:3rem 1rem;color:#555">
        <div style="font-size:2.5rem">👥</div>
        <p style="margin:0.5rem 0 0">Belum ada pengguna</p>
        <p style="font-size:0.8rem;margin-top:0.25rem">Tambahkan Operator atau Manager baru</p>
    </div>
    @endforelse
</div>

{{-- MODAL TAMBAH PENGGUNA --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center">
    <div style="background:#0b1a10;border:1px solid #1e3d28;border-radius:16px;padding:32px;width:100%;max-width:420px;margin:1rem">
        <h3 style="color:#6fcf97;margin:0 0 1.5rem;font-size:1rem">➕ Tambah Pengguna Baru</h3>

        <div id="modalError" style="display:none;background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.3);border-radius:8px;padding:10px 14px;font-size:12px;color:#e05252;margin-bottom:1rem"></div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Nama</label>
            <input type="text" id="inp_name" placeholder="Nama lengkap"
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Email</label>
            <input type="email" id="inp_email" placeholder="email@palmguard.com"
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Role</label>
            <select id="inp_role" 
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
                <option value="operator">Operator</option>
                <option value="manager">Manager</option>
            </select>
        </div>

        <div style="margin-bottom:1.5rem">
            <label style="display:block;color:#6b9e7a;font-size:0.78rem;font-weight:600;margin-bottom:0.4rem;text-transform:uppercase">Password</label>
            <input type="password" id="inp_password" placeholder="Minimal 6 karakter"
                style="width:100%;background:#0f2318;border:1px solid #1e3d28;color:#d4edd9;padding:10px 14px;border-radius:8px;font-size:0.88rem;outline:none;box-sizing:border-box">
        </div>

        <div style="display:flex;gap:0.75rem">
            <button onclick="tambahPengguna()" class="btn-primary" style="flex:1;padding:0.7rem">
                ✅ Simpan
            </button>
            <button onclick="document.getElementById('modalTambah').style.display='none'"
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
.badge-manager{background:rgba(46,204,113,0.15);color:#2ecc71;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.72rem;font-weight:600}

.btn-del-user{background:rgba(224,82,82,0.1);color:#e05252;border:1px solid rgba(224,82,82,0.3);padding:0.4rem 0.75rem;border-radius:8px;cursor:pointer;font-size:0.78rem;flex-shrink:0}
.btn-del-user:hover{background:rgba(224,82,82,0.2)}
.btn-primary{background:linear-gradient(135deg,#2d7a4f,#1a4d2e);color:#fff;border:none;padding:0.7rem 2rem;border-radius:10px;cursor:pointer;font-size:0.95rem;font-weight:600}
</style>

<script>
async function tambahPengguna() {
    const name     = document.getElementById('inp_name').value.trim();
    const email    = document.getElementById('inp_email').value.trim();
    const role     = document.getElementById('inp_role').value;
    const password = document.getElementById('inp_password').value;
    const errBox   = document.getElementById('modalError');

    if (!name || !email || !role || !password) {
        errBox.style.display = 'block';
        errBox.textContent = '⚠️ Semua field wajib diisi.';
        return;
    }

    const res = await fetch('{{ route("users.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            name, 
            email, 
            password,
            role 
        })
    });

    const data = await res.json();

    if (data.success) {
        document.getElementById('modalTambah').style.display = 'none';
        location.reload();
    } else {
        errBox.style.display = 'block';
        errBox.textContent = '⚠️ ' + (data.message || 'Terjadi kesalahan.');
    }
}

async function hapusUser(id, name) {
    if (!confirm(`Hapus akun "${name}"?`)) return;

    const res = await fetch(`/users/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    });

    const data = await res.json();
    if (data.success) {
        document.getElementById('user-' + id).remove();
    } else {
        alert(data.message || 'Gagal menghapus akun.');
    }
}
</script>

@endsection