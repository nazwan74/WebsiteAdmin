@extends('admin.layouts.app', [
    'activePage' => 'dashboard',
    'navbarTitle' => 'Dashboard Overview',
    'navbarSubtitle' => 'Welcome back, Admin'
])

@section('title', 'Admin Dashboard')

@section('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
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
    .theme-pernikahan { background: #fff1f2; color: #e11d48; border-color: rgba(225, 29, 72, 0.2) !important; }
    .theme-kekerasan { background: #fef2f2; color: #dc2626; border-color: rgba(220, 38, 38, 0.2) !important; }
    .theme-bullying { background: #fffbeb; color: #d97706; border-color: rgba(217, 119, 6, 0.2) !important; }
    .theme-stunting { background: #f0fdf4; color: #16a34a; border-color: rgba(22, 163, 74, 0.2) !important; }
    .theme-default { background: #f8fafc; color: #64748b; border-color: rgba(100, 116, 139, 0.2) !important; }
</style>
@endsection

@section('content')
<!-- Header Dashboard dengan Tombol Refresh -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Overview Real-time</h4>
        <p class="text-muted small mb-0">Pantau statistik dan laporan terbaru hari ini</p>
    </div>
    <a href="{{ route('admin.dashboard.refresh') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm border-0" style="background: var(--primary-gradient); border-radius: 12px; transition: all 0.3s ease; color: #333;">
        <i class="bi bi-arrow-clockwise fs-5"></i>
        <span class="fw-semibold">Perbarui Data</span>
    </a>
</div>

<!-- Bagian Statistik -->
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card glass-card p-4 h-100">
            <div class="icon-box text-dark" style="background: var(--primary-gradient) !important;">
                <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
            </div>
            <h6 class="text-muted small fw-medium mb-1">Pengguna Aplikasi</h6>
            <h2 class="fw-bold mb-0" id="totalUsers">{{ $totalUsers }}</h2>
            <div class="mt-2">
                <span class="text-success small fw-semibold"><i class="bi bi-arrow-up"></i> Terverifikasi</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card glass-card p-4 h-100">
            <div class="icon-box bg-warning text-white" style="background: var(--warning-gradient) !important;">
                <i class="bi bi-file-earmark-text-fill" style="font-size: 1.5rem;"></i>
            </div>
            <h6 class="text-muted small fw-medium mb-1">Laporan Masuk</h6>
            <h2 class="fw-bold mb-0" id="totalLaporan">{{ $totalLaporan }}</h2>
            <div class="mt-2">
                <span class="text-warning small fw-semibold"><i class="bi bi-clock-history"></i> Butuh Respon</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card glass-card p-4 h-100">
            <div class="icon-box bg-success text-white" style="background: var(--success-gradient) !important;">
                <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
            </div>
            <h6 class="text-muted small fw-medium mb-1">Kasus Selesai</h6>
            <h2 class="fw-bold mb-0" id="totalSelesai">{{ $totalSelesai }}</h2>
            <div class="mt-2">
                <span class="text-success small fw-semibold"><i class="bi bi-shield-check"></i> Tertangani</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card glass-card p-4 h-100">
            <div class="icon-box text-dark" style="background: var(--amber-gradient) !important;">
                <i class="bi bi-book-fill" style="font-size: 1.5rem;"></i>
            </div>
            <h6 class="text-muted small fw-medium mb-1">Artikel Edukasi</h6>
            <h2 class="fw-bold mb-0" id="totalArticles">{{ $totalArticles }}</h2>
            <div class="mt-2">
                <span class="text-info small fw-semibold"><i class="bi bi-lightbulb"></i> Literasi Aktif</span>
            </div>
        </div>
    </div>
</div>

<!-- Bar Chart Laporan per Kota -->
<div class="row mt-4 g-4">
    <div class="col-lg-12">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-0">Laporan per Kota/Daerah</h5>
                    <p class="text-muted small mb-0">Sebaran data di 14 Kabupaten/Kota Kalimantan Barat</p>
                </div>
                <div class="icon-box bg-light text-primary mb-0">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            </div>
            <div style="height: 400px;" class="chart-container">
                <canvas id="daerahLaporanChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tren Laporan per Periode + Rentang Usia Anak -->
<div class="row mt-4 g-4">
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-0">Tren Laporan per Periode</h5>
                    <p class="text-muted small mb-0">Fluktuasi jumlah laporan masuk</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="filterTrenForm" class="d-flex flex-wrap align-items-center gap-2">
                        <select name="tahun" class="form-select form-select-sm border-0 bg-light" style="width: auto; border-radius: 8px;" onchange="document.getElementById('filterTrenForm').submit();">
                            <option value="">12 bulan terakhir</option>
                            @foreach($tahunList ?? [] as $y)
                                <option value="{{ $y }}" {{ (request('tahun') == $y || ($filterTahun ?? '') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <select name="bulan" class="form-select form-select-sm border-0 bg-light" style="width: auto; border-radius: 8px;" onchange="document.getElementById('filterTrenForm').submit();" {{ empty($filterTahun) ? 'disabled' : '' }}>
                            <option value="">Semua bulan</option>
                            @foreach($bulanNama ?? ['1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $nama)
                                <option value="{{ $num }}" {{ (request('bulan') == $num || ($filterBulan ?? '') == $num) ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div style="height: 300px;" class="chart-container">
                <canvas id="trenLaporanChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100">
            <div class="mb-4">
                <h5 class="fw-bold mb-0">Rentang Usia Anak</h5>
                <p class="text-muted small mb-0">Demografi korban</p>
            </div>
            <div style="height: 300px;" class="chart-container">
                <canvas id="usiaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan Terbaru -->
<div class="row mt-4 mb-5 g-4">
    <div class="col-lg-12">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">Laporan Terbaru</h5>
                    <p class="text-muted small mb-0">Daftar kasus yang masuk sistem</p>
                </div>
                <a href="{{ route('admin.laporan') }}" class="btn btn-sm px-3 border-0 fw-bold" style="border-radius: 8px; background: var(--primary-gradient); color: #333;">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Tanggal & Waktu</th>
                            <th>Kategori Kasus</th>
                            <th>Daerah Kejadian</th>
                            <th>Status Penanganan</th>
                            <th class="pe-4 text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanTerbaru ?? [] as $item)
                            @php
                                $status = strtolower($item['status'] ?? 'baru');
                                $pillStyle = match($status) {
                                    'selesai' => 'background: #eafaf2; color: #059669; border: 1px solid #d1fae5;',
                                    'diproses' => 'background: #fff9e6; color: #d97706; border: 1px solid #fef3c7;',
                                    'ditolak' => 'background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2;',
                                    default => 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;',
                                };

                                $kategori = strtolower($item['kategori'] ?? '');
                                $categoryIcon = match($kategori) {
                                    'pernikahan anak' => 'bi-heart-break-fill',
                                    'kekerasan anak' => 'bi-exclamation-triangle-fill',
                                    'bullying' => 'bi-megaphone-fill',
                                    'stunting' => 'bi-hospital-fill',
                                    default => 'bi-tag-fill',
                                };
                                $kategoriTheme = match($kategori) {
                                    'pernikahan anak' => 'theme-pernikahan',
                                    'kekerasan anak' => 'theme-kekerasan',
                                    'bullying' => 'theme-bullying',
                                    'stunting' => 'theme-stunting',
                                    default => 'theme-default',
                                };
                                
                                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                                $parsedBuat = null;
                                if ($tanggalBuat) {
                                    try {
                                        if ($tanggalBuat instanceof \Google\Cloud\Core\Timestamp) {
                                            $parsedBuat = \Carbon\Carbon::instance($tanggalBuat->get());
                                        } elseif (is_numeric($tanggalBuat)) {
                                            $parsedBuat = \Carbon\Carbon::createFromTimestampMs((int)$tanggalBuat);
                                        } else {
                                            $parsedBuat = \Carbon\Carbon::parse($tanggalBuat);
                                        }
                                    } catch (\Exception $e) {}
                                }
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium text-dark">{{ $parsedBuat ? $parsedBuat->locale('id')->translatedFormat('d M Y') : '-' }}</div>
                                    <div class="text-muted small">{{ $parsedBuat ? $parsedBuat->format('H:i') . ' WIB' : '' }}</div>
                                </td>
                                <td>
                                    <span class="badge-category {{ $kategoriTheme }}">
                                        <i class="bi {{ $categoryIcon }}"></i>
                                        {{ $item['kategori'] ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt text-muted me-2"></i>
                                        <span>{{ $item['daerah'] ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill" style="{{ $pillStyle }}">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem; vertical-align: middle;"></i>
                                        {{ $status === 'baru' ? 'Belum Ditangani' : ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-light btn-sm fw-semibold px-3 border" style="border-radius: 6px;" onclick="openDetailLaporan('{{ $item['id'] }}')">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    Belum ada laporan masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modals')
<!-- Modal Detail Laporan -->
<div class="modal fade" id="detailLaporanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailLaporanContent">
                <div class="text-center py-5">
                    <div class="spinner-border"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Fungsi animasi penghitungan
    function animateCount(element, target, duration = 2000) {
        let start = 1;
        const increment = (target - 1) / (duration / 16);
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
            'totalUsers': {{ $totalUsers }},
            'totalLaporan': {{ $totalLaporan }},
            'totalSelesai': {{ $totalSelesai }},
            'totalArticles': {{ $totalArticles }}
        };

        Object.entries(stats).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = "1";
                animateCount(element, value);
            }
        });
    });

    //Script Chart: Tren Laporan per Periode
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trenLaporanChart');
        if (!ctx) return;

        const labels = @json($trenLaporanLabels);
        const data = @json($trenLaporanData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data,
                    borderColor: '#FFCB05',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(255, 203, 5, 0)');
                        gradient.addColorStop(1, 'rgba(255, 203, 5, 0.1)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4361ee',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#FFCB05',
                    pointHoverBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { family: 'Outfit', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1 }
                    }
                }
            }
        });
    });

    // Script Chart: Bar Laporan per Kota/Daerah
    document.addEventListener('DOMContentLoaded', function() {
        const ctxDaerah = document.getElementById('daerahLaporanChart');
        if (!ctxDaerah) return;

        const labels = @json($daerahBarLabels ?? []);
        const data = @json($daerahBarData ?? []);

        new Chart(ctxDaerah, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data,
                    backgroundColor: '#FFCB05',
                    hoverBackgroundColor: '#E6B800',
                    borderRadius: 6,
                    barThickness: 15,
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
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10 }, maxRotation: 45 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 }, stepSize: 25 }
                    }
                }
            }
        });
    });

    // Script Chart: Bar Rentang Usia Anak
    document.addEventListener('DOMContentLoaded', function() {
        const ctxUsia = document.getElementById('usiaChart');
        if (!ctxUsia) return;

        const usiaLabels = @json($usiaBarLabels ?? []);
        const usiaData = @json($usiaBarData ?? []);

        new Chart(ctxUsia, {
            type: 'bar',
            data: {
                labels: usiaLabels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: usiaData,
                    backgroundColor: [
                        '#FFCB05', '#F59E0B', '#D97706', '#B45309', '#78350F'
                    ],
                    borderRadius: 6,
                    barThickness: 20,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1e293b', padding: 10 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 9 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 }, stepSize: 25 }
                    }
                }
            }
        });
    });

    /* =========================
    EVENT DELEGATION (AMAN)
    ========================= */
    document.addEventListener('submit', function(e) {

        /* UPDATE STATUS */
        if (e.target.id === 'statusForm') {
            e.preventDefault();

            const form = e.target;
            const url = form.action;
            const data = new FormData(form);
            const selectedStatus = data.get('status');
            const chatUrl = form.getAttribute('data-chat-url');

            // Pastikan source=dashboard terkirim
            if (!data.has('source')) {
                data.append('source', 'dashboard');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (chatUrl) {
                            if (selectedStatus === 'ditolak') {
                                window.location.href = chatUrl + '?from=reject';
                            } else if (selectedStatus === 'selesai') {
                                window.location.href = chatUrl + '?from=done';
                            } else {
                                location.reload(); // Muat ulang halaman untuk memperbarui status dan statistik
                            }
                        } else {
                            location.reload();
                        }
                    });
                } else {
                     Swal.fire({
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan saat memperbarui status.',
                        icon: 'error'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem.',
                    icon: 'error'
                });
            });
        }
    });

    /* DELETE LAPORAN */
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('#delete-laporan');
        if (deleteBtn) {
            const url = deleteBtn.dataset.url;

            Swal.fire({
                title: 'Yakin?',
                text: 'Laporan akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                         if (res.status === 'success') {
                            Swal.fire('Terhapus!', res.message, 'success')
                            .then(() => location.reload());
                         } else {
                            Swal.fire('Gagal!', res.message || 'Gagal menghapus laporan.', 'error');
                         }
                    })
                    .catch(err => {
                         Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        }
    });

    let detailModal = null;
    function openDetailLaporan(id) {
        const modalEl = document.getElementById('detailLaporanModal');
        if (!detailModal) {
            detailModal = new bootstrap.Modal(modalEl);
        }
        
        // Reset content to spinner
        document.getElementById('detailLaporanContent').innerHTML = `
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
            document.getElementById('detailLaporanContent').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('detailLaporanContent').innerHTML = `
                <div class="alert alert-danger">Gagal memuat data laporan.</div>
            `;
        });
    }
</script>
@endsection