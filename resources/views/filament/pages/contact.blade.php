<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-2 border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">📞 Hubungi Kami</h2>
            <p class="text-gray-700 dark:text-gray-300">Kami siap membantu Anda! Hubungi kami melalui berbagai channel yang tersedia</p>
        </div>

        @php
            $contactInfo = $this->getContactInfo();
        @endphp

        <!-- Quick Info Alert -->
        <x-filament::section>
            <div class="bg-white dark:bg-gray-800 border-l-4 border-blue-500 p-4 rounded shadow-sm">
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-information-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-white mb-1">Sebelum Menghubungi</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $contactInfo['faq_note'] }}</p>
                        <a href="{{ \App\Filament\Pages\FAQ::getUrl() }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                            → Lihat FAQ
                        </a>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Support Channels -->
        <x-filament::section>
            <x-slot name="heading">
                Saluran Dukungan
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($contactInfo['support_channels'] as $channel)
                    <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:border-green-500 dark:hover:border-green-500 transition duration-200 hover:shadow-lg">
                        <div class="flex flex-col items-center text-center space-y-4">
                            <!-- Icon -->
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <x-filament::icon :icon="$channel['icon']" class="w-8 h-8 text-green-600 dark:text-green-400" />
                            </div>

                            <!-- Channel Name -->
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                {{ $channel['name'] }}
                            </h3>

                            <!-- Value -->
                            <div class="w-full">
                                <a href="{{ $channel['action'] }}" target="_blank" class="block text-lg font-semibold text-green-600 dark:text-green-400 hover:underline break-all">
                                    {{ $channel['value'] }}
                                </a>
                            </div>

                            <!-- Description -->
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $channel['description'] }}
                            </p>

                            <!-- Availability -->
                            <div class="w-full pt-2 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-center gap-1">
                                    <x-filament::icon icon="heroicon-o-clock" class="w-4 h-4" />
                                    {{ $channel['available'] }}
                                </p>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ $channel['action'] }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                                Hubungi via {{ $channel['name'] }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Emergency Contact -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                    <span>{{ $contactInfo['emergency_contact']['title'] }}</span>
                </div>
            </x-slot>

            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-left">
                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                            {{ $contactInfo['emergency_contact']['note'] }}
                        </p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $contactInfo['emergency_contact']['phone'] }}
                        </p>
                    </div>
                    <a href="tel:+62{{ substr($contactInfo['emergency_contact']['phone'], 1) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition whitespace-nowrap">
                        <x-filament::icon icon="heroicon-o-phone" class="w-5 h-5" />
                        Hubungi Sekarang
                    </a>
                </div>
            </div>
        </x-filament::section>

        <!-- Business Hours -->
        <x-filament::section>
            <x-slot name="heading">
                Jam Operasional
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100">Hari Kerja</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $contactInfo['business_hours']['weekdays'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Sabtu</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $contactInfo['business_hours']['saturday'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100">Minggu</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $contactInfo['business_hours']['sunday'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Additional Info -->
        <x-filament::section>
            <x-slot name="heading">
                Informasi Tambahan
            </x-slot>

            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-envelope" class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Email</h4>
                        <a href="mailto:{{ $contactInfo['email'] }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $contactInfo['email'] }}
                        </a>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-map-pin" class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Alamat</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $contactInfo['address'] }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-globe-alt" class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Zona Waktu</h4>
                        <p class="text-gray-700 dark:text-gray-300">WIB (GMT+7) - Waktu Indonesia Barat</p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Footer Note -->
        <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">
            <p class="mb-2">Terima kasih telah menggunakan Sistem Presensi</p>
            <p>Kami berkomitmen memberikan layanan terbaik untuk Anda</p>
        </div>
    </div>
</x-filament-panels::page>
