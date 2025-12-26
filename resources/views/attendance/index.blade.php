<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->organization->name ?? 'Sistem Presensi' }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Face-API.js for face detection -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .attendance-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto 20px;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
        }
        #video {
            width: 100%;
            display: block;
        }
        #canvas {
            display: none;
        }
        .btn-capture {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 5px solid white;
            background: #dc3545;
            cursor: pointer;
            margin: 20px auto;
            display: block;
            transition: transform 0.2s;
        }
        .btn-capture:hover {
            transform: scale(1.1);
        }
        .btn-capture:active {
            transform: scale(0.95);
        }
        .location-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .location-status {
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            font-size: 0.9em;
            font-weight: bold;
        }
        .location-status.ready {
            background: #d4edda;
            color: #155724;
            border: 2px solid #28a745;
        }
        .location-status.waiting {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }
        .location-status.error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #dc3545;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        #map {
            height: 250px;
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        @media (max-width: 576px) {
            .attendance-card {
                padding: 20px 15px;
            }
            .header-section {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 15px;
            }
            .header-section h2 {
                font-size: 1.5rem;
            }
            .header-actions {
                width: 100%;
            }
            .header-actions .btn {
                flex: 1;
                min-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="attendance-card">
            <div class="d-flex justify-content-between align-items-center mb-4 header-section">
                <h2 class="mb-0">{{ $user->organization->name ?? 'Sistem Presensi' }}</h2>
                <div class="header-actions">
                    <a href="{{ route('karyawan.dashboard') }}" class="btn btn-sm btn-outline-primary">Dashboard</a>
                    <a href="{{ route('karyawan.profile') }}" class="btn btn-sm btn-outline-secondary">Profil</a>
                    <form method="POST" action="{{ route('karyawan.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Keluar</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-muted mb-4">
                Masuk sebagai: <strong>{{ $user->name }}</strong> ({{ $user->email }})
            </p>
            
            <form id="attendanceForm">

                <!-- Camera Section -->
                <div class="mb-4">
                    <h5 class="mb-3">Foto Selfie</h5>
                    <div class="camera-container">
                        <video id="video" autoplay playsinline></video>
                        <canvas id="canvas"></canvas>
                    </div>
                    <button type="button" class="btn-capture" id="captureBtn" style="display: none;">
                        <svg width="30" height="30" fill="white" viewBox="0 0 16 16" style="margin: 15px;">
                            <circle cx="8" cy="8" r="3"/>
                        </svg>
                    </button>
                    <button type="button" class="btn btn-secondary w-100 mb-2" id="retakeBtn" style="display: none;">
                        Ambil Ulang
                    </button>
                    <div id="capturedImage" style="display: none; text-align: center;">
                        <img id="preview" style="max-width: 100%; border-radius: 10px;" alt="Captured">
                    </div>
                </div>

                <!-- Location Section -->
                <div class="mb-4">
                    <h5 class="mb-3">Lokasi</h5>
                    <div class="location-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Status Lokasi:</strong></span>
                            <span class="location-status waiting" id="locationStatus">Mendapatkan lokasi...</span>
                        </div>
                        <div id="locationDetails" style="font-size: 0.9em; color: #6c757d;">
                            <div>Latitude: <span id="latitude">-</span></div>
                            <div>Longitude: <span id="longitude">-</span></div>
                            <div>Akurasi GPS: <span id="accuracy" style="font-weight: bold;">-</span></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-info w-100 mt-2" id="refreshLocationBtn" style="display: none;">
                            🔄 Refresh Lokasi untuk Akurasi Lebih Baik
                        </button>
                        <div id="map"></div>
                    </div>
                </div>

                <!-- Type Selection -->
                <div class="mb-4">
                    <label class="form-label">Tipe Absen</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="type" id="check_in" value="check_in" checked>
                        <label class="btn btn-outline-success" for="check_in">Masuk</label>

                        <input type="radio" class="btn-check" name="type" id="check_out" value="check_out">
                        <label class="btn btn-outline-danger" for="check_out">Keluar</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn" disabled>
                    Kirim Presensi
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: rgba(255, 255, 255, 0.95); padding: 20px 0; margin-top: 40px; text-align: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); border-radius: 15px;">
        <div style="color: #666; font-size: 0.85em;">
            &copy; 2025 Created by <strong style="color: #667eea;">Yusril Mahendri</strong> 
            <a href="https://yusrilmahendri.site" target="_blank" style="color: #764ba2; text-decoration: none;">yusrilmahendri.site</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let stream = null;
        let capturedPhoto = null;
        let currentLocation = null;
        let map = null;
        let marker = null;
        let faceApiLoaded = false;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // ===== SECURITY FUNCTIONS =====
        
        // Load Face-API models
        async function loadFaceApiModels() {
            if (faceApiLoaded) return;
            
            try {
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                faceApiLoaded = true;
                console.log('Face-API models loaded successfully');
            } catch (error) {
                console.error('Failed to load Face-API models:', error);
            }
        }
        
        // Detect face in captured photo
        async function detectFaceInPhoto() {
            if (!faceApiLoaded) {
                await loadFaceApiModels();
            }
            
            if (!capturedPhoto) {
                return { detected: false, confidence: 0 };
            }
            
            try {
                // Create image element from base64
                const img = new Image();
                img.src = capturedPhoto;
                
                await new Promise((resolve) => {
                    img.onload = resolve;
                });
                
                // Detect faces
                const detections = await faceapi.detectAllFaces(
                    img,
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                );
                
                if (detections && detections.length > 0) {
                    const detection = detections[0];
                    const confidence = Math.round(detection.score * 100);
                    
                    console.log('Face detected with confidence:', confidence + '%');
                    
                    return {
                        detected: true,
                        confidence: confidence,
                        faceCount: detections.length
                    };
                } else {
                    console.warn('No face detected in photo');
                    return { detected: false, confidence: 0 };
                }
            } catch (error) {
                console.error('Face detection error:', error);
                // If face detection fails, still allow but log it
                return { detected: true, confidence: 50 }; // Fallback
            }
        }
        
        // Generate device fingerprint
        function getDeviceFingerprint() {
            const nav = navigator;
            const screen = window.screen;
            
            // Create a unique device ID based on various factors
            const deviceData = [
                nav.userAgent,
                nav.language,
                screen.width,
                screen.height,
                screen.colorDepth,
                new Date().getTimezoneOffset(),
                !!window.sessionStorage,
                !!window.localStorage
            ].join('|');
            
            // Simple hash function
            let hash = 0;
            for (let i = 0; i < deviceData.length; i++) {
                const char = deviceData.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash; // Convert to 32bit integer
            }
            
            const deviceId = 'dev_' + Math.abs(hash).toString(16);
            
            // Extract device info
            const userAgent = nav.userAgent;
            let deviceModel = 'Unknown';
            let deviceOs = 'Unknown';
            
            // Detect OS
            if (userAgent.indexOf('Android') > -1) {
                deviceOs = 'Android';
                const match = userAgent.match(/Android (\d+(\.\d+)?)/);
                if (match) deviceOs += ' ' + match[1];
            } else if (userAgent.indexOf('iPhone') > -1 || userAgent.indexOf('iPad') > -1) {
                deviceOs = 'iOS';
                const match = userAgent.match(/OS (\d+(_\d+)?)/);
                if (match) deviceOs += ' ' + match[1].replace('_', '.');
            } else if (userAgent.indexOf('Windows') > -1) {
                deviceOs = 'Windows';
            } else if (userAgent.indexOf('Mac') > -1) {
                deviceOs = 'macOS';
            } else if (userAgent.indexOf('Linux') > -1) {
                deviceOs = 'Linux';
            }
            
            // Detect device model (simplified)
            if (userAgent.indexOf('Android') > -1) {
                const match = userAgent.match(/\(([^)]+)\)/);
                if (match) {
                    const parts = match[1].split(';');
                    deviceModel = parts[parts.length - 1].trim();
                }
            } else if (userAgent.indexOf('iPhone') > -1) {
                deviceModel = 'iPhone';
            } else if (userAgent.indexOf('iPad') > -1) {
                deviceModel = 'iPad';
            }
            
            return {
                device_id: deviceId,
                device_model: deviceModel,
                device_os: deviceOs
            };
        }
        
        // ===== END SECURITY FUNCTIONS =====

        // Initialize camera
        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user', // Front camera
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                document.getElementById('video').srcObject = stream;
                document.getElementById('captureBtn').style.display = 'block';
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Error',
                    text: 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.',
                });
                console.error('Camera error:', error);
            }
        }

        // Capture photo
        document.getElementById('captureBtn').addEventListener('click', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            
            capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('preview').src = capturedPhoto;
            document.getElementById('capturedImage').style.display = 'block';
            document.getElementById('video').style.display = 'none';
            document.getElementById('captureBtn').style.display = 'none';
            document.getElementById('retakeBtn').style.display = 'block';
            
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            checkFormReady();
        });

        // Retake photo
        document.getElementById('retakeBtn').addEventListener('click', function() {
            capturedPhoto = null;
            document.getElementById('capturedImage').style.display = 'none';
            document.getElementById('retakeBtn').style.display = 'none';
            document.getElementById('video').style.display = 'block';
            initCamera();
        });

        // Get location
        const attendanceLocations = @json($locations);
        let watchId = null;
        let bestAccuracy = Infinity;
        let attemptCount = 0;
        const MAX_ATTEMPTS = 5;
        const GPS_CACHE_KEY = 'attendance_gps_cache';
        const GPS_CACHE_DURATION = 60000; // 60 seconds
        let lastKnownPosition = null;
        let positionHistory = [];
        
        // ===== FAKE GPS DETECTION FUNCTIONS =====
        function detectFakeGPS(position) {
            const reasons = [];
            let suspicionScore = 0;
            
            // 1. Check if accuracy is suspiciously perfect (0-2 meters)
            if (position.coords.accuracy < 2) {
                reasons.push('Akurasi GPS terlalu sempurna (' + position.coords.accuracy.toFixed(1) + 'm) - mencurigakan');
                suspicionScore += 3;
            }
            
            // 2. Check if altitude is missing (fake GPS often doesn't provide this)
            if (position.coords.altitude === null || position.coords.altitude === undefined) {
                reasons.push('Data altitude tidak tersedia - indikasi fake GPS');
                suspicionScore += 2;
            }
            
            // 3. Check if altitudeAccuracy is missing
            if (position.coords.altitudeAccuracy === null || position.coords.altitudeAccuracy === undefined) {
                reasons.push('Data altitudeAccuracy tidak tersedia');
                suspicionScore += 1;
            }
            
            // 4. Check for impossible speed
            if (lastKnownPosition) {
                const timeDiff = (position.timestamp - lastKnownPosition.timestamp) / 1000; // seconds
                const distance = calculateDistance(
                    lastKnownPosition.coords.latitude,
                    lastKnownPosition.coords.longitude,
                    position.coords.latitude,
                    position.coords.longitude
                );
                const speed = distance / timeDiff; // meters per second
                
                // If speed > 50 m/s (180 km/h) it's suspicious
                if (timeDiff > 1 && speed > 50) {
                    reasons.push('Perpindahan lokasi tidak wajar (' + Math.round(speed * 3.6) + ' km/jam) - teleportasi terdeteksi');
                    suspicionScore += 4;
                }
            }
            
            // 5. Check if heading is missing when there's movement
            if (position.coords.speed !== null && position.coords.speed > 0.5) {
                if (position.coords.heading === null || position.coords.heading === undefined) {
                    reasons.push('Data arah pergerakan (heading) tidak tersedia saat bergerak');
                    suspicionScore += 1;
                }
            }
            
            // 6. Check for unrealistic coordinate precision (too many decimal places)
            const latStr = position.coords.latitude.toString();
            const lonStr = position.coords.longitude.toString();
            const latDecimals = latStr.split('.')[1]?.length || 0;
            const lonDecimals = lonStr.split('.')[1]?.length || 0;
            
            if (latDecimals > 8 || lonDecimals > 8) {
                reasons.push('Presisi koordinat tidak realistis (terlalu banyak desimal)');
                suspicionScore += 2;
            }
            
            // 7. Check position history for patterns
            positionHistory.push({
                lat: position.coords.latitude,
                lon: position.coords.longitude,
                time: position.timestamp
            });
            
            // Keep only last 5 positions
            if (positionHistory.length > 5) {
                positionHistory.shift();
            }
            
            // Check if positions are exactly the same (fake GPS often returns identical coords)
            if (positionHistory.length >= 3) {
                const allSame = positionHistory.every((pos, i, arr) => 
                    i === 0 || (pos.lat === arr[0].lat && pos.lon === arr[0].lon)
                );
                
                if (allSame && position.coords.speed === 0) {
                    // This is normal - device is stationary
                } else if (allSame) {
                    reasons.push('Koordinat identik pada pembacaan berurutan - pola fake GPS');
                    suspicionScore += 2;
                }
            }
            
            // 8. Check timestamp validity
            const now = Date.now();
            const positionAge = now - position.timestamp;
            
            if (positionAge < -1000) { // Position from future
                reasons.push('Timestamp GPS dari masa depan - manipulasi waktu terdeteksi');
                suspicionScore += 5;
            }
            
            if (positionAge > 60000) { // Position older than 1 minute
                reasons.push('Data GPS terlalu lama (lebih dari 1 menit)');
                suspicionScore += 1;
            }
            
            // Update last known position
            lastKnownPosition = position;
            
            // Decision: suspicious if score >= 5
            return {
                isSuspicious: suspicionScore >= 5,
                suspicionScore: suspicionScore,
                reasons: reasons
            };
        }
        
        // Log fake GPS attempt to server
        function logFakeGpsAttempt(position, reasons) {
            fetch('/attendance/log-fake-gps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    altitude: position.coords.altitude,
                    altitudeAccuracy: position.coords.altitudeAccuracy,
                    heading: position.coords.heading,
                    speed: position.coords.speed,
                    timestamp: position.timestamp,
                    reasons: reasons,
                    user_agent: navigator.userAgent
                })
            }).catch(error => console.error('Failed to log fake GPS attempt:', error));
        }
        // ===== END FAKE GPS DETECTION FUNCTIONS =====
        
        // Save GPS to localStorage
        function saveGPSCache(location) {
            const cache = {
                latitude: location.latitude,
                longitude: location.longitude,
                accuracy: location.accuracy,
                timestamp: Date.now()
            };
            localStorage.setItem(GPS_CACHE_KEY, JSON.stringify(cache));
        }
        
        // Load GPS from localStorage
        function loadGPSCache() {
            try {
                const cached = localStorage.getItem(GPS_CACHE_KEY);
                if (!cached) return null;
                
                const cache = JSON.parse(cached);
                const age = Date.now() - cache.timestamp;
                
                // Only use cache if less than GPS_CACHE_DURATION old
                if (age > GPS_CACHE_DURATION) {
                    localStorage.removeItem(GPS_CACHE_KEY);
                    return null;
                }
                
                return {
                    latitude: cache.latitude,
                    longitude: cache.longitude,
                    accuracy: cache.accuracy
                };
            } catch (e) {
                return null;
            }
        }
        
        function getLocation() {
            if (!navigator.geolocation) {
                updateLocationStatus('error', 'Browser tidak mendukung geolokasi');
                return;
            }
            
            // Try to load cached GPS first
            const cachedGPS = loadGPSCache();
            if (cachedGPS) {
                currentLocation = cachedGPS;
                bestAccuracy = cachedGPS.accuracy;
                
                document.getElementById('latitude').textContent = currentLocation.latitude.toFixed(8);
                document.getElementById('longitude').textContent = currentLocation.longitude.toFixed(8);
                
                // Display cached accuracy
                const accuracyEl = document.getElementById('accuracy');
                accuracyEl.textContent = Math.round(cachedGPS.accuracy) + ' meter';
                
                if (cachedGPS.accuracy <= 10) {
                    accuracyEl.style.color = '#28a745';
                    accuracyEl.textContent += ' (Sangat Baik ✓) 📦';
                } else if (cachedGPS.accuracy <= 20) {
                    accuracyEl.style.color = '#ffc107';
                    accuracyEl.textContent += ' (Baik) 📦';
                } else {
                    accuracyEl.style.color = '#dc3545';
                    accuracyEl.textContent += ' (Kurang Akurat ⚠️) 📦';
                }
                
                checkLocationRadius();
                initMap(currentLocation.latitude, currentLocation.longitude, currentLocation.accuracy);
                checkFormReady();
                
                updateLocationStatus('waiting', '📦 Menggunakan lokasi tersimpan. Mencari update...');
            } else {
                updateLocationStatus('waiting', '🔍 Mencari sinyal GPS terbaik... (Tunggu 5-10 detik)');
            }

            // Options for high accuracy GPS
            const options = {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0
            };
            
            // Use watchPosition for continuous updates to get best accuracy
            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    attemptCount++;
                    const accuracy = position.coords.accuracy;
                    
                    // ===== FAKE GPS DETECTION =====
                    const fakeGpsDetected = detectFakeGPS(position);
                    
                    if (fakeGpsDetected.isSuspicious) {
                        if (watchId) {
                            navigator.geolocation.clearWatch(watchId);
                            watchId = null;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: '⚠️ Fake GPS Terdeteksi!',
                            html: '<div style="text-align: left;">' +
                                  '<p><strong>Sistem mendeteksi kemungkinan penggunaan Fake GPS!</strong></p>' +
                                  '<p style="color: #dc3545;">Alasan deteksi:</p>' +
                                  '<ul style="margin-left: 20px; font-size: 0.9em;">' +
                                  fakeGpsDetected.reasons.map(r => '<li>' + r + '</li>').join('') +
                                  '</ul>' +
                                  '<hr>' +
                                  '<p><strong>⚠️ PERINGATAN:</strong></p>' +
                                  '<p style="font-size: 0.9em;">Penggunaan Fake GPS untuk absensi adalah <strong style="color: #dc3545;">PELANGGARAN SERIUS</strong> dan dapat berakibat:</p>' +
                                  '<ul style="margin-left: 20px; font-size: 0.85em; color: #dc3545;">' +
                                  '<li>Surat peringatan</li>' +
                                  '<li>Pemotongan gaji</li>' +
                                  '<li>Pemutusan hubungan kerja (PHK)</li>' +
                                  '</ul>' +
                                  '<p style="font-size: 0.85em; margin-top: 10px;"><strong>Insiden ini telah dicatat dalam sistem audit.</strong></p>' +
                                  '</div>',
                            confirmButtonText: 'Saya Mengerti',
                            confirmButtonColor: '#dc3545',
                            allowOutsideClick: false,
                            width: '550px'
                        });
                        
                        updateLocationStatus('error', '❌ Fake GPS terdeteksi! Absensi ditolak.');
                        document.getElementById('refreshLocationBtn').style.display = 'block';
                        
                        // Log to server for audit
                        logFakeGpsAttempt(position, fakeGpsDetected.reasons);
                        return;
                    }
                    // ===== END FAKE GPS DETECTION =====
                    
                    // Only update if this reading is more accurate
                    if (accuracy < bestAccuracy || !currentLocation) {
                        bestAccuracy = accuracy;
                        
                        currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: accuracy
                        };
                        
                        // Save to cache
                        saveGPSCache(currentLocation);
                        
                        document.getElementById('latitude').textContent = currentLocation.latitude.toFixed(8);
                        document.getElementById('longitude').textContent = currentLocation.longitude.toFixed(8);
                        
                        // Display accuracy with color coding
                        const accuracyEl = document.getElementById('accuracy');
                        accuracyEl.textContent = Math.round(accuracy) + ' meter';
                        
                        if (accuracy <= 10) {
                            accuracyEl.style.color = '#28a745'; // Green - Excellent
                            accuracyEl.textContent += ' (Sangat Baik ✓)';
                        } else if (accuracy <= 20) {
                            accuracyEl.style.color = '#ffc107'; // Yellow - Good
                            accuracyEl.textContent += ' (Baik)';
                        } else {
                            accuracyEl.style.color = '#dc3545'; // Red - Poor
                            accuracyEl.textContent += ' (Kurang Akurat ⚠️)';
                        }
                        
                        // Check distance to nearest location
                        checkLocationRadius();
                        
                        // Initialize/update map
                        initMap(currentLocation.latitude, currentLocation.longitude, currentLocation.accuracy);
                        
                        checkFormReady();
                    }
                    
                    // Stop watching after getting good accuracy or max attempts
                    if (accuracy <= 10 || attemptCount >= MAX_ATTEMPTS) {
                        if (watchId) {
                            navigator.geolocation.clearWatch(watchId);
                            watchId = null;
                        }
                        document.getElementById('refreshLocationBtn').style.display = 'block';
                    }
                },
                function(error) {
                    if (watchId) {
                        navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                    }
                    
                    let message = 'Tidak dapat mendapatkan lokasi.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = '⛔ Izin lokasi ditolak. Silakan berikan izin akses lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = '📡 Sinyal GPS tidak tersedia. Pastikan GPS aktif dan di area terbuka.';
                            break;
                        case error.TIMEOUT:
                            message = '⏱️ Timeout. Coba refresh atau pindah ke area dengan sinyal GPS lebih baik.';
                            break;
                    }
                    updateLocationStatus('error', message);
                    document.getElementById('refreshLocationBtn').style.display = 'block';
                },
                options
            );
        }
        
        // Refresh location button
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('refreshLocationBtn').addEventListener('click', function() {
                attemptCount = 0;
                bestAccuracy = Infinity;
                // Clear cache to force fresh GPS reading
                localStorage.removeItem(GPS_CACHE_KEY);
                currentLocation = null;
                this.style.display = 'none';
                getLocation();
            });
        });
        
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Earth radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
        
        function checkLocationRadius() {
            if (!attendanceLocations || attendanceLocations.length === 0) {
                updateLocationStatus('error', 'Tidak ada lokasi absen');
                return;
            }
            
            let nearestLocation = null;
            let minDistance = Infinity;
            
            attendanceLocations.forEach(location => {
                const distance = calculateDistance(
                    currentLocation.latitude,
                    currentLocation.longitude,
                    parseFloat(location.latitude),
                    parseFloat(location.longitude)
                );
                
                if (distance < minDistance) {
                    minDistance = distance;
                    nearestLocation = location;
                }
            });
            
            const distanceRounded = Math.round(minDistance);
            
            if (minDistance <= nearestLocation.radius) {
                updateLocationStatus('ready', '✅ Dalam radius! Jarak: ' + distanceRounded + 'm dari ' + nearestLocation.name);
            } else {
                const selisih = Math.round(minDistance - nearestLocation.radius);
                updateLocationStatus('error', '⚠️ Di luar radius! Jarak: ' + distanceRounded + 'm, Kurang: ' + selisih + 'm lagi ke ' + nearestLocation.name);
                
                // Show warning alert with recommendations
                const accuracyWarning = currentLocation.accuracy > 20 ? 
                    '<p style="color: #dc3545;"><strong>⚠️ Perhatian:</strong> Akurasi GPS Anda ' + Math.round(currentLocation.accuracy) + ' meter (kurang akurat). Hasil mungkin tidak presisi.</p>' : '';
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan Lokasi!',
                    html: '<div style="text-align: left;">' +
                          '<p>📍 <strong>Lokasi:</strong> ' + nearestLocation.name + '</p>' +
                          '<p>🎯 <strong>Radius Maksimal:</strong> ' + nearestLocation.radius + ' meter</p>' +
                          '<p>📏 <strong>Jarak Anda:</strong> ' + distanceRounded + ' meter</p>' +
                          '<p>⚠️ <strong>Kekurangan:</strong> ' + selisih + ' meter lagi</p>' +
                          '<p>📡 <strong>Akurasi GPS:</strong> ±' + Math.round(currentLocation.accuracy) + ' meter</p>' +
                          accuracyWarning +
                          '<hr>' +
                          '<p><strong>💡 Tips Meningkatkan Akurasi:</strong></p>' +
                          '<ul style="margin-left: 20px; font-size: 0.9em;">' +
                          '<li>Pastikan GPS/Location di HP aktif</li>' +
                          '<li>Keluar ke area terbuka (hindari dalam gedung)</li>' +
                          '<li>Tunggu 10-30 detik agar GPS stabil</li>' +
                          '<li>Klik tombol "Refresh Lokasi" untuk coba lagi</li>' +
                          '<li>Restart GPS/HP jika tetap tidak akurat</li>' +
                          '</ul>' +
                          '</div>',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#667eea',
                    allowOutsideClick: false,
                    width: '500px'
                });
            }
        }

        function updateLocationStatus(status, message) {
            const statusEl = document.getElementById('locationStatus');
            statusEl.className = 'location-status ' + status;
            statusEl.textContent = message;
        }

        function initMap(lat, lng, accuracy) {
            // Show map container
            document.getElementById('map').style.display = 'block';
            
            // Initialize map if not already done
            if (!map) {
                map = L.map('map', {
                    attributionControl: false
                }).setView([lat, lng], 16);
                
                // Add OpenStreetMap tiles (Indonesia region)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
                
                // Add marker
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup('<strong>Lokasi Anda</strong><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6))
                    .openPopup();
                
                // Add circle to show GPS accuracy
                L.circle([lat, lng], {
                    color: '#667eea',
                    fillColor: '#764ba2',
                    fillOpacity: 0.2,
                    radius: accuracy || 50
                }).addTo(map);
                
                // Add circles for all attendance locations
                @foreach($locations as $location)
                L.circle([{{ $location->latitude }}, {{ $location->longitude }}], {
                    color: '#28a745',
                    fillColor: '#d4edda',
                    fillOpacity: 0.15,
                    radius: {{ $location->radius }}
                }).addTo(map).bindPopup('<strong>{{ $location->name }}</strong><br>Radius: {{ $location->radius }}m');
                @endforeach
            } else {
                // Update existing map
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng])
                    .bindPopup('<strong>Lokasi Anda</strong><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6))
                    .openPopup();
            }
        }

        function checkFormReady() {
            const isReady = capturedPhoto && currentLocation;
            document.getElementById('submitBtn').disabled = !isReady;
        }

        // Form submission
        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';

            try {
                // === SECURITY: Detect face in photo ===
                const faceDetection = await detectFaceInPhoto();
                
                // === SECURITY: Get device fingerprint ===
                const deviceInfo = getDeviceFingerprint();
                
                const formData = {
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    photo: capturedPhoto,
                    type: document.querySelector('input[name="type"]:checked').value,
                    device_id: deviceInfo.device_id,
                    device_model: deviceInfo.device_model,
                    device_os: deviceInfo.device_os,
                    face_detected: faceDetection.detected,
                    face_confidence: faceDetection.confidence
                };

                const response = await fetch('{{ route("attendance.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    // Check if overtime detected
                    if (data.overtime_detected && data.redirect_url) {
                        await Swal.fire({
                            icon: 'warning',
                            title: '⏰ Overtime Terdeteksi!',
                            html: `
                                <div style="text-align: center; padding: 10px;">
                                    <p style="font-size: 1.1em; margin-bottom: 20px;">${data.message}</p>
                                    <div style="background: #fff3cd; padding: 15px; border-radius: 10px; border-left: 4px solid #ffc107;">
                                        <p style="margin: 0; color: #856404;">
                                            <strong>Anda akan diarahkan ke halaman pengajuan lembur.</strong><br>
                                            Silakan isi alasan lembur Anda.
                                        </p>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: 'Lanjutkan',
                            confirmButtonColor: '#667eea'
                        });
                        window.location.href = data.redirect_url;
                        return;
                    }

                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `
                            <div style="text-align: center; padding: 10px;">
                                <h5 style="color: #28a745; margin-bottom: 15px;">${data.message}</h5>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
                                    <div style="margin-bottom: 10px;">
                                        <strong style="color: #667eea;">⏰ Waktu:</strong>
                                        <div style="font-size: 1.2em; color: #333; margin-top: 5px;">${data.data.attendance_time}</div>
                                    </div>
                                    <div>
                                        <strong style="color: #667eea;">📍 Lokasi:</strong>
                                        <div style="font-size: 1.1em; color: #333; margin-top: 5px;">${data.data.location}</div>
                                    </div>
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea'
                    });
                    
                    // Reset form (keep logged in, just reset attendance form)
                    capturedPhoto = null;
                    currentLocation = null;
                    document.getElementById('capturedImage').style.display = 'none';
                    document.getElementById('retakeBtn').style.display = 'none';
                    document.getElementById('video').style.display = 'block';
                    updateLocationStatus('waiting', 'Mendapatkan lokasi...');
                    getLocation();
                    initCamera();
                    
                    // Optionally redirect to dashboard after successful attendance
                    setTimeout(() => {
                        window.location.href = '{{ route("karyawan.dashboard") }}';
                    }, 2000);
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan presensi.'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Presensi';
                checkFormReady();
            }
        });

        // Function to show overtime modal
        async function showOvertimeModal(overtimeData) {
            const { value: reason } = await Swal.fire({
                title: '⏰ Pengajuan Lembur',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <div style="background: #fff3cd; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                            <h6 style="color: #856404; margin-bottom: 10px;">📊 Detail Jam Kerja:</h6>
                            ${overtimeData.hours_worked ? `
                                <div style="margin-bottom: 8px;">
                                    <strong>Total Jam Kerja:</strong> ${overtimeData.hours_worked} jam<br>
                                    <strong>Batas Maksimal:</strong> ${overtimeData.max_hours} jam
                                </div>
                            ` : ''}
                            ${overtimeData.shift_end ? `
                                <div style="margin-bottom: 8px;">
                                    <strong>Shift Berakhir:</strong> ${overtimeData.shift_end}<br>
                                    <strong>Waktu Check-Out:</strong> ${overtimeData.check_out_time}
                                </div>
                            ` : ''}
                            <div style="color: #d9534f; font-weight: bold; margin-top: 10px;">
                                ⚠️ Overtime: ${overtimeData.overtime_hours} jam (${overtimeData.overtime_minutes} menit)
                            </div>
                        </div>
                        <label for="overtime-reason" style="font-weight: bold; margin-bottom: 8px; display: block;">
                            Alasan Lembur: <span style="color: red;">*</span>
                        </label>
                        <textarea 
                            id="overtime-reason" 
                            class="swal2-input" 
                            placeholder="Tuliskan alasan lembur Anda (minimal 10 karakter)" 
                            rows="4" 
                            style="width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ddd; border-radius: 5px; resize: vertical; min-height: 100px;"
                        ></textarea>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Submit Lembur',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#dc3545',
                preConfirm: () => {
                    const reason = document.getElementById('overtime-reason').value;
                    if (!reason || reason.trim().length < 10) {
                        Swal.showValidationMessage('Alasan lembur minimal 10 karakter');
                        return false;
                    }
                    return reason;
                }
            });

            if (reason) {
                await submitOvertimeRequest(overtimeData.attendance_id, reason);
            } else {
                // User cancelled, just redirect to dashboard
                Swal.fire({
                    icon: 'info',
                    title: 'Lembur Dibatalkan',
                    text: 'Anda membatalkan pengajuan lembur. Check-out tidak dilanjutkan.',
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    window.location.href = '{{ route("karyawan.dashboard") }}';
                });
            }
        }

        // Function to submit overtime request
        async function submitOvertimeRequest(attendanceId, reason) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = {
                    attendance_id: attendanceId,
                    reason: reason,
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    photo: capturedPhoto
                };

                const response = await fetch('{{ route("attendance.submit-overtime") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `
                            <div style="text-align: center; padding: 10px;">
                                <h5 style="color: #28a745; margin-bottom: 15px;">${data.message}</h5>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
                                    <div style="margin-bottom: 10px;">
                                        <strong style="color: #667eea;">⏰ Waktu Check-Out:</strong>
                                        <div style="font-size: 1.2em; color: #333; margin-top: 5px;">${data.data.attendance_time}</div>
                                    </div>
                                    <div style="margin-bottom: 10px;">
                                        <strong style="color: #667eea;">📍 Lokasi:</strong>
                                        <div style="font-size: 1.1em; color: #333; margin-top: 5px;">${data.data.location}</div>
                                    </div>
                                    <div>
                                        <strong style="color: #667eea;">⏳ Durasi Lembur:</strong>
                                        <div style="font-size: 1.1em; color: #333; margin-top: 5px;">${data.data.overtime_duration} menit</div>
                                    </div>
                                    <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px;">
                                        <small style="color: #856404;">Status: <strong>Menunggu Persetujuan</strong></small>
                                    </div>
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea'
                    });

                    setTimeout(() => {
                        window.location.href = '{{ route("karyawan.dashboard") }}';
                    }, 2000);
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan lembur.'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data lembur.'
                });
            }
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            // Load Face-API models asynchronously
            loadFaceApiModels().catch(err => {
                console.error('Failed to load face detection:', err);
            });
            
            initCamera();
            getLocation();
        });
    </script>
</body>
</html>

