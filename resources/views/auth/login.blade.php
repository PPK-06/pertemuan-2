@extends('layouts.guest')

@section('title', 'Masuk')
@section('heading', 'Selamat datang kembali')
@section('subheading', 'Masuk ke akun JARA kamu.')
@section('footer')
    Belum punya akun?
    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Daftar</a>
@endsection

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      {{ $errors->has('email') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
        <input id="password" type="password" name="password" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
        Masuk
    </button>
</form>
@endsection
