<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-2 border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">💡 Frequently Asked Questions</h2>
            <p class="text-gray-700 dark:text-gray-300">Temukan jawaban atas pertanyaan yang sering diajukan tentang Sistem Presensi</p>
        </div>

        <!-- FAQ Categories -->
        @foreach($this->getFAQData() as $category)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ $category['category'] }}
                        </span>
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                            {{ count($category['items']) }} Pertanyaan
                        </span>
                    </div>
                </x-slot>

                <div class="space-y-4">
                    @foreach($category['items'] as $index => $faq)
                        <div class="border-l-4 border-blue-500 pl-4 py-2 bg-white dark:bg-gray-800/50 rounded-r">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-start gap-2">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <span>{{ $faq['question'] }}</span>
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 ml-8">
                                {{ $faq['answer'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach

        <!-- Help Section -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-gray-900 dark:text-white">
                    <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5" />
                    <span>Butuh Bantuan Lebih Lanjut?</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center">
                    <x-filament::icon icon="heroicon-o-book-open" class="w-8 h-8 mx-auto mb-2 text-blue-600 dark:text-blue-400" />
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Dokumentasi</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Baca panduan lengkap sistem</p>
                    <a href="{{ asset('docs/MANUAL_BOOK.md') }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                        → Lihat Manual Book
                    </a>
                </div>

                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <x-filament::icon icon="heroicon-o-envelope" class="w-8 h-8 mx-auto mb-2 text-green-600 dark:text-green-400" />
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Email Support</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Kirim email ke:</p>
                    <a href="mailto:yusrilmahendri.yusril@gmail.com" class="text-sm font-semibold text-green-600 dark:text-green-400 hover:underline break-all">
                        yusrilmahendri.yusril@gmail.com
                    </a>
                </div>

                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-center">
                    <x-filament::icon icon="heroicon-o-phone" class="w-8 h-8 mx-auto mb-2 text-purple-600 dark:text-purple-400" />
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Telepon/WhatsApp</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Hubungi kami:</p>
                    <a href="https://wa.me/6285161597598" target="_blank" class="text-sm font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                        085161597598
                    </a>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                <div class="text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Informasi Kontak Lengkap</h4>
                    <div class="flex flex-col md:flex-row justify-center items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-envelope" class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                            <a href="mailto:yusrilmahendri.yusril@gmail.com" class="text-blue-600 dark:text-blue-400 hover:underline">
                                yusrilmahendri.yusril@gmail.com
                            </a>
                        </div>
                        <div class="hidden md:block text-gray-400">|</div>
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-phone" class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                            <a href="tel:+6285161597598" class="text-blue-600 dark:text-blue-400 hover:underline">
                                085161597598
                            </a>
                        </div>
                        <div class="hidden md:block text-gray-400">|</div>
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                            <a href="https://wa.me/6285161597598" target="_blank" class="text-green-600 dark:text-green-400 hover:underline">
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ \App\Filament\Pages\Contact::getUrl() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <x-filament::icon icon="heroicon-o-arrow-right" class="w-4 h-4" />
                    Lihat Halaman Contact Lengkap
                </a>
            </div>
        </x-filament::section>

        <!-- Last Updated -->
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Last Updated: {{ now()->format('d F Y') }}</p>
            <p class="mt-1">Sistem Presensi v2.0</p>
        </div>
    </div>
</x-filament-panels::page>
