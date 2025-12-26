{{-- Working Hours Preview Component --}}
<div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Preview Konfigurasi
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Simulasi berdasarkan pengaturan yang Anda masukkan</p>
    </div>

    {{-- Configuration Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Min Hours Card --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl p-4 border-2 border-blue-200 dark:border-blue-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-500 text-white rounded-xl shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Jam Minimum</div>
                    <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $minHours }}</div>
                    <div class="text-xs text-blue-600 dark:text-blue-400">jam/hari</div>
                </div>
            </div>
        </div>

        {{-- Grace Period Card --}}
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/30 rounded-xl p-4 border-2 border-amber-200 dark:border-amber-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-500 text-white rounded-xl shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Grace Period</div>
                    <div class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $gracePeriod }}</div>
                    <div class="text-xs text-amber-600 dark:text-amber-400">jam toleransi</div>
                </div>
            </div>
        </div>

        {{-- Max Before Overtime Card --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl p-4 border-2 border-green-200 dark:border-green-700 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-500 text-white rounded-xl shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Maks Sebelum Lembur</div>
                    <div class="text-3xl font-bold text-green-700 dark:text-green-300">{{ $maxHours }}</div>
                    <div class="text-xs text-green-600 dark:text-green-400">jam total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline Visualization --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Simulasi Skenario</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Contoh jika karyawan check-in jam {{ $checkInTime }}</p>
            </div>
        </div>

        <div class="space-y-3">
            {{-- Rejected Scenario --}}
            <div class="group hover:scale-[1.02] transition-transform">
                <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border-2 border-red-200 dark:border-red-800">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-2 bg-red-500 text-white rounded-full shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-red-700 dark:text-red-300">Checkout jam {{ $rejectTime }}</span>
                            <span class="text-xs bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200 px-3 py-1 rounded-full font-bold">6 jam</span>
                        </div>
                        <p class="text-sm text-red-600 dark:text-red-400 font-medium">❌ Ditolak! Kurang {{ $minHours - 6 }} jam dari minimum yang ditetapkan</p>
                    </div>
                </div>
            </div>

            {{-- Allowed Scenario 1 --}}
            <div class="group hover:scale-[1.02] transition-transform">
                <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-200 dark:border-green-800">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-2 bg-green-500 text-white rounded-full shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-green-700 dark:text-green-300">Checkout jam {{ $allowTime1 }}</span>
                            <span class="text-xs bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-3 py-1 rounded-full font-bold">{{ $minHours }} jam</span>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">✅ Berhasil! Tepat memenuhi jam kerja minimum</p>
                    </div>
                </div>
            </div>

            {{-- Allowed Scenario 2 --}}
            <div class="group hover:scale-[1.02] transition-transform">
                <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-200 dark:border-green-800">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-2 bg-green-500 text-white rounded-full shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-green-700 dark:text-green-300">Checkout jam {{ $allowTime2 }}</span>
                            <span class="text-xs bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-3 py-1 rounded-full font-bold">{{ $maxHours }} jam</span>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">✅ Berhasil! Masih dalam grace period (belum dihitung lembur)</p>
                    </div>
                </div>
            </div>

            {{-- Overtime Scenario --}}
            <div class="group hover:scale-[1.02] transition-transform">
                <div class="flex items-start gap-3 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border-2 border-purple-200 dark:border-purple-800">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-2 bg-purple-500 text-white rounded-full shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-purple-700 dark:text-purple-300">Checkout jam {{ $overtimeTime }}</span>
                            <span class="text-xs bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full font-bold">{{ $maxHours + 1 }} jam</span>
                        </div>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">✅ Berhasil! 🎖️ Bonus +1 jam lembur tercatat otomatis</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Information Box --}}
    <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-indigo-50 dark:from-indigo-900/20 dark:via-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 border-2 border-indigo-200 dark:border-indigo-700 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 p-2 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1 text-sm text-indigo-900 dark:text-indigo-200">
                <p class="font-bold mb-2 text-base">💡 Cara Kerja Sistem</p>
                <ul class="space-y-1.5 text-indigo-800 dark:text-indigo-300">
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">▸</span>
                        <span>Karyawan <strong>wajib bekerja minimal {{ $minHours }} jam</strong> sebelum bisa checkout</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">▸</span>
                        <span><strong>Grace period {{ $gracePeriod }} jam</strong> memberikan toleransi sebelum dihitung lembur</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">▸</span>
                        <span>Setelah {{ $maxHours }} jam, <strong>sistem otomatis mencatat lembur</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-500 mt-0.5">▸</span>
                        <span>Perhitungan lembur <strong>akurat hingga menit</strong> (contoh: 1.5 jam lembur)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

