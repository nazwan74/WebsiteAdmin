<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | GESA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #FFCB05;
            --primary-dark: #E6B800;
            --secondary: #FFFAE6;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--primary);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        .bg-blobs {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            filter: blur(80px);
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.6;
            animation: move 20s infinite alternate;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.4);
            top: -150px;
            left: -150px;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: rgba(230, 184, 0, 0.3);
            bottom: -100px;
            right: -100px;
            animation-delay: -5s;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 40px;
            padding: 2.5rem 3rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-out;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-wrapper {
            background: rgba(255, 255, 255, 0.9);
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            padding: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .login-title {
            color: #333;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .login-subtitle {
            color: #616161;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.6rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #616161;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 10;
        }

        .form-control {
            background: #fff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            color: #333 !important;
            padding: 0.85rem 1rem 0.85rem 3.2rem !important;
            border-radius: 16px !important;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
            z-index: 5;
        }

        .form-control:focus {
            background: #fff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(255, 203, 5, 0.2) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%);
            border: none;
            color: #333;
            padding: 0.9rem;
            border-radius: 14px;
            font-weight: 700;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(255, 203, 5, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px rgba(255, 203, 5, 0.4);
            filter: brightness(1.05);
        }

        .back-link {
            color: #616161;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--primary-dark);
        }

        .strength-meter {
            height: 6px;
            background: rgba(0,0,0,0.05);
            border-radius: 3px;
            margin-top: 10px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
    </style>
</head>

<body>
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="login-card">
        <div class="logo-wrapper">
            <img src="{{ asset('images/Gesa_Logo.png') }}" alt="Logo" class="img-fluid">
        </div>

        <h3 class="login-title">Reset Password</h3>
        <p class="login-subtitle">Silakan masukkan kata sandi baru Anda</p>

        <form method="POST" action="{{ route('admin.reset-password') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="oobCode" value="{{ $oobCode }}">
            
            <div class="mb-3">
                <label class="form-label">Kata Sandi Baru</label>
                <div class="input-wrapper">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 6 karakter" required autofocus>
                    <div class="strength-meter">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <div class="input-wrapper">
                    <i class="bi bi-check2-circle input-icon"></i>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi kata sandi baru" required>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
            </button>

            <a href="{{ route('admin.login') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
            </a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const strengthBar = document.getElementById('strengthBar');

            password.addEventListener('input', function() {
                let strength = 0;
                const val = password.value;
                
                if (val.length >= 6) strength += 25;
                if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 25;
                if (val.match(/[0-9]/)) strength += 25;
                if (val.match(/[^a-zA-Z0-9]/)) strength += 25;

                strengthBar.style.width = strength + '%';
                
                if (strength <= 25) strengthBar.style.background = '#ef4444';
                else if (strength <= 50) strengthBar.style.background = '#f59e0b';
                else if (strength <= 75) strengthBar.style.background = '#3b82f6';
                else strengthBar.style.background = '#10b981';
            });

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Reset',
                    text: '{{ $errors->first() }}',
                    background: '#fff',
                    color: '#333',
                    confirmButtonColor: '#FFCB05',
                    customClass: { popup: 'rounded-4' }
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    background: '#fff',
                    color: '#333',
                    confirmButtonColor: '#FFCB05',
                    customClass: { popup: 'rounded-4' }
                });
            @endif
        });
    </script>
</body>
</html>
 