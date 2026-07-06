<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Tampilkan semua Operator dan Manager (tidak tampilkan Admin)
        $users = User::whereIn('role', ['operator', 'manager'])
                     ->latest()
                     ->get();

        return view('pages.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:operator,manager',   // Hanya boleh pilih operator atau manager
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
            'role.in'           => 'Role yang dipilih tidak valid.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,        // ← Sekarang bisa 'operator' atau 'manager'
        ]);

        return response()->json([
            'success' => true, 
            'message' => $request->role === 'manager' 
                        ? 'Manager berhasil ditambahkan.' 
                        : 'Operator berhasil ditambahkan.'
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Tidak boleh menghapus Admin
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak bisa menghapus akun Admin.'
            ]);
        }

        // Opsional: Manager tidak boleh menghapus Operator (kalau mau dibatasi)
        // if (auth()->user()->isManager() && $user->role === 'operator') {
        //     return response()->json(['success' => false, 'message' => 'Manager tidak boleh menghapus Operator.']);
        // }

        $user->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Akun berhasil dihapus.'
        ]);
    }
}