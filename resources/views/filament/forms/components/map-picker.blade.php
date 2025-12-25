<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        latitude: @entangle('data.latitude'),
        longitude: @entangle('data.longitude'),
        radius: @entangle('data.radius') || 100,
        map: null,
        marker: null,
        circle: null,
        
        init() {
            this.$nextTick(() => {
                this.initMap();
            });
        },
        
        initMap() {
            // Initialize Leaflet map
            this.map = L.map('map-' + '{{ $getId() }}', {
                attributionControl: false
            }).setView([this.latitude || -6.2088, this.longitude || 106.8456], 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(this.map);
            
            // Add marker
            this.marker = L.marker([this.latitude || -6.2088, this.longitude || 106.8456], {
                draggable: true
            }).addTo(this.map);
            
            // Add circle for radius
            this.circle = L.circle([this.latitude || -6.2088, this.longitude || 106.8456], {
                color: 'blue',
                fillColor: '#30f',
                fillOpacity: 0.2,
                radius: this.radius
            }).addTo(this.map);
            
            // Handle marker drag
            this.marker.on('dragend', (event) => {
                const position = event.target.getLatLng();
                this.updatePosition(position.lat, position.lng);
            });
            
            // Handle map click
            this.map.on('click', (event) => {
                const { lat, lng } = event.latlng;
                this.updatePosition(lat, lng);
            });
            
            // Watch for radius changes
            this.$watch('radius', value => {
                if (this.circle) {
                    this.circle.setRadius(value || 100);
                }
            });
            
            // Watch for coordinate changes from input fields
            this.$watch('latitude', value => {
                if (value && this.marker && this.circle) {
                    const newLatLng = [parseFloat(value), this.longitude];
                    this.marker.setLatLng(newLatLng);
                    this.circle.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                }
            });
            
            this.$watch('longitude', value => {
                if (value && this.marker && this.circle) {
                    const newLatLng = [this.latitude, parseFloat(value)];
                    this.marker.setLatLng(newLatLng);
                    this.circle.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                }
            });
        },
        
        updatePosition(lat, lng) {
            this.latitude = lat.toFixed(8);
            this.longitude = lng.toFixed(8);
            this.marker.setLatLng([lat, lng]);
            this.circle.setLatLng([lat, lng]);
        },
        
        getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation');
                return;
            }
            
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Mendapatkan Lokasi...';
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    this.updatePosition(lat, lng);
                    this.map.setView([lat, lng], 17);
                    
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                },
                (error) => {
                    let message = 'Tidak dapat mendapatkan lokasi Anda';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Izin lokasi ditolak. Silakan berikan izin akses lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            message = 'Waktu permintaan lokasi habis.';
                            break;
                    }
                    alert(message);
                    
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        },
        
        searchLocation() {
            const address = prompt('Masukkan alamat atau nama tempat:');
            if (!address) return;
            
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Mencari...';
            
            // Using Nominatim (OpenStreetMap geocoding) - prioritize Indonesia
            const url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address) + '&countrycodes=id&limit=5';
            
            fetch(url, {
                headers: {
                    'User-Agent': 'PresensiApp/1.0'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        if (data.length > 1) {
                            let options = 'Pilih lokasi:\n\n';
                            data.forEach((item, index) => {
                                options += (index + 1) + '. ' + item.display_name + '\n';
                            });
                            const choice = prompt(options + '\nMasukkan nomor pilihan (1-' + data.length + '):');
                            const index = parseInt(choice) - 1;
                            
                            if (index >= 0 && index < data.length) {
                                const lat = parseFloat(data[index].lat);
                                const lng = parseFloat(data[index].lon);
                                this.updatePosition(lat, lng);
                                this.map.setView([lat, lng], 17);
                            }
                        } else {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            this.updatePosition(lat, lng);
                            this.map.setView([lat, lng], 17);
                        }
                    } else {
                        alert('Lokasi tidak ditemukan. Coba dengan kata kunci yang lebih spesifik.');
                    }
                    
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch((error) => {
                    console.error('Search error:', error);
                    alert('Gagal mencari lokasi. Silakan coba lagi.');
                    
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }
    }">
        <div class="space-y-4">
            <!-- Quick Action Buttons - Prominent Display -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-xl border-2 border-blue-200 dark:border-blue-700 shadow-md">
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Main CTA Button -->
                    <button 
                        type="button"
                        @click="getCurrentLocation()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border-2 border-blue-800 rounded-lg font-bold text-sm text-white shadow-lg hover:from-blue-700 hover:to-blue-800 hover:shadow-xl active:scale-95 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        <svg class="w-6 h-6 mr-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-base">Gunakan Lokasi Saya Saat Ini</span>
                        <span class="ml-2 text-xs bg-white/20 px-2 py-1 rounded">GPS</span>
                    </button>
                    
                    <!-- Secondary Button -->
                    <button 
                        type="button"
                        @click="searchLocation()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 shadow hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-md active:scale-95 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:opacity-25 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Alamat
                    </button>
                </div>
                
                <!-- Helper Text -->
                <div class="mt-3 flex items-center justify-center gap-2 text-xs text-blue-700 dark:text-blue-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Klik tombol biru untuk menggunakan GPS atau cari manual dengan nama tempat</span>
                </div>
            </div>
            
            <!-- Coordinates Display -->
            <div class="flex items-center justify-between bg-white dark:bg-gray-900 p-3 rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Koordinat Terpilih:</span>
                    <span class="text-sm font-mono font-bold text-gray-700 dark:text-gray-200">
                        <span x-text="latitude || '-'"></span>, <span x-text="longitude || '-'"></span>
                    </span>
                </div>
                <button
                    type="button"
                    @click="navigator.clipboard.writeText(latitude + ', ' + longitude).then(() => alert('Koordinat disalin!')).catch(() => alert('Gagal menyalin'))"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-md transition-colors"
                    title="Salin koordinat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Salin
                </button>
            </div>
            
            <!-- Map Container -->
            <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 shadow-sm">
                <div id="map-{{ $getId() }}" style="height: 400px; width: 100%;"></div>
            </div>
            
            <!-- Info -->
            <div class="text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-blue-900 dark:text-blue-200 mb-2">🗺️ Cara Menggunakan Map Picker:</div>
                        <ul class="space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold">📍</span>
                                <span>Klik pada peta untuk memilih lokasi atau drag marker (pin merah) untuk menyesuaikan posisi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold">🎯</span>
                                <span>Tombol <strong>"Gunakan Lokasi Saya"</strong> untuk menggunakan GPS otomatis (akurat & cepat!)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold">🔍</span>
                                <span>Tombol <strong>"Cari Alamat"</strong> untuk mencari lokasi berdasarkan nama tempat atau alamat</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold">🔵</span>
                                <span>Lingkaran biru menunjukkan <strong>radius geofencing</strong> - karyawan hanya bisa absen dalam area ini</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold">📋</span>
                                <span>Klik ikon copy untuk menyalin koordinat latitude & longitude</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @once
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        @endpush
        
        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        @endpush
    @endonce
</x-dynamic-component>
