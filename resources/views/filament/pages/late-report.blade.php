<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan Keterlambatan
            </x-slot>
            
            <form wire:submit="generateReport">
                {{ $this->form }}
                
                <div class="mt-6 flex gap-3">
                    <x-filament::button type="submit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Laporan
                    </x-filament::button>
                    
                    @if($reportData)
                        <x-filament::button color="danger" wire:click="printReport">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print / Download PDF
                        </x-filament::button>
                    @endif
                </div>
            </form>
        </x-filament::section>
        
        <!-- Report Results -->
        @if($reportData)
            <x-filament::section>
                <x-slot name="heading">
                    Laporan Keterlambatan
                    <span class="text-sm font-normal text-gray-500">
                        ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
                    </span>
                </x-slot>
                
                <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="text-sm text-red-600 dark:text-red-400">Total Keterlambatan</div>
                        <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ count($reportData) }}</div>
                    </div>
                    
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
                        <div class="text-sm text-orange-600 dark:text-orange-400">Rata-rata Terlambat</div>
                        <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">
                            @php
                                $avgMinutes = count($reportData) > 0 ? round(array_sum(array_column($reportData, 'late_minutes')) / count($reportData)) : 0;
                                $hours = floor($avgMinutes / 60);
                                $mins = $avgMinutes % 60;
                            @endphp
                            @if($hours > 0)
                                {{ $hours }} jam {{ $mins }} menit
                            @else
                                {{ $mins }} menit
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="text-sm text-yellow-600 dark:text-yellow-400">Terlambat Terlama</div>
                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">
                            @php
                                $maxMinutes = count($reportData) > 0 ? max(array_column($reportData, 'late_minutes')) : 0;
                                $hours = floor($maxMinutes / 60);
                                $mins = $maxMinutes % 60;
                            @endphp
                            @if($hours > 0)
                                {{ $hours }} jam {{ $mins }} menit
                            @else
                                {{ $mins }} menit
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">NIK</th>
                                <th class="px-4 py-3 text-left">Shift</th>
                                <th class="px-4 py-3 text-center">Jam Shift</th>
                                <th class="px-4 py-3 text-center">Check-in</th>
                                <th class="px-4 py-3 text-center">Terlambat</th>
                                <th class="px-4 py-3 text-left">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($reportData as $index => $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $record['name'] }}</td>
                                    <td class="px-4 py-3">{{ $record['nik'] }}</td>
                                    <td class="px-4 py-3">{{ $record['shift'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($record['shift_start'])->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($record['check_in_time'])->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $hours = floor($record['late_minutes'] / 60);
                                            $mins = $record['late_minutes'] % 60;
                                            $timeText = $hours > 0 ? "$hours jam $mins menit" : "$mins menit";
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $record['late_minutes'] > 60 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 
                                               ($record['late_minutes'] > 30 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : 
                                               'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') }}">
                                            {{ $timeText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $record['location'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data keterlambatan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
