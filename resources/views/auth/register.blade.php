@extends('layouts.guest')

@section('title', 'Daftar — Jara')
@section('heading', 'Mulai kelola tugasmu 🚀')
@section('subheading', 'Buat akun gratis, undang tim, dan pantau progres harian.')
@section('footer')
    Sudah punya akun?
    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Masuk</a>
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-900">Buat akun</h2>
    <p class="text-slate-500 text-sm mt-1">Gratis, cuma butuh semenit.</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
               placeholder="Nama lengkapmu"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm
                      {{ $errors->has('name') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               placeholder="nama@email.com"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm
                      {{ $errors->has('email') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required
                   placeholder="Min. 8 karakter"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm
                          {{ $errors->has('password') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Ulangi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   placeholder="Ulangi password"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
        </div>
    </div>

    <button type="submit"
            class="w-full px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition shadow-sm shadow-indigo-200">
        Daftar
    </button>
</form>
@endsection
