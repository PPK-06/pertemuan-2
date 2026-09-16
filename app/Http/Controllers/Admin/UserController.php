<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh user.
     */
    public function index()
    {
        $users = User::latest()->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Membuat user baru dari form admin.
     * Beda dengan register(): di sini admin boleh pilih role
     * ('admin' atau 'user') karena yang mengisi form adalah admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'in:admin,user'],
        ]);

        User::create($data);

        return redirect()->route('admin.users.index');
    }

    /**
     * Menghapus user.
     */
    public function destroy(User $user)
    {
        // Cegah admin menghapus akunnya sendiri supaya tidak
        // tidak sengaja mengunci diri sendiri dari sistem.
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->withErrors(['user' => 'Kamu tidak bisa menghapus akunmu sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index');
    }
}
