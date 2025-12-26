<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold">
                    <?php
                        $bulanIndo = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    ?>
                    <?php echo e($bulanIndo[$month]); ?> <?php echo e($year); ?>

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

        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">📋 Keterangan Warna:</h3>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-green-100 border-2 border-green-400 dark:bg-green-900 dark:border-green-600"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">✅ Tepat Waktu</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-yellow-100 border-2 border-yellow-400 dark:bg-yellow-900 dark:border-yellow-600"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">⚠️ Terlambat</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-red-100 border-2 border-red-400 dark:bg-red-900 dark:border-red-600"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">❌ Alpha</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-purple-100 border-2 border-purple-400 dark:bg-purple-900 dark:border-purple-600"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🏖️ Weekend</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-gray-50 border-2 border-gray-300 dark:bg-gray-700 dark:border-gray-500"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">⏳ Belum Terjadi</span>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow dark:bg-gray-900">
            
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 text-sm font-semibold text-center text-gray-900 bg-gray-50 dark:bg-gray-800 dark:text-gray-100">
                        <?php echo e($day); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                <?php
                    $firstDay = \Carbon\Carbon::create($year, $month, 1);
                    $dayOfWeek = $firstDay->dayOfWeek;
                    $offset = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;
                ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < $offset; $i++): ?>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendanceData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative p-4 bg-white dark:bg-gray-900 <?php echo e($day['color']); ?> hover:opacity-80 transition-opacity cursor-help group"
                         title="<?php echo e($day['tooltip']); ?>">
                        <div class="text-sm font-medium <?php echo e($day['is_today'] ? 'text-blue-600 font-bold' : ''); ?>">
                            <?php echo e($day['date']); ?>

                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['count'] > 0 && auth()->user()->role === 'admin'): ?>
                            <div class="text-xs mt-1"><?php echo e($day['count']); ?> hadir</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                            <?php echo e($day['tooltip']); ?>

                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['is_today']): ?>
                            <div class="absolute top-1 right-1 w-2 h-2 bg-blue-600 rounded-full"></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php
                    $totalCells = $offset + count($attendanceData);
                    $remainingCells = 35 - $totalCells;
                    if ($totalCells > 35) { $remainingCells = 42 - $totalCells; }
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < $remainingCells; $i++): ?>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->role === 'karyawan'): ?>
            <?php
                $ontime = collect($attendanceData)->where('status', 'ontime')->count();
                $late = collect($attendanceData)->where('status', 'late')->count();
                $alpha = collect($attendanceData)->where('status', 'alpha')->count();
                $total = $ontime + $late;
            ?>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 rounded-lg dark:bg-green-900/20">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo e($ontime); ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Tepat Waktu</div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg dark:bg-yellow-900/20">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400"><?php echo e($late); ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Terlambat</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg dark:bg-red-900/20">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400"><?php echo e($alpha); ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Alpha</div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg dark:bg-blue-900/20">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo e($total); ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Hadir</div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH /Users/mac/Documents/code/web/presensi/resources/views/filament/pages/attendance-calendar.blade.php ENDPATH**/ ?>