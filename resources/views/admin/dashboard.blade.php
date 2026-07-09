@extends('admin.layouts.app', [
    'activePage' => 'dashboard',
    'navbarTitle' => 'Dashboard Overview',
    'navbarSubtitle' => 'Welcome back, Admin'
])

@section('title', 'Admin Dashboard')

@section('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endsection

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --amber-gradient: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }

    h1, h2, h3, h4, h5, h6, .fw-bold {
        font-family: 'Outfit', sans-serif;
    }

    .stat-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: -1;
    }

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    }

    .table thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        border-top: none;
        padding: 12px 16px;
    }

    .status-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .chart-container {
        position: relative;
        transition: all 0.3s ease;
    }

    /* Category Badges */
    .badge-category {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1.5px solid transparent; /* Dipertebal */
    }
    .theme-eksploitasi { background: #f5f3ff; color: #7c3aed; border-color: rgba(124, 58, 237, 0.2) !important; }
    .theme-kekerasan-fisik { background: #fef2f2; color: #dc2626; border-color: rgba(220, 38, 38, 0.2) !important; }
    .theme-kekerasan-psikis { background: #fff7ed; color: #f97316; border-color: rgba(249, 115, 22, 0.2) !important; }
    .theme-kekerasan-seksual { background: #fff1f2; color: #be123c; border-color: rgba(190, 18, 60, 0.2) !important; }
    .theme-penelantaran { background: #f0f9ff; color: #0284c7; border-color: rgba(2, 132, 199, 0.2) !important; }
    .theme-perdagangan { background: #eef2ff; color: #4f46e5; border-color: rgba(79, 70, 229, 0.2) !important; }
    .theme-perundungan { background: #fffbeb; color: #ca8a04; border-color: rgba(202, 138, 4, 0.2) !important; }
    .theme-default { background: #f8fafc; color: #64748b; border-color: rgba(100, 116, 139, 0.2) !important; }
</style>
@endsection

@section('content')
@php
    $isSuperAdmin = (Session::get('admin.role') === 'super_admin');
    $settings = $dashboardSettings ?? [];
    $layoutOrder = $isSuperAdmin ? ($settings['layout_order'] ?? ['stats', 'chart_daerah', 'tren_usia', 'table_terbaru']) : ['stats', 'chart_daerah', 'tren_usia', 'table_terbaru'];
@endphp

<!-- Header Dashboard dengan Tombol Refresh & Kustomisasi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Overview Real-time</h4>
        <p class="text-muted small mb-0">Pantau statistik dan pengaduan terbaru hari ini</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.dashboard.refresh') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm border-0" style="background: var(--primary-gradient); border-radius: 12px; transition: all 0.3s ease; color: #333;">
            <i class="bi bi-arrow-clockwise fs-5"></i>
            <span class="fw-semibold">Perbarui Data</span>
        </a>
    </div>
</div>

<!-- FILTER GLOBAL (super admin & admin pengaduan) -->
@if(Session::get('admin.role') !== 'admin_artikel')
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card p-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                <i class="bi bi-funnel-fill text-warning"></i> Filter Data Analitik Global
            </h6>
            <form method="GET" action="{{ route('admin.dashboard') }}" id="globalFilterForm" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1">Periode Tanggal</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="start_date" class="form-control border bg-light" style="border-radius: 8px 0 0 8px; font-size: 0.8rem;" value="{{ $globalStartDate ?? '' }}" max="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" onchange="this.form.submit()">
                        <span class="input-group-text bg-light border-start-0 border-end-0 small text-muted">s/d</span>
                        <input type="date" name="end_date" class="form-control border bg-light" style="border-radius: 0 8px 8px 0; font-size: 0.8rem;" value="{{ $globalEndDate ?? '' }}" max="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1">Kota/Kabupaten</label>
                    <select name="global_daerah" class="form-select form-select-sm border bg-light" style="border-radius: 8px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">Semua Wilayah</option>
                        @foreach($listAllDaerah ?? [] as $d)
                            <option value="{{ $d }}" {{ ($globalDaerah ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Tipe Kasus</label>
                    <select name="global_tipe" class="form-select form-select-sm border bg-light" style="border-radius: 8px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        @foreach($listAllTipe ?? [] as $t)
                            <option value="{{ $t }}" {{ ($globalTipe ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Status Laporan</label>
                    <select name="global_status" class="form-select form-select-sm border bg-light" style="border-radius: 8px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($listAllStatus ?? [] as $s)
                            <option value="{{ $s }}" {{ ($globalStatus ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Rentang Usia</label>
                    <select name="global_usia" class="form-select form-select-sm border bg-light" style="border-radius: 8px; font-size: 0.8rem;" onchange="this.form.submit()">
                        <option value="">Semua Usia</option>
                        @foreach($listAllUsia ?? [] as $u)
                            <option value="{{ $u }}" {{ ($globalUsia ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light px-3 fw-bold border" style="border-radius: 8px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                        <i class="bi bi-x-circle me-1"></i> Reset Semua Filter
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(Session::get('admin.role') !== 'admin_artikel')
    <!-- Bagian Statistik Berdasarkan Filter (KPI Cards) -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="stat-card glass-card p-3 h-100 border-0 shadow-sm text-dark bg-white" style="border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box text-white mb-0" style="background: var(--amber-gradient) !important; width: 44px; height: 44px; border-radius: 10px;">
                        <i class="bi bi-file-earmark-text-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.75rem;">Total Kasus</h6>
                        <h3 class="fw-bold mb-0" id="totalKejadian" style="font-size: 1.4rem;">{{ $totalKejadian ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card glass-card p-3 h-100 border-0 shadow-sm text-dark bg-white" style="border-left: 4px solid #64748b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box text-white mb-0" style="background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important; width: 44px; height: 44px; border-radius: 10px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.75rem;">Belum Ditangani</h6>
                        <h3 class="fw-bold mb-0" id="countBaru" style="font-size: 1.4rem;">{{ $countBaru ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card glass-card p-3 h-100 border-0 shadow-sm text-dark bg-white" style="border-left: 4px solid #fbbf24 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box text-white mb-0" style="background: var(--warning-gradient) !important; width: 44px; height: 44px; border-radius: 10px;">
                        <i class="bi bi-arrow-repeat fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.75rem;">Diproses</h6>
                        <h3 class="fw-bold mb-0" id="countProses" style="font-size: 1.4rem;">{{ $countProses ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card glass-card p-3 h-100 border-0 shadow-sm text-dark bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box text-white mb-0" style="background: var(--success-gradient) !important; width: 44px; height: 44px; border-radius: 10px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.75rem;">Selesai</h6>
                        <h3 class="fw-bold mb-0" id="countSelesai" style="font-size: 1.4rem;">{{ $countSelesai ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card glass-card p-3 h-100 border-0 shadow-sm text-dark bg-white" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box text-white mb-0" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important; width: 44px; height: 44px; border-radius: 10px;">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.75rem;">Dibatalkan</h6>
                        <h3 class="fw-bold mb-0" id="countDibatalkan" style="font-size: 1.4rem;">{{ $countDibatalkan ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Tren Kasus per Bulan -->
    <div class="row g-4 mb-4">
        <!-- A. Tren Kasus per Bulan (Line Chart) -->
        <div class="col-lg-12">
            <div class="glass-card p-4 border-0 shadow-sm bg-white h-100" style="border-radius: 16px;">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Tren Kasus per Bulan</h5>
                    <p class="text-muted small mb-0">Fluktuasi laporan pengaduan masuk dari waktu ke waktu</p>
                </div>
                <div style="height: 320px;" class="chart-container">
                    <canvas id="trenKasusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Kasus Berdasarkan Wilayah & Distribusi Usia Korban -->
    <!-- Row 2: Distribusi Usia Korban per Daerah (Stacked Bar Chart) -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="glass-card p-4 border-0 shadow-sm bg-white h-100" style="border-radius: 16px;">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Distribusi Usia Korban per Wilayah</h5>
                    <p class="text-muted small mb-0">Demografi korban pengaduan berdasarkan kelompok usia di setiap Kota/Kabupaten</p>
                </div>
                <div style="height: 380px;" class="chart-container">
                    <canvas id="usiaHistogramChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Sebaran Tipe Kasus per Daerah (Stacked Horizontal Bar Chart) -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="glass-card p-4 border-0 shadow-sm bg-white h-100" style="border-radius: 16px;">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Sebaran Tipe Kasus per Daerah</h5>
                    <p class="text-muted small mb-0">Komposisi jenis pengaduan di setiap Kota/Kabupaten Kalimantan Barat</p>
                </div>
                <div style="height: 520px;" class="chart-container">
                    <canvas id="tipeKasusPerDaerahChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Perbandingan Status Laporan per Daerah (Stacked Bar Chart) -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="glass-card p-4 border-0 shadow-sm bg-white h-100" style="border-radius: 16px;">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Perbandingan Status Laporan per Daerah</h5>
                    <p class="text-muted small mb-0">Belum ditangani, diproses, selesai, dan dibatalkan di setiap Kota/Kabupaten Kalimantan Barat</p>
                </div>
                <div style="height: 400px;" class="chart-container">
                    <canvas id="statusLaporanPerDaerahChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pengaduan Bawah (DataTables dengan Ekspor Excel/PDF) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-12">
            <div class="glass-card p-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Daftar Laporan Terfilter</h5>
                    <p class="text-muted small mb-0">Detail pengaduan masuk sesuai filter global analitik</p>
                </div>
                <div class="table-responsive">
                    <table id="pengaduanFilteredTable" class="table table-hover align-middle mb-0 w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Lokasi</th>
                                <th>Tipe Kasus</th>
                                <th>Usia</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filteredReports ?? [] as $report)
                                @php
                                        $displayText = \App\Http\Controllers\DashboardController::normalizeTipeKasus($report['kategori'] ?? '');
                                        $kategori = mb_strtolower($displayText, 'UTF-8');
                                        $categoryIcon = match($kategori) {
                                        'eksploitasi anak' => 'bi-shield-exclamation',
                                        'kekerasan fisik' => 'bi-hand-index-thumb-fill',
                                        'kekerasan psikis' => 'bi-emoji-frown-fill',
                                        'kekerasan seksual' => 'bi-exclamation-octagon-fill',
                                        'penelantaran anak' => 'bi-person-x-fill',
                                        'perdagangan anak' => 'bi-truck',
                                        'perundungan' => 'bi-megaphone-fill',
                                        'yang lain' => 'bi-three-dots',
                                        default => 'bi-tag-fill',
                                    };
                                    $kategoriTheme = match($kategori) {
                                        'eksploitasi anak' => 'theme-eksploitasi',
                                        'kekerasan fisik' => 'theme-kekerasan-fisik',
                                        'kekerasan psikis' => 'theme-kekerasan-psikis',
                                        'kekerasan seksual' => 'theme-kekerasan-seksual',
                                        'penelantaran anak' => 'theme-penelantaran',
                                        'perdagangan anak' => 'theme-perdagangan',
                                        'perundungan' => 'theme-perundungan',
                                        'yang lain' => 'theme-default',
                                        default => 'theme-default',
                                    };
                                    $statusText = $report['status'] ?? 'belum ditangani';
                                    $statusBadgeClass = match($statusText) {
                                        'belum ditangani' => 'bg-secondary text-white',
                                        'diproses' => 'bg-warning text-dark',
                                        'selesai' => 'bg-success text-white',
                                        'dibatalkan' => 'bg-danger text-white',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <i class="bi bi-calendar3 me-1 text-muted"></i> {{ $report['created_at'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-medium">
                                            <i class="bi bi-geo-alt-fill me-1 text-muted"></i> {{ $report['daerah'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-category {{ $kategoriTheme }}" style="border-radius: 8px; font-size: 0.65rem;">
                                            <i class="bi {{ $categoryIcon }}"></i>
                                            {{ $displayText }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            {{ $report['child_age'] }}
                                            @if($report['ageCategory'] !== 'Tidak diketahui')
                                                <span class="text-muted d-block" style="font-size: 0.65rem;">({{ $report['ageCategory'] }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusBadgeClass }} rounded-pill" style="font-size: 0.7rem; padding: 4px 10px; font-weight: 600;">
                                            {{ ucfirst($statusText) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" onclick="openDetailLaporan('{{ $report['id'] }}')" class="btn btn-light btn-sm fw-semibold px-3 border shadow-sm" style="border-radius: 8px; font-size: 0.75rem;">
                                            Detail Laporan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@else
<!-- Tabel Artikel Terbaru khusus Admin Artikel -->
<div class="row mt-4 mb-5 g-4">
    <div class="col-lg-12">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">Artikel Terbaru</h5>
                    <p class="text-muted small mb-0">Daftar edukasi yang baru rilis</p>
                </div>
                <a href="{{ route('admin.articel.index') }}" class="btn btn-sm px-3 border-0 fw-bold" style="border-radius: 8px; background: var(--primary-gradient); color: #333;">Kelola Semua Artikel</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Tanggal Rilis</th>
                            <th>Update Terakhir</th>
                            <th class="pe-4 text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestArticles ?? [] as $item)
                            @php
                                $kategori = strtolower($item['articleType'] ?? '');
                                $categoryIcon = match($kategori) {
                                    'pernikahan dini' => 'bi-heart-break-fill',
                                    'kekerasan anak' => 'bi-exclamation-triangle-fill',
                                    'bullying' => 'bi-megaphone-fill',
                                    'stunting' => 'bi-hospital-fill',
                                    default => 'bi-tag-fill',
                                };
                                $kategoriTheme = match($kategori) {
                                    'pernikahan dini' => 'theme-pernikahan',
                                    'kekerasan anak' => 'theme-kekerasan',
                                    'bullying' => 'theme-bullying',
                                    'stunting' => 'theme-stunting',
                                    default => 'theme-default',
                                };
                                $displayText = match($kategori) {
                                    'pernikahan dini' => 'Pernikahan Anak',
                                    'kekerasan anak' => 'Kekerasan Anak',
                                    'bullying' => 'Bullying',
                                    'stunting' => 'Stunting',
                                    default => ucfirst($kategori),
                                };
                                
                                $released = $item['releasedDate'] ?? null;
                                $parsedRel = null;
                                if ($released) {
                                    try {
                                        if ($released instanceof \Google\Cloud\Core\Timestamp) {
                                            $parsedRel = \Carbon\Carbon::instance($released->get());
                                        } elseif (is_numeric($released)) {
                                            $parsedRel = \Carbon\Carbon::createFromTimestampMs((int)$released);
                                        } else {
                                            $parsedRel = \Carbon\Carbon::parse($released);
                                        }
                                    } catch (\Exception $e) {}
                                }

                                $updated = $item['updateDate'] ?? null;
                                $parsedUpd = null;
                                if ($updated) {
                                    try {
                                        if ($updated instanceof \Google\Cloud\Core\Timestamp) {
                                            $parsedUpd = \Carbon\Carbon::instance($updated->get());
                                        } elseif (is_numeric($updated)) {
                                            $parsedUpd = \Carbon\Carbon::createFromTimestampMs((int)$updated);
                                        } else {
                                            $parsedUpd = \Carbon\Carbon::parse($updated);
                                        }
                                    } catch (\Exception $e) {}
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $item['title'] }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 450px;">{{ strip_tags($item['description'] ?? '') }}</div>
                                </td>
                                <td>
                                    <span class="badge-category {{ $kategoriTheme }}" style="border-radius: 8px; font-size: 0.65rem;">
                                        <i class="bi {{ $categoryIcon }}"></i>
                                        {{ $displayText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-medium text-dark"><i class="bi bi-calendar3 me-1"></i> {{ $parsedRel ? $parsedRel->locale('id')->translatedFormat('d M Y') : '-' }}</div>
                                </td>
                                <td>
                                    @if($parsedUpd)
                                        <div class="text-primary small fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i> {{ $parsedUpd->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.articel.edit', $item['id']) }}" class="btn btn-light btn-sm fw-semibold px-3 border" style="border-radius: 6px;">
                                        Edit Artikel
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
                                    Belum ada artikel edukasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('modals')
<!-- Modal Detail Pengaduan -->
<div class="modal fade" id="detailPengaduanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengaduan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailPengaduanContent">
                <div class="text-center py-5">
                    <div class="spinner-border"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Load DataTables & Export Buttons Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    // Fungsi animasi penghitungan
    function animateCount(element, target, duration = 1500) {
        let start = 0;
        if (target <= 0) {
            element.textContent = "0";
            return;
        }
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = Math.round(target);
                clearInterval(timer);
            } else {
                element.textContent = Math.round(start);
            }
        }, 16);
    }

    // Fungsi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi animasi penghitungan statistik
        const stats = {
            'totalKejadian': {{ $totalKejadian ?? 0 }},
            'countBaru': {{ $countBaru ?? 0 }},
            'countProses': {{ $countProses ?? 0 }},
            'countSelesai': {{ $countSelesai ?? 0 }},
            'countDibatalkan': {{ $countDibatalkan ?? 0 }}
        };

        Object.entries(stats).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = "0";
                animateCount(element, value);
            }
        });
    });

    @if(Session::get('admin.role') !== 'admin_artikel')
    // 1. Chart: Tren Kasus per Bulan (Line Chart)
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTren = document.getElementById('trenKasusChart');
        if (!ctxTren) return;

        const labels = @json($trendLabels ?? []);
        const data = @json($trendData ?? []);

        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1e293b', padding: 12 }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b' } },
                    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#e2e8f0' }, ticks: { color: '#64748b', stepSize: 1 } }
                }
            }
        });
    });



    // 3. Chart: Sebaran Tipe Kasus per Daerah (Stacked Horizontal Bar Chart)
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTipeDaerah = document.getElementById('tipeKasusPerDaerahChart');
        if (!ctxTipeDaerah) return;

        const labels = @json($tipeDaerahLabels ?? []);
        const datasets = @json($tipeDaerahDatasets ?? []).map(function(ds) {
            return Object.assign({}, ds, { borderRadius: 4, barThickness: 14 });
        });

        new Chart(ctxTipeDaerah, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { color: '#64748b', usePointStyle: true, padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            footer: function(items) {
                                const total = items.reduce((sum, item) => sum + (item.parsed.x || 0), 0);
                                return 'Total: ' + total + ' kasus';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { color: '#64748b', stepSize: 1 }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    }
                }
            }
        });
    });

    // 4. Chart: Distribusi Usia Korban per Daerah (Stacked Bar Chart)
    document.addEventListener('DOMContentLoaded', function() {
        const ctxUsia = document.getElementById('usiaHistogramChart');
        if (!ctxUsia) return;

        const labels = @json($usiaDaerahLabels ?? []);
        const data0_5 = @json($usiaDataset0_5 ?? []);
        const data6_10 = @json($usiaDataset6_10 ?? []);
        const data11_15 = @json($usiaDataset11_15 ?? []);
        const data16_18 = @json($usiaDataset16_18 ?? []);

        new Chart(ctxUsia, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '0–5 Tahun',
                        data: data0_5,
                        backgroundColor: '#3b82f6',
                        hoverBackgroundColor: '#2563eb',
                    },
                    {
                        label: '6–10 Tahun',
                        data: data6_10,
                        backgroundColor: '#10b981',
                        hoverBackgroundColor: '#059669',
                    },
                    {
                        label: '11–15 Tahun',
                        data: data11_15,
                        backgroundColor: '#fbbf24',
                        hoverBackgroundColor: '#f59e0b',
                    },
                    {
                        label: '16–18 Tahun',
                        data: data16_18,
                        backgroundColor: '#ef4444',
                        hoverBackgroundColor: '#dc2626',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 10, font: { size: 10 }, color: '#475569' }
                    },
                    tooltip: { backgroundColor: '#1e293b', padding: 10 }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 8 }, maxRotation: 45, autoSkip: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });
    });

    // 5. Chart: Perbandingan Status Laporan per Daerah (Stacked Bar Chart)
    document.addEventListener('DOMContentLoaded', function() {
        const ctxStatus = document.getElementById('statusLaporanPerDaerahChart');
        if (!ctxStatus) return;

        const labels = @json($statusDaerahLabels ?? []);
        const datasets = @json($statusDaerahDatasets ?? []);

        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 10, font: { size: 10 }, color: '#475569' }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            footer: function(items) {
                                const total = items.reduce((sum, item) => sum + (item.parsed.y || 0), 0);
                                return 'Total: ' + total + ' laporan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 8 }, maxRotation: 45, autoSkip: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });
    });

    // 6. Inisialisasi jQuery DataTables dengan Tombol Ekspor
    $(document).ready(function() {
        $('#pengaduanFilteredTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json',
                searchPlaceholder: "Cari pengaduan...",
                search: "",
            },
            dom: "<'row mb-3 align-items-center'<'col-sm-12 col-md-6 d-flex gap-2'B><'col-sm-12 col-md-6 d-flex justify-content-md-end mt-2 mt-md-0'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-md-end'p>>",
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel-fill me-1"></i> Excel',
                    className: 'btn btn-success btn-sm border-0 shadow-sm text-white px-3',
                    title: 'Daftar Pengaduan Terfilter',
                    style: 'border-radius: 8px;',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm border-0 shadow-sm text-white px-3',
                    title: 'Daftar Pengaduan Terfilter',
                    style: 'border-radius: 8px;',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ],
            pageLength: 10,
            ordering: true,
            order: [[0, 'desc']], // Sort by Date desc by default
            responsive: true,
            initComplete: function() {
                // Percantik input search DataTables
                $('.dataTables_filter input').addClass('form-control form-control-sm border bg-light px-3 py-1.5').css({
                    'border-radius': '8px',
                    'width': '220px'
                });
                $('.dt-buttons .btn').removeClass('btn-secondary');
            }
        });
    });

    let detailModal = null;
    function openDetailLaporan(id) {
        const modalEl = document.getElementById('detailPengaduanModal');
        if (!detailModal) {
            detailModal = new bootstrap.Modal(modalEl);
        }
        
        document.getElementById('detailPengaduanContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
                <div class="mt-2">Memuat data...</div>
            </div>
        `;
        
        detailModal.show();

        fetch(`/admin/laporan/${id}?source=dashboard`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('detailPengaduanContent').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('detailPengaduanContent').innerHTML = `
                <div class="alert alert-danger">Gagal memuat data pengaduan.</div>
            `;
        });
    }
    @endif
</script>
@endsection