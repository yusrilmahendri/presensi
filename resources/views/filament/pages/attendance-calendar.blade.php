<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header with navigation --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold">
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                </h2>
                <button wire:click="today" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                    Hari Ini
                </button>
            </div>
            
            <div class="flex gap-2">
                <button wire:click="previousMonth" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                    ← Bulan Lalu
                </button>
                <button wire:click="nextMonth" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                    Bulan Depan →
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-green-100"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Tepat Waktu</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-yellow-100"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Terlambat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-red-100"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Alpha</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-purple-100"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Weekend</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-gray-50 border"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Belum Terjadi</span>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div class="bg-white rounded-lg shadow dark:bg-gray-900">
            {{-- Days of week header --}}
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="p-3 text-sm font-semibold text-center text-gray-900 bg-gray-50 dark:bg-gray-800 dark:text-gray-100">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar days --}}
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                @php
                    $firstDay = \Carbon\Carbon::create($year, $month, 1);
                    $dayOfWeek = $firstDay->dayOfWeek;
                    $offset = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;
                @endphp

                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $offset; $i++)
                    <div class="p-4 bg-gray-50 dark:bg-gray-800"></div>
                @endfor

                {{-- Calendar days --}}
                @foreach($attendanceData as $day)
                    <div class="relative p-4 bg-white dark:bg-gray-900 {{ $day['color'] }} hover:opacity-80 transition-opacity cursor-help group"
                         title="{{ $day['tooltip'] }}">
                        <div class="text-sm font-medium {{ $day['is_today'] ? 'text-blue-600 font-bold' : '' }}">
                            {{ $day['date'] }}
                        </div>
                        @if($day['count'] > 0 && auth()->user()->role === 'admin')
                            <div class="text-xs mt-1">{{ $day['count'] }} hadir</div>
                        @endif
                        
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                            {{ $day['tooltip'] }}
                        </div>
                        
                        @if($day['is_today'])
                            <div class="absolute top-1 right-1 w-2 h-2 bg-blue-600 rounded-full"></div>
                        @endif
                    </div>
                @endforeach

                @php
                    $totalCells = $offset + count($attendanceData);
                    $remainingCells = 35 - $totalCells;
                    if ($totalCells > 35) { $remainingCells = 42 - $totalCells; }
                @endphp
                @for($i = 0; $i < $remainingCells; $i++)
                    <div class="p-4 bg-gray-50 dark:bg-gray-800"></div>
                @endfor
            </div>
        </div>

        {{-- Summary Stats for Karyawan --}}
        @if(auth()->user()->role === 'karyawan')
            @php
                $ontime = collect($attendanceData)->where('status', 'ontime')->count();
                $late = collect($attendanceData)->where('status', 'late')->count();
                $alpha = collect($attendanceData)->where('status', 'alpha')->count();
                $total = $ontime + $late;
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 rounded-lg dark:bg-green-900/20">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $ontime }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Tepat Waktu</div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg dark:bg-yellow-900/20">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $late }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Terlambat</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg dark:bg-red-900/20">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $alpha }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Alpha</div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg dark:bg-blue-900/20">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $total }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Hadir</div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
