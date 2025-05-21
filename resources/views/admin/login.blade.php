<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(to right, #fff7d1, #ffe886);
        min-height: 100vh;
    }

    .login-card {
        background: #fff;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .login-card .form-control {
        border-radius: 10px;
    }

    .login-card .form-label {
        font-weight: 600;
    }

    .login-card button {
        border-radius: 10px;
        background-color: #FFCB05;
        color: #000;
        font-weight: 600;
        border: none;
        transition: background 0.3s ease;
    }

    .login-card button:hover {
        background-color: #e6b404;
    }

    .form-icon {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .input-group {
        position: relative;
    }

    .input-group input {
        padding-left: 2.5rem;
    }
    .login-card .form-control {
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

    .login-card .form-control:focus {
        border-color: #FFCB05;
        box-shadow: 0 0 0 3px rgba(255, 203, 5, 0.25);
        outline: none;
    }


</style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('images/Gesa_Logo.png') }}" alt="Gesa Logo" class="img-fluid" style="max-height: 80px;">
            </div>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <i class="bi bi-envelope-fill form-icon"></i>
                    <input type="email" class="form-control" name="email" required>
                </div>
                </div>

                <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <i class="bi bi-lock-fill form-icon"></i>
                    <input type="password" class="form-control" name="password" required>
                </div>
                </div>

                <button type="submit" class="btn w-100 mt-3">Login</button>
            </form>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
