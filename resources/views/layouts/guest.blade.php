<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jara')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Transisi antar halaman: sama seperti layout utama */
        #page { opacity: 0; transform: translateY(8px); }
        #page.page-enter { opacity: 1; transform: none; transition: opacity .3s ease, transform .3s ease; }
        #page.page-leave { opacity: 0; transform: translateY(6px); transition: opacity .16s ease, transform .16s ease; }
        @media (prefers-reduced-motion: reduce) {
            #page, #page.page-enter, #page.page-leave { opacity: 1; transform: none; transition: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation (sama seperti dashboard) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 text-white p-2 rounded-xl font-bold shadow-md shadow-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-slate-900">Jara</span>
            </div>

            <div class="flex items-center gap-2 text-sm font-medium">
                <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('login') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900' }}">Masuk</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl transition {{ request()->routeIs('register') ? 'bg-indigo-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">Daftar</a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main id="page" class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            <!-- Panel brand ala kartu dashboard -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-8 shadow-sm text-white flex flex-col justify-between overflow-hidden relative">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-20 -left-10 w-56 h-56 bg-white/10 rounded-full"></div>
                <div class="relative">
                    <div class="bg-white/15 text-white p-2.5 rounded-xl inline-flex shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="mt-5 text-2xl font-bold tracking-tight">@yield('heading', 'Kelola tugasmu dengan Jara')</h1>
                    <p class="mt-2 text-indigo-100 text-sm leading-relaxed">@yield('subheading', 'Satu tempat untuk project, daftar tugas, dan pekerjaan harian tim.')</p>
                </div>
                <div class="relative mt-8 space-y-3">
                    <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center text-lg">📁</div>
                        <div>
                            <div class="font-semibold text-sm">Projects</div>
                            <div class="text-xs text-indigo-100">Kelola project & tim</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center text-lg">🗂️</div>
                        <div>
                            <div class="font-semibold text-sm">Daftar Tugas</div>
                            <div class="text-xs text-indigo-100">Wadah & keanggotaan</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center text-lg">✅</div>
                        <div>
                            <div class="font-semibold text-sm">Tasks</div>
                            <div class="text-xs text-indigo-100">Pantau progres harian</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu form -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col justify-center">
                @yield('content')

                <p class="text-center text-sm text-slate-500 mt-6">@yield('footer')</p>
            </div>

        </div>
    </main>

    <script>
        // Fade in/out antar halaman, sama seperti layout utama.
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
