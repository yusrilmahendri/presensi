<div class="space-y-4">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-500 text-white rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Jam Kerja Minimum</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300"><?php echo e($minHours); ?> jam</div>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-lg p-4 border-2 border-amber-200 dark:border-amber-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-500 text-white rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Grace Period</div>
                    <div class="text-2xl font-bold text-amber-700 dark:text-amber-300"><?php echo e($gracePeriod); ?> jam</div>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border-2 border-green-200 dark:border-green-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-500 text-white rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Maksimal Sebelum Lembur</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300"><?php echo e($maxHours); ?> jam</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h4 class="font-semibold text-gray-800 dark:text-gray-200">Contoh Kasus (Check-in jam <?php echo e($checkInTime); ?>)</h4>
        </div>

        <div class="space-y-3">
            
            <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="p-1.5 bg-red-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-red-700 dark:text-red-300">Checkout jam <?php echo e($rejectTime); ?></span>
                        <span class="text-xs bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200 px-2 py-1 rounded-full font-medium">6 jam</span>
                    </div>
                    <p class="text-sm text-red-600 dark:text-red-400">❌ Ditolak! Kurang <?php echo e($minHours - 6); ?> jam dari minimum</p>
                </div>
            </div>

            
            <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="p-1.5 bg-green-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-green-700 dark:text-green-300">Checkout jam <?php echo e($allowTime1); ?></span>
                        <span class="text-xs bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded-full font-medium"><?php echo e($minHours); ?> jam</span>
                    </div>
                    <p class="text-sm text-green-600 dark:text-green-400">✅ Boleh checkout • Tepat memenuhi jam minimum</p>
                </div>
            </div>

            
            <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="p-1.5 bg-green-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-green-700 dark:text-green-300">Checkout jam <?php echo e($allowTime2); ?></span>
                        <span class="text-xs bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded-full font-medium"><?php echo e($maxHours); ?> jam</span>
                    </div>
                    <p class="text-sm text-green-600 dark:text-green-400">✅ Boleh checkout • Dalam grace period (belum lembur)</p>
                </div>
            </div>

            
            <div class="flex items-start gap-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="p-1.5 bg-purple-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-purple-700 dark:text-purple-300">Checkout jam <?php echo e($overtimeTime); ?></span>
                        <span class="text-xs bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full font-medium"><?php echo e($maxHours + 1); ?> jam</span>
                    </div>
                    <p class="text-sm text-purple-600 dark:text-purple-400">✅ Checkout berhasil • 🎖️ +1 jam lembur tercatat otomatis</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1 text-sm text-indigo-800 dark:text-indigo-300">
                <p class="font-semibold mb-1">💡 Cara Kerja Sistem:</p>
                <ul class="space-y-1 ml-4 list-disc">
                    <li>Karyawan <strong>harus bekerja minimal <?php echo e($minHours); ?> jam</strong> sebelum bisa checkout</li>
                    <li><strong>Grace period <?php echo e($gracePeriod); ?> jam</strong> memberikan toleransi sebelum dihitung lembur</li>
                    <li>Setelah <?php echo e($maxHours); ?> jam, <strong>sistem otomatis mencatat lembur</strong></li>
                    <li>Perhitungan lembur <strong>akurat sampai menit</strong> (contoh: 1.5 jam lembur)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/mac/Documents/code/web/presensi/resources/views/filament/components/working-hours-preview.blade.php ENDPATH**/ ?>