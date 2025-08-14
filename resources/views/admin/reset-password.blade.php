<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Gaya Kustom -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #fff7d1, #ffe886);
            min-height: 100vh;
        }

        /* Gaya Card Reset Password */
        .reset-password-card {
            background: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        /* Gaya Form Control */
        .reset-password-card .form-control {
            border-radius: 10px;
            border-top-left-radius: 8px !important;
            border-bottom-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
            border-bottom-right-radius: 8px !important;
            margin-left: 0 !important;
            border: 1px solid #ddd;
            padding: 0.5rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .reset-password-card .form-control:focus {
            border-color: #FFCB05;
            box-shadow: 0 0 0 3px rgba(255, 203, 5, 0.25);
            outline: none;
        }

        /* Gaya Label Form */
        .reset-password-card .form-label {
            font-weight: 600;
        }

        /* Gaya Tombol */
        .reset-password-card button {
            border-radius: 10px;
            background-color: #FFCB05;
            color: #000;
            font-weight: 600;
            border: none;
            transition: background 0.3s ease;
        }

        .reset-password-card button:hover {
            background-color: #e6b404;
        }

        /* Gaya Icon Form */
        .form-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Gaya Input Group */
        .input-group {
            position: relative;
        }

        .input-group input {
            padding-left: 2.5rem;
        }

        /* Gaya Alert */
        .alert {
            border-radius: 10px;
            border: none;
        }

        /* Gaya Password Strength */
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .strength-weak {
            color: #dc3545;
        }

        .strength-medium {
            color: #ffc107;
        }

        .strength-strong {
            color: #198754;
        }

        /* Gaya Link */
        .back-to-login {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-to-login:hover {
            color: #FFCB05;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">
    <!-- Konten Utama -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <!-- Card Reset Password -->
                <div class="reset-password-card">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/Gesa_Logo.png') }}" alt="Gesa Logo" class="img-fluid" style="max-height: 80px;">
                    </div>

                    <!-- Judul -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Reset Password</h4>
                        <p class="text-muted">Masukkan password baru untuk akun Anda</p>
                    </div>

                    <!-- Pesan Error -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Form Reset Password -->
                    <form method="POST" action="{{ route('admin.reset-password') }}">
                        @csrf
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="oobCode" value="{{ $oobCode }}">
                        
                        <!-- Field Password Baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <i class="bi bi-lock-fill form-icon"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" id="password" required 
                                       placeholder="Masukkan password baru">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>

                        <!-- Field Konfirmasi Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <i class="bi bi-lock-fill form-icon"></i>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       name="password_confirmation" id="password_confirmation" required 
                                       placeholder="Konfirmasi password baru">
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="btn w-100 mb-3">
                            <i class="bi bi-check-circle me-2"></i>Reset Password
                        </button>

                        <!-- Tombol Kembali ke Login -->
                        <div class="text-center">
                            <a href="{{ route('admin.login') }}" class="back-to-login">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Kustom -->
    <script>
        // Fungsi cek kekuatan password
        function checkPasswordStrength(password) {
            const strengthElement = document.getElementById('passwordStrength');
            let strength = 0;
            let message = '';

            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    message = '<span class="strength-weak">Kekuatan password: Lemah</span>';
                    break;
                case 2:
                case 3:
                    message = '<span class="strength-medium">Kekuatan password: Sedang</span>';
                    break;
                case 4:
                case 5:
                    message = '<span class="strength-strong">Kekuatan password: Kuat</span>';
                    break;
            }

            strengthElement.innerHTML = message;
        }

        // Event listener untuk password strength
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Event listener untuk konfirmasi password
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 