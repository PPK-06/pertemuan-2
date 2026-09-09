<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JARA Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-10 px-4 sm:px-6 lg:px-8">

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <header class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-gray-500 mt-2 font-medium">{{ $projectName }}</p>
        </div>
    </header>

    <!-- Progress Bar -->
    <section class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Project Progress</h2>
            <span class="text-lg font-extrabold text-blue-600">{{ $progressPercent }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-4 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%"></div>
        </div>
    </section>

    <!-- Metrics Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition hover:shadow-md">
            <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Tasks</span>
            <span class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalTask }}</span>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition hover:shadow-md">
            <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider">To Do</span>
            <span class="text-4xl font-extrabold text-yellow-500 mt-2">{{ $todoTask }}</span>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition hover:shadow-md">
            <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider">In Progress</span>
            <span class="text-4xl font-extrabold text-blue-500 mt-2">{{ $inProgressTask }}</span>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition hover:shadow-md">
            <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Done</span>
            <span class="text-4xl font-extrabold text-green-500 mt-2">{{ $doneTask }}</span>
        </div>
    </section>

    <!-- Lists -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Upcoming Tasks -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Deadline Terdekat
            </h2>
            @if($upcomingTasks->isEmpty())
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-medium">Tidak ada tugas dengan deadline terdekat.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($upcomingTasks as $task)
                        <div class="p-5 bg-white rounded-2xl border border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:shadow-lg transition duration-300">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">{{ $task->title ?? 'Untitled Task' }}</h3>
                                <p class="text-sm text-gray-500 mt-1 font-medium">
                                    Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                @if(strtolower($task->priority) == 'high')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full tracking-wide">High</span>
                                @elseif(strtolower($task->priority) == 'medium')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full tracking-wide">Medium</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full tracking-wide">Low</span>
                                @endif
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ str_replace('_', ' ', $task->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Overdue Tasks -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold text-red-600 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tugas Overdue
            </h2>
            @if($overdueTasks->isEmpty())
                <div class="text-center py-10 bg-green-50 rounded-2xl border border-dashed border-green-200">
                    <p class="text-green-600 font-medium">Bagus! Tidak ada tugas yang overdue.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($overdueTasks as $task)
                        <div class="p-5 bg-red-50 rounded-2xl border border-red-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:shadow-lg transition duration-300">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $task->title ?? 'Untitled Task' }}</h3>
                                <p class="text-sm text-red-600 mt-1 font-medium">
                                    Overdue: {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                @if(strtolower($task->priority) == 'high')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full tracking-wide">High</span>
                                @elseif(strtolower($task->priority) == 'medium')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full tracking-wide">Medium</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full tracking-wide">Low</span>
                                @endif
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ str_replace('_', ' ', $task->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </section>
</div>

</body>
</html>
