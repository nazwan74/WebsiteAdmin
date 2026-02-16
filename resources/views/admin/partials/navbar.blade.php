<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid">
        <button class="hamburger-btn" id="hamburgerBtn" type="button">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-flex align-items-center">
            <div class="ms-3">
                <div class="navbar-dashboard-title">{{ $title ?? 'Dashboard' }}</div>
                <div class="navbar-dashboard-subtitle">{{ $subtitle ?? 'Admin' }}</div>
            </div>
        </div>
        
        <div class="ms-auto d-flex align-items-center">
            
            {{-- Notification Bell --}}
            <div class="notif-bell-wrapper me-3" id="notifBellWrapper">
                <button class="notif-bell-btn" id="notifBellBtn" title="Notifikasi">
                    <i class="bi bi-bell"></i>
                    <span class="notif-badge d-none" id="notifBellBadge"></span>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <i class="bi bi-bell-fill"></i> Notifikasi Chat
                    </div>
                    <div id="notifList">
                        <div class="notif-empty">
                            <i class="bi bi-chat-dots"></i>
                            Tidak ada pesan baru
                        </div>
                    </div>
                </div>
            </div>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                @csrf
                <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>
