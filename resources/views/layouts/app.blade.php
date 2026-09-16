<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JARA') — JARA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-2 font-bold text-lg text-indigo-600">
                    <span class="text-2xl">✅</span> JARA
                </a>

                {{-- Nav links --}}
                <div class="flex items-center gap-5 text-sm font-medium text-gray-600">
                    @auth
                    <a href="{{ route('dashboard') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('projects.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Projects
                    </a>
                    <a href="{{ route('task-lists.index') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('task-lists.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Daftar Tugas
                    </a>
                    <a href="{{ route('tasks.index') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('tasks.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Tasks
                    </a>
                    @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('admin.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Users
                    </a>
                    @endif
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-500">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 transition">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('login') ? 'text-indigo-600 font-semibold' : '' }}">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition font-semibold">
                        Daftar
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <span>⚠️</span> {{ session('error') }}
        </div>
    </div>
    @endif
    @if($errors->has('user'))
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium">
            {{ $errors->first('user') }}
        </div>
    </div>
    @endif

    {{-- Main --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page header --}}
        @hasSection('header')
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">@yield('header')</h1>
                @hasSection('subheader')
                <p class="text-gray-500 text-sm mt-1">@yield('subheader')</p>
                @endif
            </div>
            @hasSection('action')
            <div class="shrink-0">@yield('action')</div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
