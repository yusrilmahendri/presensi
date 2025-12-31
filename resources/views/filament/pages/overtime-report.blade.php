<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan Overtime
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
                    Laporan Overtime
                    <span class="text-sm font-normal text-gray-500">
                        ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
                    </span>
                </x-slot>
                
                @php
                    $totalOvertimes = count($reportData);
                    $totalHours = array_sum(array_column($reportData, 'duration_hours'));
                    $approvedCount = count(array_filter($reportData, fn($r) => $r['status'] === 'approved'));
                    $pendingCount = count(array_filter($reportData, fn($r) => $r['status'] === 'pending'));
                    $rejectedCount = count(array_filter($reportData, fn($r) => $r['status'] === 'rejected'));
                @endphp
                
                <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-sm text-blue-600 dark:text-blue-400">Total Overtime</div>
                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $totalOvertimes }}</div>
                    </div>
                    
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="text-sm text-purple-600 dark:text-purple-400">Total Jam</div>
                        <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ round($totalHours, 1) }}</div>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-sm text-green-600 dark:text-green-400">Disetujui</div>
                        <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $approvedCount }}</div>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="text-sm text-yellow-600 dark:text-yellow-400">Menunggu</div>
                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $pendingCount }}</div>
                    </div>
                    
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="text-sm text-red-600 dark:text-red-400">Ditolak</div>
                        <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $rejectedCount }}</div>
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
                                <th class="px-4 py-3 text-center">Waktu Mulai</th>
                                <th class="px-4 py-3 text-center">Waktu Selesai</th>
                                <th class="px-4 py-3 text-center">Durasi (Jam)</th>
                                <th class="px-4 py-3 text-center">Multiplier</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-left">Disetujui Oleh</th>
                                <th class="px-4 py-3 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($reportData as $index => $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $record['name'] }}</td>
                                    <td class="px-4 py-3">{{ $record['nik'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $record['start_time'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $record['end_time'] }}</td>
                                    <td class="px-4 py-3 text-center font-medium">{{ $record['duration_hours'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $record['multiplier'] }}x</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $record['status'] === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                               ($record['status'] === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 
                                               'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') }}">
                                            {{ $record['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $record['approved_by'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $record['notes'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data overtime
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
