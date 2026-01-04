/**
 * Global Session Handler
 * Menangani session expired untuk AJAX requests
 */

// Setup AJAX global handler untuk menangani session expired
(function() {
    // Untuk jQuery AJAX (jika menggunakan jQuery)
    if (typeof $ !== 'undefined' && $.ajaxSetup) {
        $.ajaxSetup({
            statusCode: {
                419: function() {
                    alert('Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
                    window.location.href = '/login';
                }
            }
        });
    }

    // Untuk Fetch API
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (response.status === 419) {
                    alert('Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
                    window.location.href = '/login';
                    return Promise.reject(new Error('Session expired'));
                }
                return response;
            });
    };

    // Untuk Axios (jika menggunakan Axios)
    if (typeof axios !== 'undefined') {
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 419) {
                    alert('Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
                    window.location.href = '/login';
                }
                return Promise.reject(error);
            }
        );
    }

    // Untuk Livewire (jika menggunakan Livewire)
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('request', ({fail}) => {
            fail(({status, preventDefault}) => {
                if (status === 419) {
                    preventDefault();
                    alert('Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
                    window.location.href = '/login';
                }
            });
        });
    }

    // Monitor form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.classList.contains('session-checked')) {
            form.classList.add('session-checked');
            
            // Add listener untuk menangani error pada form submission
            form.addEventListener('error', function() {
                // Form error handler
            });
        }
    });
})();

// Auto logout warning (optional - warning sebelum session expired)
(function() {
    const SESSION_TIMEOUT = 120 * 60 * 1000; // 120 menit (sesuaikan dengan config session)
    const WARNING_TIME = 5 * 60 * 1000; // 5 menit sebelum timeout
    
    let lastActivity = Date.now();
    let warningShown = false;

    // Reset activity timer
    function resetActivity() {
        lastActivity = Date.now();
        warningShown = false;
    }

    // Check session
    function checkSession() {
        const elapsed = Date.now() - lastActivity;
        
        // Jika hampir expired (5 menit sebelum)
        if (elapsed > (SESSION_TIMEOUT - WARNING_TIME) && !warningShown) {
            warningShown = true;
            if (confirm('Sesi Anda akan berakhir dalam 5 menit karena tidak ada aktivitas. Klik OK untuk melanjutkan sesi.')) {
                resetActivity();
                // Ping server untuk keep session alive
                fetch('/api/ping', { method: 'POST' }).catch(() => {});
            }
        }
        
        // Jika sudah expired
        if (elapsed > SESSION_TIMEOUT) {
            alert('Anda tidak aktivitas sehingga keluar dari sistem. Silakan login kembali.');
            window.location.href = '/login';
        }
    }

    // Monitor user activity
    const events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
    events.forEach(event => {
        document.addEventListener(event, resetActivity, true);
    });

    // Check session setiap 1 menit
    setInterval(checkSession, 60 * 1000);
})();
