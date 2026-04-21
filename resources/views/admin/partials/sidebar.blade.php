{{-- Sidebar Partial --}}
{{-- Usage: @include('admin.partials.sidebar', ['activePage' => 'dashboard']) --}}

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo mb-4">
        <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 60px;">
    </div>
    
    <div class="sidebar-label text-uppercase small fw-bold mb-2 px-3" style="color: #94a3b8; letter-spacing: 0.05em; font-size: 0.7rem;">Main Menu</div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="/admin/dashboard" class="{{ ($activePage ?? '') === 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/admin/articel" class="{{ ($activePage ?? '') === 'articel' ? 'active' : '' }}">
                <i class="bi bi-journal-richtext"></i>
                <span>Artikel</span>
            </a>
        </li>
        <li>
            <a href="/admin/laporan" class="{{ ($activePage ?? '') === 'laporan' ? 'active' : '' }}">
                <i class="bi bi-chat-left-text-fill"></i>
                <span>Laporan</span>
            </a>
        </li>

        @if(Session::get('admin.role') === 'super_admin')
        <div class="sidebar-label text-uppercase small fw-bold mt-4 mb-2 px-3" style="color: #94a3b8; letter-spacing: 0.05em; font-size: 0.7rem;">System</div>
        <li>
            <a href="/admin/pengaturan" class="{{ ($activePage ?? '') === 'pengaturan' ? 'active' : '' }}">
                <i class="bi bi-sliders"></i>
                <span>Pengaturan Admin</span>
            </a>
        </li>
        @endif
        
        <div class="sidebar-label text-uppercase small fw-bold mt-4 mb-2 px-3" style="color: #94a3b8; letter-spacing: 0.05em; font-size: 0.7rem;">Account</div>
        <li>
            <a href="/admin/profile" class="{{ ($activePage ?? '') === 'profile' ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Profil Saya</span>
            </a>
        </li>
    </ul>

    <!-- Admin Footer Info -->
    <div class="mt-auto pt-4 px-3 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold shadow-sm" style="width: 38px; height: 38px; font-size: 0.85rem; background: #FFCB05 !important; border: 2px solid white;">
                {{ substr(Session::get('admin.nama', 'A'), 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-dark small fw-bold text-truncate">{{ Session::get('admin.nama', 'Administrator') }}</div>
                <div class="text-muted" style="font-size: 0.7rem; color: #616161 !important;">{{ ucfirst(Session::get('admin.role', 'Admin')) }}</div>
            </div>
        </div>
    </div>
</div>
