@extends('admin.layouts.app', [
    'activePage' => 'dashboard',
    'navbarTitle' => 'Dashboard Overview',
    'navbarSubtitle' => 'Welcome back, Admin'
])

@section('title', 'Admin Dashboard')

@section('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<!-- Bagian Statistik -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-people-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-0 text-muted">Pengguna Aplikasi</h6>
            </div>
            <h3 class="fw-bold" id="totalUsers">1</h3>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-file-earmark-text-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-0 text-muted">Laporan Masuk</h6>
            </div>
            <h3 class="fw-bold" id="totalLaporan">1</h3>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 1.5rem;"></i>
                <h6 class="mb-0 text-muted">Kasus Selesai</h6>
            </div>
            <h3 class="fw-bold" id="totalSelesai">1</h3>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-book-fill me-2" style="font-size: 1.5rem; color: #8e44ad;"></i>
                <h6 class="mb-0 text-muted">Artikel Edukasi</h6>
            </div>
            <h3 class="fw-bold" id="totalArticles">1</h3>
        </div>
    </div>
</div>

<!-- Bar Chart Laporan per Kota -->
<div class="row mt-3 g-3">
    <div class="col-lg-12">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Laporan per Kota/Daerah</h6>
                <span class="text-muted small">Kalimantan Barat (14 kab/kota)</span>
            </div>
            <div style="height: 420px;">
                <canvas id="daerahLaporanChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tren Laporan per Periode + Rentang Usia Anak -->
<div class="row mt-3 g-3">
    <div class="col-lg-8">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h6 class="fw-bold mb-0">Tren Laporan per Periode</h6>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="filterTrenForm" class="d-flex flex-wrap align-items-center gap-2">
                        <label class="form-label mb-0 small text-muted">Tahun:</label>
                        <select name="tahun" class="form-select form-select-sm" style="width: auto; min-width: 120px;" onchange="document.getElementById('filterTrenForm').submit();">
                            <option value="">12 bulan terakhir</option>
                            @foreach($tahunList ?? [] as $y)
                                <option value="{{ $y }}" {{ (request('tahun') == $y || ($filterTahun ?? '') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <label class="form-label mb-0 small text-muted ms-1">Bulan:</label>
                        <select name="bulan" class="form-select form-select-sm" style="width: auto; min-width: 140px;" onchange="document.getElementById('filterTrenForm').submit();" {{ empty($filterTahun) ? 'disabled' : '' }}>
                            <option value="">Semua bulan</option>
                            @php
                                $bulanNama = ['1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                            @endphp
                            @foreach($bulanNama as $num => $nama)
                                <option value="{{ $num }}" {{ (request('bulan') == $num || ($filterBulan ?? '') == $num) ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                    </form>
                    <span class="badge bg-primary">{{ $trenPeriodLabel ?? '12 bulan terakhir' }}</span>
                    <a href="{{ route('admin.dashboard.refresh') }}" class="btn btn-sm btn-outline-secondary" title="Refresh Data">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
            <div style="height: 280px;">
                <canvas id="trenLaporanChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="bg-white shadow-sm rounded p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Rentang Usia Anak</h6>
            </div>
            <div style="height: 280px;">
                <canvas id="usiaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan Terbaru -->
<div class="row mt-3 g-3">
    <div class="col-lg-12">
        <div class="bg-white shadow-sm rounded p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Laporan Terbaru</h6>
                <a href="{{ route('admin.laporan') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Daerah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanTerbaru ?? [] as $item)
                            @php
                                $status = strtolower($item['status'] ?? 'baru');
                                $badgeColor = match($status) {
                                    'selesai' => 'success',
                                    'diproses' => 'warning',
                                    'ditolak' => 'danger',
                                    default => 'secondary',
                                };
                                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                                $parsedBuat = null;
                                if ($tanggalBuat !== null && $tanggalBuat !== '') {
                                    try {
                                        $parsedBuat = $tanggalBuat instanceof \DateTimeInterface
                                            ? \Carbon\Carbon::instance($tanggalBuat)
                                            : \Carbon\Carbon::parse($tanggalBuat);
                                    } catch (\Throwable $e) {
                                        try {
                                            $parsedBuat = \Carbon\Carbon::createFromLocaleFormat('d M Y H:i', 'id', $tanggalBuat);
                                        } catch (\Throwable $e2) {
                                            try {
                                                $parsedBuat = \Carbon\Carbon::createFromLocaleFormat('d M Y', 'id', $tanggalBuat);
                                            } catch (\Throwable $e3) {
                                                $parsedBuat = null;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-nowrap">{{ $parsedBuat ? $parsedBuat->locale('id')->translatedFormat('d M Y, H:i') : ($tanggalBuat ?: '-') }}</td>
                                <td>{{ $item['kategori'] ?? '-' }}</td>
                                <td>{{ $item['daerah'] ?? '-' }}</td>
                                @php
                                    $statusDisplay = $status === 'baru' ? 'Belum Ditangani' : ucfirst($status);
                                @endphp
                                <td><span class="badge bg-{{ $badgeColor }}">{{ $statusDisplay }}</span></td>
                                <td>
                                <button
                                    class="btn btn-primary btn-sm"
                                    onclick="openDetailLaporan('{{ $item['id'] }}')">
                                    Detail
                                </button>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada laporan.</td>
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
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.15)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Laporan: ' + context.parsed.y + ' kasus';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0,
                            font: { size: 11 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            font: { size: 11 }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Laporan',
                            font: { size: 11 }
                        },
                        grid: {
                            drawBorder: false
                        }
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
                    backgroundColor: 'rgba(52, 152, 219, 0.6)',
                    borderColor: '#3498db',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' laporan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: { size: 10 },
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 30
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 25,
                            precision: 0,
                            font: { size: 11 }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Laporan',
                            font: { size: 11 }
                        },
                        grid: {
                            drawBorder: false
                        }
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
                        'rgba(231, 76, 60, 0.6)',
                        'rgba(46, 204, 113, 0.6)',
                        'rgba(52, 152, 219, 0.6)',
                        'rgba(243, 156, 18, 0.6)',
                        'rgba(155, 89, 182, 0.6)'
                    ],
                    borderColor: [
                        '#e74c3c',
                        '#2ecc71',
                        '#3498db',
                        '#f39c12',
                        '#9b59b6'
                    ],
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' laporan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 30
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 25,
                            precision: 0,
                            font: { size: 11 }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Laporan',
                            font: { size: 11 }
                        },
                        grid: {
                            drawBorder: false
                        }
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