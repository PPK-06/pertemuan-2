@extends('layouts.guest')

@section('title', 'Masuk — Jara')
@section('heading', 'Selamat datang kembali 👋')
@section('subheading', 'Masuk untuk lanjut mengelola project dan tugasmu.')
@section('footer')
    Belum punya akun?
    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Daftar gratis</a>
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-900">Masuk</h2>
    <p class="text-slate-500 text-sm mt-1">Senang melihatmu lagi.</p>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="nama@email.com"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm
                      {{ $errors->has('email') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input id="password" type="password" name="password" required
               placeholder="••••••••"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition shadow-sm shadow-indigo-200">
        Masuk
    </button>
</form>
@endsection
