<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sistem Presensi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        }
        .location-status.waiting {
            background: #fff3cd;
            color: #856404;
        }
        .location-status.error {
            background: #f8d7da;
            color: #721c24;
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
                <h2 class="mb-0">Sistem Presensi</h2>
                <div class="header-actions">
                    <a href="<?php echo e(route('karyawan.dashboard')); ?>" class="btn btn-sm btn-outline-primary">Dashboard</a>
                    <a href="<?php echo e(route('karyawan.profile')); ?>" class="btn btn-sm btn-outline-secondary">Profile</a>
                    <form method="POST" action="<?php echo e(route('karyawan.logout')); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-muted mb-4">
                Logged in as: <strong><?php echo e($user->name); ?></strong> (<?php echo e($user->email); ?>)
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
                        </div>
                        <div id="map"></div>
                    </div>
                </div>

                <!-- Type Selection -->
                <div class="mb-4">
                    <label class="form-label">Tipe Absen</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="type" id="check_in" value="check_in" checked>
                        <label class="btn btn-outline-success" for="check_in">Check In</label>

                        <input type="radio" class="btn-check" name="type" id="check_out" value="check_out">
                        <label class="btn btn-outline-danger" for="check_out">Check Out</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn" disabled>
                    Submit Presensi
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

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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
        function getLocation() {
            if (!navigator.geolocation) {
                updateLocationStatus('error', 'Browser tidak mendukung geolokasi');
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    
                    document.getElementById('latitude').textContent = currentLocation.latitude.toFixed(8);
                    document.getElementById('longitude').textContent = currentLocation.longitude.toFixed(8);
                    updateLocationStatus('ready', 'Lokasi berhasil didapatkan');
                    
                    // Initialize map
                    initMap(currentLocation.latitude, currentLocation.longitude);
                    
                    checkFormReady();
                },
                function(error) {
                    let message = 'Tidak dapat mendapatkan lokasi.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Izin lokasi ditolak. Silakan berikan izin akses lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            message = 'Waktu permintaan lokasi habis.';
                            break;
                    }
                    updateLocationStatus('error', message);
                },
                options
            );
        }

        function updateLocationStatus(status, message) {
            const statusEl = document.getElementById('locationStatus');
            statusEl.className = 'location-status ' + status;
            statusEl.textContent = message;
        }

        function initMap(lat, lng) {
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
                
                // Add circle to show accuracy
                L.circle([lat, lng], {
                    color: '#667eea',
                    fillColor: '#764ba2',
                    fillOpacity: 0.2,
                    radius: 50
                }).addTo(map);
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
                const formData = {
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    photo: capturedPhoto,
                    type: document.querySelector('input[name="type"]:checked').value
                };

                const response = await fetch('<?php echo e(route("attendance.store")); ?>', {
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
                        window.location.href = '<?php echo e(route("karyawan.dashboard")); ?>';
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

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            initCamera();
            getLocation();
        });
    </script>
</body>
</html>

<?php /**PATH /Users/mac/Documents/code/web/presensi/resources/views/attendance/index.blade.php ENDPATH**/ ?>