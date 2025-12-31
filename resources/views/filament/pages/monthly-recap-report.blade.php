<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan
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
                    Laporan Rekap Kehadiran Bulan 
                    {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month ?? now()->month] }} 
                    {{ $year ?? now()->year }}
                </x-slot>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">NIK</th>
                                <th class="px-4 py-3 text-left">Shift</th>
                                <th class="px-4 py-3 text-center">Hari Kerja</th>
                                <th class="px-4 py-3 text-center">Hadir</th>
                                <th class="px-4 py-3 text-center">Tepat Waktu</th>
                                <th class="px-4 py-3 text-center">Terlambat</th>
                                <th class="px-4 py-3 text-center">Alpha</th>
                                <th class="px-4 py-3 text-center">% Hadir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($reportData as $index => $data)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $data['user']->name }}</td>
                                    <td class="px-4 py-3">{{ $data['user']->nik ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $data['user']->shift->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $data['total_hari_kerja'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            {{ $data['total_hadir'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                            {{ $data['total_tepat_waktu'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                            {{ $data['total_terlambat'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                            {{ $data['total_alpha'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        {{ $data['persentase_hadir'] }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-gray-800 font-semibold">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right">TOTAL:</td>
                                <td class="px-4 py-3 text-center">{{ collect($reportData)->sum('total_hadir') }}</td>
                                <td class="px-4 py-3 text-center">{{ collect($reportData)->sum('total_tepat_waktu') }}</td>
                                <td class="px-4 py-3 text-center">{{ collect($reportData)->sum('total_terlambat') }}</td>
                                <td class="px-4 py-3 text-center">{{ collect($reportData)->sum('total_alpha') }}</td>
                                <td class="px-4 py-3 text-center">
                                    {{ count($reportData) > 0 ? round(collect($reportData)->avg('persentase_hadir'), 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                @if(count($reportData) === 0)
                    <div class="text-center py-8 text-gray-500">
                        Tidak ada data untuk periode yang dipilih
                    </div>
                @endif
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
