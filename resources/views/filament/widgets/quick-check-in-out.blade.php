<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Quick Check In/Out</h3>
                <div class="text-sm text-gray-500">
                    {{ now()->format('d M Y H:i') }}
                </div>
            </div>
            
            @php
                $checkIn = $this->getCheckInToday();
                $checkOut = $this->getCheckOutToday();
                $user = auth()->user();
            @endphp
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Check In Status -->
                <div class="p-4 rounded-lg {{ $checkIn ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 {{ $checkIn ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-semibold {{ $checkIn ? 'text-green-700' : 'text-gray-600' }}">Check In</span>
                    </div>
                    @if($checkIn)
                        <div class="text-sm text-green-600">
                            ✓ {{ $checkIn->attendance_time->format('H:i') }}
                        </div>
                        <div class="text-xs text-green-500 mt-1">
                            {{ $checkIn->attendanceLocation->name ?? 'N/A' }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500">
                            Belum check-in
                        </div>
                    @endif
                </div>
                
                <!-- Check Out Status -->
                <div class="p-4 rounded-lg {{ $checkOut ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50 border border-gray-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 {{ $checkOut ? 'text-orange-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-semibold {{ $checkOut ? 'text-orange-700' : 'text-gray-600' }}">Check Out</span>
                    </div>
                    @if($checkOut)
                        <div class="text-sm text-orange-600">
                            ✓ {{ $checkOut->attendance_time->format('H:i') }}
                        </div>
                        <div class="text-xs text-orange-500 mt-1">
                            {{ $checkOut->attendanceLocation->name ?? 'N/A' }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500">
                            Belum check-out
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Shift Info -->
            @if($user->shift)
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-blue-700">
                            <strong>Shift Hari Ini:</strong> {{ $user->shift->name }} 
                            ({{ $user->shift->start_time }} - {{ $user->shift->end_time }})
                        </span>
                    </div>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <button 
                    wire:click="checkIn" 
                    wire:loading.attr="disabled"
                    @if($checkIn) disabled @endif
                    class="px-4 py-3 rounded-lg font-semibold transition-colors
                        {{ $checkIn 
                            ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                            : 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800' }}"
                >
                    <div wire:loading.remove wire:target="checkIn">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Check In
                    </div>
                    <div wire:loading wire:target="checkIn">
                        Processing...
                    </div>
                </button>
                
                <button 
                    wire:click="checkOut" 
                    wire:loading.attr="disabled"
                    @if($checkOut || !$checkIn) disabled @endif
                    class="px-4 py-3 rounded-lg font-semibold transition-colors
                        {{ ($checkOut || !$checkIn)
                            ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                            : 'bg-orange-600 text-white hover:bg-orange-700 active:bg-orange-800' }}"
                >
                    <div wire:loading.remove wire:target="checkOut">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Check Out
                    </div>
                    <div wire:loading wire:target="checkOut">
                        Processing...
                    </div>
                </button>
            </div>
            
            <div class="text-xs text-gray-500 text-center">
                Tombol check-in/out akan otomatis disabled setelah digunakan
            </div>
        </div>
    </x-filament::section>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('check-in-success', (event) => {
                new FilamentNotification()
                    .title('Berhasil!')
                    .success()
                    .body(event.message)
                    .send();
            });
            
            @this.on('check-in-error', (event) => {
                new FilamentNotification()
                    .title('Error!')
                    .danger()
                    .body(event.message)
                    .send();
            });
            
            @this.on('check-out-success', (event) => {
                new FilamentNotification()
                    .title('Berhasil!')
                    .success()
                    .body(event.message)
                    .send();
            });
            
            @this.on('check-out-error', (event) => {
                new FilamentNotification()
                    .title('Error!')
                    .danger()
                    .body(event.message)
                    .send();
            });
        });
    </script>
</x-filament-widgets::widget>
