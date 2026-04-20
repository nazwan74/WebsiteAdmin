@extends('admin.layouts.app', [
    'activePage' => 'profile',
    'navbarTitle' => 'Profile Admin',
    'navbarSubtitle' => 'Pengaturan Akun'
])

@section('title', 'Profile Admin')

@section('styles')
<style>
    /* Gaya Container Profile */
    .profile-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 20px;
    }
    
    /* Gaya Header Profile */
    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #e6edff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    .profile-avatar i {
        font-size: 40px;
        color: #4361ee;
    }
    
    /* Gaya Informasi Profile */
    .profile-info {
        margin-bottom: 30px;
    }
    
    .profile-info-item {
        margin-bottom: 15px;
    }
    
    .profile-info-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .profile-info-value {
        color: #333;
    }
    
    /* Gaya Form Password */
    .password-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <!-- Header Profile -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <h3>{{ $admin['name'] ?? 'Admin' }}</h3>
        <span class="badge bg-primary">{{ ucfirst($admin['role'] ?? 'admin') }}</span>
    </div>

    <!-- Informasi Profile -->
    <div class="profile-info">
        <div class="profile-info-item">
            <div class="profile-info-label">Email</div>
            <div class="profile-info-value">{{ $admin['email'] }}</div>
        </div>
        <div class="profile-info-item">
            <div class="profile-info-label">Role</div>
            <div class="profile-info-value">{{ ucfirst($admin['role'] ?? 'admin') }}</div>
        </div>
    </div>

    <!-- Form Ubah Password -->
    <div class="password-form">
        <h4 class="mb-4">Ubah Password</h4>
        
        <!-- Pesan Sukses -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Pesan Error -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Password -->
        <form action="{{ route('admin.profile.update-password') }}" method="POST">
            @csrf
            
            <!-- Field Password Saat Ini -->
            <div class="mb-3">
                <label for="current_password" class="form-label">Password Saat Ini</label>
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Field Password Baru -->
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Field Konfirmasi Password -->
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" required>
                @error('new_password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key me-2"></i>Ubah Password
            </button>
        </form>
    </div>
</div>
@endsection