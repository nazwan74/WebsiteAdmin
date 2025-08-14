<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Admin</title>
    
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

        /* Gaya Card Lupa Password */
        .forgot-password-card {
            background: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        /* Gaya Form Control */
        .forgot-password-card .form-control {
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

        .forgot-password-card .form-control:focus {
            border-color: #FFCB05;
            box-shadow: 0 0 0 3px rgba(255, 203, 5, 0.25);
            outline: none;
        }

        /* Gaya Label Form */
        .forgot-password-card .form-label {
            font-weight: 600;
        }

        /* Gaya Tombol */
        .forgot-password-card button {
            border-radius: 10px;
            background-color: #FFCB05;
            color: #000;
            font-weight: 600;
            border: none;
            transition: background 0.3s ease;
        }

        .forgot-password-card button:hover {
            background-color: #e6b404;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
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
                <!-- Card Lupa Password -->
                <div class="forgot-password-card">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/Gesa_Logo.png') }}" alt="Gesa Logo" class="img-fluid" style="max-height: 80px;">
                    </div>

                    <!-- Judul -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Lupa Password?</h4>
                        <p class="text-muted">Masukkan email Anda untuk menerima link reset password</p>
                    </div>

                    <!-- Pesan Sukses -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Pesan Error -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Form Lupa Password -->
                    <form method="POST" action="{{ route('admin.forgot-password.send') }}">
                        @csrf
                        
                        <!-- Field Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <i class="bi bi-envelope-fill form-icon"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required 
                                       placeholder="Masukkan email admin">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="btn w-100 mb-3">
                            <i class="bi bi-send me-2"></i>Kirim Link Reset
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
</body>
</html> 