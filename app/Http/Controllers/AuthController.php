<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan form pendaftaran.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses data dari form pendaftaran.
     */
    public function register(Request $request)
    {
        // 1. Validasi. Kalau gagal, Laravel otomatis redirect back
        //    membawa pesan error + old input, baris di bawah tidak dijalankan.
        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // 2. Simpan user baru.
        //    role di-hardcode 'user' dan TIDAK diambil dari input,
        //    supaya tidak ada yang bisa mendaftar langsung sebagai admin.
        //    Password di-hash otomatis oleh cast 'hashed' di model User.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
        ]);

        // 3. Langsung loginkan user yang baru mendaftar.
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
