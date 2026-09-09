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
                <a href="{{ route('tasks.index') }}" class="flex items-center gap-2 font-bold text-lg text-indigo-600">
                    <span class="text-2xl">✅</span> JARA
                </a>

                {{-- Nav links --}}
                <div class="flex items-center gap-6 text-sm font-medium text-gray-600">
                    <a href="{{ route('tasks.index') }}"
                       class="hover:text-indigo-600 transition {{ request()->routeIs('tasks.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Tasks
                    </a>
                    {{-- Link berikut akan diisi oleh branch lain --}}
                    @auth
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-500">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 transition">Logout</button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash message --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         class="fixed top-20 right-4 z-50 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Main --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page header --}}
        @hasSection('header')
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">@yield('header')</h1>
                @hasSection('subheader')
                <p class="text-gray-500 text-sm mt-1">@yield('subheader')</p>
                @endif
            </div>
            @hasSection('action')
            <div>@yield('action')</div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
