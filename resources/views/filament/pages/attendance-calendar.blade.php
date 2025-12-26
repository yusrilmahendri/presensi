<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header dengan Navigasi Bulan --}}
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" class="rounded-xl shadow-xl p-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-lg">
                        <svg class="w-8 h-8 md:w-10 md:h-10" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                            @php
                                $bulanIndo = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                $currentMonth = (int) $month;
                            @endphp
                            {{ $bulanIndo[$currentMonth] ?? 'Bulan' }} {{ $year }}
                        </h2>
                        <p class="text-sm md:text-base font-semibold" style="color: rgba(255,255,255,0.95);">📅 Pantau Kehadiran Karyawan</p>
                    </div>
                </div>
                
                <div class="flex gap-2 md:gap-3 w-full md:w-auto">
                    <button wire:click="previousMonth" 
                            style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.4);"
                            class="flex items-center justify-center gap-2 flex-1 md:flex-none px-4 md:px-5 py-3 text-sm font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 hover:bg-white/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden md:inline">Bulan Sebelumnya</span>
                    </button>
                    <button wire:click="today" 
                            style="background: white; color: #667eea; box-shadow: 0 4px 6px rgba(0,0,0,0.2);"
                            class="flex items-center justify-center gap-2 flex-1 md:flex-none px-4 md:px-6 py-3 text-sm font-black rounded-xl transition-all duration-200 hover:shadow-xl hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        <span>Bulan Ini</span>
                    </button>
                    <button wire:click="nextMonth" 
                            style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.4);"
                            class="flex items-center justify-center gap-2 flex-1 md:flex-none px-4 md:px-5 py-3 text-sm font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 hover:bg-white/30">
                        <span class="hidden md:inline">Bulan Berikutnya</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Keterangan Warna - Legend --}}
        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border-2 border-gray-100 dark:border-gray-700 p-5 md:p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <h3 class="text-base md:text-lg font-bold text-gray-800 dark:text-gray-200">Keterangan Warna</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
                <div class="flex items-center gap-2 p-2 md:p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-br from-green-100 to-green-200 border-2 border-green-400 dark:from-green-900 dark:to-green-800 dark:border-green-600 flex items-center justify-center shadow-sm">
                        <span class="text-xs">✅</span>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">Tepat Waktu</span>
                </div>
                <div class="flex items-center gap-2 p-2 md:p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-br from-yellow-100 to-yellow-200 border-2 border-yellow-400 dark:from-yellow-900 dark:to-yellow-800 dark:border-yellow-600 flex items-center justify-center shadow-sm">
                        <span class="text-xs">⚠️</span>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">Terlambat</span>
                </div>
                <div class="flex items-center gap-2 p-2 md:p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-br from-red-100 to-red-200 border-2 border-red-400 dark:from-red-900 dark:to-red-800 dark:border-red-600 flex items-center justify-center shadow-sm">
                        <span class="text-xs">❌</span>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">Alpha</span>
                </div>
                <div class="flex items-center gap-2 p-2 md:p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-br from-purple-100 to-purple-200 border-2 border-purple-400 dark:from-purple-900 dark:to-purple-800 dark:border-purple-600 flex items-center justify-center shadow-sm">
                        <span class="text-xs">🏖️</span>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">Weekend</span>
                </div>
                <div class="flex items-center gap-2 p-2 md:p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-300 dark:from-gray-700 dark:to-gray-600 dark:border-gray-500 flex items-center justify-center shadow-sm">
                        <span class="text-xs">⏳</span>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Terjadi</span>
                </div>
            </div>
        </div>

        {{-- Kalender Bulanan --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border-4 border-indigo-100">
            {{-- Header Hari dalam Seminggu --}}
            <div class="grid grid-cols-7 gap-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                    <div class="p-3 md:p-4 text-xs md:text-sm font-black text-center" style="color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- Tanggal Kalender --}}
            <div class="grid grid-cols-7 gap-0 bg-gray-100">
                @php
                    $firstDay = \Carbon\Carbon::create($year, $month, 1);
                    $dayOfWeek = $firstDay->dayOfWeek;
                    $offset = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;
                    
                    // Fungsi untuk mendapatkan style berdasarkan status
                    function getStatusStyle($status) {
                        switch($status) {
                            case 'ontime':
                                return 'background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 3px solid #28a745;';
                            case 'late':
                                return 'background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); border: 3px solid #ffc107;';
                            case 'alpha':
                                return 'background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 3px solid #dc3545;';
                            case 'weekend':
                                return 'background: linear-gradient(135deg, #e2d9f3 0%, #d4c5f9 100%); border: 3px solid #9b59b6;';
                            case 'future':
                                return 'background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 3px solid #dee2e6;';
                            default:
                                return 'background: white; border: 3px solid #e9ecef;';
                        }
                    }
                @endphp

                {{-- Cell kosong sebelum tanggal 1 --}}
                @for($i = 0; $i < $offset; $i++)
                    <div class="p-4 bg-gray-200" style="border: 1px solid #dee2e6;"></div>
                @endfor

                {{-- Tanggal dalam Kalender --}}
                @foreach($attendanceData as $day)
                    <div class="relative p-3 md:p-5 min-h-[70px] md:min-h-[100px] hover:scale-105 hover:shadow-2xl hover:z-20 transition-all duration-300 cursor-help group"
                         style="{{ getStatusStyle($day['status']) }}"
                         title="{{ $day['tooltip'] }}">
                        
                        {{-- Tanggal --}}
                        <div class="text-base md:text-xl font-black mb-1" 
                             style="{{ $day['is_today'] ? 'color: #667eea; font-size: 1.5rem;' : 'color: #2d3748;' }}">
                            {{ $day['date'] }}
                        </div>
                        
                        {{-- Status Icon --}}
                        <div class="text-xl md:text-2xl mb-1">
                            @if($day['status'] === 'ontime')
                                ✅
                            @elseif($day['status'] === 'late')
                                ⚠️
                            @elseif($day['status'] === 'alpha')
                                ❌
                            @elseif($day['status'] === 'weekend')
                                🏖️
                            @elseif($day['status'] === 'future')
                                ⏳
                            @endif
                        </div>
                        
                        {{-- Jumlah Hadir untuk Admin --}}
                        @if($day['count'] > 0 && auth()->user()->role === 'admin')
                            <div class="text-xs font-bold mt-1 px-2 py-1 rounded-full inline-block"
                                 style="background: rgba(255,255,255,0.8); color: #2d3748; border: 2px solid rgba(0,0,0,0.1);">
                                👥 {{ $day['count'] }} orang
                            </div>
                        @endif
                        
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-4 py-2 text-sm font-bold rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-30"
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-3 h-3" style="background: #764ba2;"></div>
                            {{ $day['tooltip'] }}
                        </div>
                        
                        {{-- Indikator Hari Ini --}}
                        @if($day['is_today'])
                            <div class="absolute top-2 right-2">
                                <div class="relative">
                                    <div class="w-4 h-4 rounded-full animate-pulse shadow-xl" style="background: #667eea;"></div>
                                    <div class="absolute top-0 right-0 w-4 h-4 rounded-full animate-ping" style="background: #764ba2;"></div>
                                </div>
                            </div>
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

        {{-- Ringkasan Statistik untuk Karyawan --}}
        @if(auth()->user()->role === 'karyawan')
            @php
                $ontime = collect($attendanceData)->where('status', 'ontime')->count();
                $late = collect($attendanceData)->where('status', 'late')->count();
                $alpha = collect($attendanceData)->where('status', 'alpha')->count();
                $total = $ontime + $late;
            @endphp
            
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg border-2 border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ringkasan Kehadiran Bulan Ini
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    <div class="p-4 md:p-5 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-green-200 dark:border-green-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl">✅</span>
                            <div class="text-3xl md:text-4xl font-black text-green-600 dark:text-green-400">{{ $ontime }}</div>
                        </div>
                        <div class="text-xs md:text-sm font-semibold text-green-700 dark:text-green-300">Tepat Waktu</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">Hari</div>
                    </div>
                    <div class="p-4 md:p-5 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-yellow-200 dark:border-yellow-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl">⚠️</span>
                            <div class="text-3xl md:text-4xl font-black text-yellow-600 dark:text-yellow-400">{{ $late }}</div>
                        </div>
                        <div class="text-xs md:text-sm font-semibold text-yellow-700 dark:text-yellow-300">Terlambat</div>
                        <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Hari</div>
                    </div>
                    <div class="p-4 md:p-5 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-red-200 dark:border-red-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl">❌</span>
                            <div class="text-3xl md:text-4xl font-black text-red-600 dark:text-red-400">{{ $alpha }}</div>
                        </div>
                        <div class="text-xs md:text-sm font-semibold text-red-700 dark:text-red-300">Alpha</div>
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">Hari</div>
                    </div>
                    <div class="p-4 md:p-5 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl">📊</span>
                            <div class="text-3xl md:text-4xl font-black text-blue-600 dark:text-blue-400">{{ $total }}</div>
                        </div>
                        <div class="text-xs md:text-sm font-semibold text-blue-700 dark:text-blue-300">Total Hadir</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">Hari</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
