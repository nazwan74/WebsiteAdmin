@extends('admin.layouts.app', [
    'activePage' => 'profile',
    'navbarTitle' => 'Profil Saya',
    'navbarSubtitle' => 'Kelola Informasi Akun'
])

@section('title', 'Profile Admin')

@section('styles')
<style>
    .profile-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .profile-banner {
        height: 120px;
        background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%);
    }
    
    .avatar-wrapper {
        margin-top: -60px;
        position: relative;
        z-index: 2;
    }
    
    .profile-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 30px;
        background: white;
        padding: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .avatar-icon-box {
        width: 100%;
        height: 100%;
        background: #FFFAE6;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #FFCB05;
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 4px;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
    
    .password-section {
        background: #f8fafc;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
    }
    
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        border-color: #FFCB05;
        box-shadow: 0 0 0 4px rgba(255, 203, 5, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-banner"></div>
                
                <div class="px-4 pb-4">
                    <div class="avatar-wrapper text-center mb-4">
                        <div class="profile-avatar-large">
                            <div class="avatar-icon-box">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mt-3 mb-1">{{ Session::get('admin.nama', 'Administrator') }}</h4>
                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem; background: #FFFAE6; color: #B45309;">
                            {{ strtoupper(str_replace('_', ' ', $admin['role'] ?? 'admin')) }}
                        </span>
                    </div>

                    <div class="row g-4 mb-5 justify-content-center text-center">
                        <div class="col-md-5">
                            <div class="info-label">Alamat Email</div>
                            <div class="info-value">{{ $admin['email'] }}</div>
                        </div>
                        <div class="col-md-5 border-start">
                            <div class="info-label">Level Akses</div>
                            <div class="info-value">{{ ucfirst($admin['role'] ?? 'admin') }} System</div>
                        </div>
                    </div>

                    <div class="password-section">
                        <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock" style="color: #FFCB05;"></i> Keamanan Akun
                        </h6>
                        
                        <form action="{{ route('admin.profile.update-password') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Password Saat Ini</label>
                                    <input type="password" class="form-control" name="current_password" placeholder="Masukkan password lama Anda" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Password Baru</label>
                                    <input type="password" class="form-control" name="new_password" placeholder="Minimal 6 karakter" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="new_password_confirmation" placeholder="Ulangi password baru" required>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); color: #333;">
                                        <i class="bi bi-key-fill me-2"></i>Perbarui Kata Sandi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection