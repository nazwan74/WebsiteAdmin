<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | GESA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
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
            width: 400px;
            height: 400px;
            background: #6366f1;
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            width: 350px;
            height: 350px;
            background: #ec4899;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-wrapper {
            background: rgba(255, 255, 255, 0.9);
            width: 90px;
            height: 90px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            padding: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .login-title {
            color: #fff;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #94a3b8;
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 0.9rem;
        }

        .form-label {
            color: #e2e8f0;
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
            color: #94a3b8;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 10;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            padding: 0.8rem 1rem 0.8rem 3.2rem !important;
            border-radius: 14px !important;
            font-size: 1rem;
            transition: all 0.3s;
            position: relative;
            z-index: 5;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            color: white;
            padding: 0.9rem;
            border-radius: 14px;
            font-weight: 700;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(79, 70, 229, 0.4);
            filter: brightness(1.1);
        }

        .back-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
            display: block;
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link:hover {
            color: #fff;
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

        <h3 class="login-title">Lupa Password?</h3>
        <p class="login-subtitle">Masukkan email Anda untuk menerima link pemulihan</p>

        <form method="POST" action="{{ route('admin.forgot-password.send') }}">
            @csrf
            
            <div class="mb-4">
                <label class="form-label">Alamat Email</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" class="form-control" name="email" placeholder="Masukkan email admin" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Kirim Link Reset <i class="bi bi-send-fill ms-2"></i>
            </button>

            <a href="{{ route('admin.login') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
            </a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ $errors->first() }}',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#6366f1',
                    customClass: { popup: 'rounded-4' }
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonColor: '#6366f1',
                    customClass: { popup: 'rounded-4' }
                });
            @endif
        });
    </script>
</body>
</html>
 