<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <button class="hamburger-btn" id="hamburgerBtn" type="button">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-flex align-items-center">
            <div class="ms-2">
                <div class="navbar-dashboard-title">{{ $title ?? 'Dashboard' }}</div>
                <div class="navbar-dashboard-subtitle">{{ $subtitle ?? 'Selamat datang kembali' }}</div>
            </div>
        </div>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            
            {{-- Notification Bell --}}
            <div class="notif-bell-wrapper" id="notifBellWrapper">
                <button class="notif-bell-btn position-relative d-flex align-items-center justify-content-center" id="notifBellBtn" title="Notifikasi" style="width: 42px; height: 42px; background: #f1f5f9; border-radius: 10px; border: none; color: #64748b;">
                    <i class="bi bi-bell-fill" style="font-size: 1.1rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBellBadge" style="font-size: 0.6rem;">
                        0
                    </span>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <i class="bi bi-bell-fill me-2 text-primary"></i> Notifikasi Chat
                    </div>
                    <div id="notifList">
                        <div class="notif-empty p-4 text-center">
                            <i class="bi bi-chat-dots fs-1 text-muted opacity-25 d-block mb-2"></i>
                            <span class="text-muted small">Tidak ada pesan baru</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vr mx-2" style="height: 30px; color: #e2e8f0;"></div>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                @csrf
                <button type="button" class="btn btn-light text-danger fw-bold shadow-sm d-flex align-items-center gap-2 px-3" style="border-radius: 10px; border: 1px solid rgba(220, 53, 69, 0.1); background: #fff5f5;" onclick="confirmLogout()">
                    <i class="bi bi-power" style="font-size: 1.1rem;"></i>
                    <span class="d-none d-md-inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>
