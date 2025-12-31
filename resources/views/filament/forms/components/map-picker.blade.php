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
            this.map = L.map('map-' + '{{ $getId() }}').setView([this.latitude || -6.2088, this.longitude || 106.8456], 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors',
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
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    this.updatePosition(lat, lng);
                    this.map.setView([lat, lng], 17);
                });
            }
        },
        
        searchLocation() {
            const address = prompt('Masukkan alamat atau nama tempat:');
            if (!address) return;
            
            // Using Nominatim (OpenStreetMap geocoding)
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        this.updatePosition(lat, lng);
                        this.map.setView([lat, lng], 17);
                    } else {
                        alert('Lokasi tidak ditemukan');
                    }
                })
                .catch(() => alert('Gagal mencari lokasi'));
        }
    }">
        <div class="space-y-3">
            <!-- Map Container -->
            <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 shadow-sm">
                <div id="map-{{ $getId() }}" style="height: 400px; width: 100%;"></div>
            </div>
            
            <!-- Controls -->
            <div class="flex gap-2 flex-wrap">
                <button 
                    type="button"
                    @click="getCurrentLocation()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Gunakan Lokasi Saya
                </button>
                
                <button 
                    type="button"
                    @click="searchLocation()"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari Alamat
                </button>
            </div>
            
            <!-- Info -->
            <div class="text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-blue-900 dark:text-blue-200">Cara Menggunakan:</div>
                        <ul class="mt-1 space-y-1 list-disc list-inside">
                            <li>Klik pada peta untuk memilih lokasi</li>
                            <li>Drag marker (pin) untuk menyesuaikan posisi</li>
                            <li>Gunakan tombol "Lokasi Saya" untuk GPS otomatis</li>
                            <li>Gunakan "Cari Alamat" untuk mencari tempat</li>
                            <li>Lingkaran biru menunjukkan radius geofencing</li>
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
