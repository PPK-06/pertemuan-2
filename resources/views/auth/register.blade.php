@extends('layouts.guest')

@section('title', 'Daftar')
@section('heading', 'Buat akun baru')
@section('subheading', 'Daftar untuk mulai mengelola task di JARA.')
@section('footer')
    Sudah punya akun?
    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Masuk</a>
@endsection

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      {{ $errors->has('name') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      {{ $errors->has('email') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-gray-400 font-normal">(min. 8 karakter)</span></label>
        <input id="password" type="password" name="password" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      {{ $errors->has('password') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Ulangi password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
        Daftar
    </button>
</form>
@endsection
