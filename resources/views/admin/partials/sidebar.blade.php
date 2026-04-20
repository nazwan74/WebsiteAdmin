{{-- Sidebar Partial --}}
{{-- Usage: @include('admin.partials.sidebar', ['activePage' => 'dashboard']) --}}

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 80px;">
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="/admin/dashboard" class="{{ ($activePage ?? '') === 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/articel" class="{{ ($activePage ?? '') === 'articel' ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                Artikel
            </a>
        </li>
        <li>
            <a href="/admin/laporan" class="{{ ($activePage ?? '') === 'laporan' ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                Laporan
            </a>
        </li>

        @if(Session::get('admin.role') === 'super_admin')
        <li>
            <a href="/admin/pengaturan" class="{{ ($activePage ?? '') === 'pengaturan' ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                Pengaturan
            </a>
        </li>
        @endif
        <li>
            <a href="/admin/profile" class="{{ ($activePage ?? '') === 'profile' ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                Profile
            </a>
        </li>
    </ul>
</div>
