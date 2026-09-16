<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jara - To Do List')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Transisi antar halaman: fade in saat masuk, fade out saat pindah */
        #page { opacity: 0; transform: translateY(8px); }
        #page.page-enter { opacity: 1; transform: none; transition: opacity .3s ease, transform .3s ease; }
        #page.page-leave { opacity: 0; transform: translateY(6px); transition: opacity .16s ease, transform .16s ease; }
        @media (prefers-reduced-motion: reduce) {
            #page, #page.page-enter, #page.page-leave { opacity: 1; transform: none; transition: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 text-white p-2 rounded-xl font-bold shadow-md shadow-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="font-bold text-lg tracking-tight text-slate-900">Jara</a>

                @auth
                <nav class="hidden md:flex items-center gap-1 ml-4 text-sm font-medium text-slate-500">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : '' }}">Dashboard</a>
                    <a href="{{ route('projects.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition {{ request()->routeIs('projects.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">Projects</a>
                    <a href="{{ route('task-lists.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition {{ request()->routeIs('task-lists.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">Daftar Tugas</a>
                    <a href="{{ route('tasks.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition {{ request()->routeIs('tasks.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">Tasks</a>
                    @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition {{ request()->routeIs('admin.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">Users</a>
                    @endif
                </nav>
                @endauth
            </div>

            <div class="flex items-center gap-4">
                @auth
                <div class="flex items-center gap-3 border-l pl-4 border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-slate-700 hidden sm:inline">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
                @else
                <div class="flex items-center gap-2 text-sm font-medium">
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-slate-600 hover:text-slate-900 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition">Daftar</a>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    @if(session('success'))
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error') || $errors->has('user'))
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-sm font-medium">
            {{ session('error') ?? $errors->first('user') }}
        </div>
    </div>
    @endif

    <!-- Main Content Area -->
    <main id="page" class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Dukungan header halaman lama (dashboard, projects, dsb.) --}}
        @hasSection('header')
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">@yield('header')</h1>
                @hasSection('subheader')
                <p class="text-slate-500 text-sm mt-1">@yield('subheader')</p>
                @endif
            </div>
            @hasSection('action')
            <div class="shrink-0">@yield('action')</div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Fade in halus setiap halaman dimuat (termasuk back/forward cache).
        (function () {
            var page = document.getElementById('page');
            if (!page) return;
            function enter() {
                page.classList.remove('page-leave');
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        page.classList.add('page-enter');
                    });
                });
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') enter();
            else document.addEventListener('DOMContentLoaded', enter);
            window.addEventListener('pageshow', function (e) { if (e.persisted) enter(); });
            // Fade out singkat saat klik link internal, supaya perpindahan terasa dinamis.
            document.addEventListener('click', function (e) {
                var a = e.target.closest('a[href]');
                if (!a || a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                var url = new URL(a.getAttribute('href'), window.location.origin);
                if (url.origin !== window.location.origin) return;
                e.preventDefault();
                page.classList.remove('page-enter');
                page.classList.add('page-leave');
                setTimeout(function () { window.location.href = url.toString(); }, 160);
            });
        })();
    </script>

</body>
</html>
