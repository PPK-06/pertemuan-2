<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JARA') — JARA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 font-bold text-2xl text-indigo-600">
                    <span class="text-3xl">✅</span> JARA
                </a>
                <h1 class="mt-4 text-xl font-bold text-gray-900">@yield('heading')</h1>
                <p class="text-gray-500 text-sm mt-1">@yield('subheading')</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                @yield('content')
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">@yield('footer')</p>
        </div>
    </div>
</body>
</html>
