@extends('layouts.app')

@section('title', 'Manage Users')
@section('header', '👥 Manage Users')
@section('subheader', 'Kelola akun dan peran pengguna.')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Nama</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Role</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Hapus {{ $user->name }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-gray-400 hover:text-red-500 transition font-medium">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-300 italic">kamu</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm max-w-xl">
        <h2 class="font-bold text-gray-800 mb-4">Tambah User</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-1.5">Role</label>
                    <select id="role" name="role" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="user" @selected(old('role') === 'user' || ! old('role'))>user</option>
                        <option value="admin" @selected(old('role') === 'admin')>admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition">
                Simpan
            </button>
        </form>
    </div>

</div>
@endsection
