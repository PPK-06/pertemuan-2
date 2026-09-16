<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    /**
     * Menampilkan form login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses data dari form login.
     */
    public function login(Request $request)
    {
        // 1. Validasi format input dulu (bukan validasi kredensial).
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba autentikasi. Kalau gagal, lempar ValidationException
        //    supaya Laravel redirect back dengan pesan error + old input,
        //    sama seperti perilaku $request->validate() di atas.
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // 3. Regenerate session id supaya tidak kena session fixation,
        //    lalu arahkan ke halaman yang tadinya ingin diakses (kalau ada).
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate + regenerate token supaya session lama benar-benar
        // tidak bisa dipakai lagi (mencegah session fixation/replay).
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
