@extends('admin.layouts.app', [
    'activePage' => 'laporan',
    'navbarTitle' => 'Manajemen Pengaduan',
    'navbarSubtitle' => 'Admin'
])

@section('title', 'Pengaduan')

@section('head-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('styles')
    <style>
        .status-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        
        .status-baru { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .status-diproses { background: #FFFAE6; color: #B45309; border: 1px solid rgba(255, 203, 5, 0.3); }
        .status-selesai { background: #ecfdf5; color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-ditolak { background: #fef2f2; color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.1); }
        .status-dibatalkan { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

        .badge-category {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: 1.5px solid transparent; /* Dipertebal */
        }

        .badge-category i {
            font-size: 0.9rem;
        }

        .theme-pernikahan { background: #fff1f2; color: #e11d48; border-color: rgba(225, 29, 72, 0.2) !important; }
        .theme-kekerasan { background: #fef2f2; color: #dc2626; border-color: rgba(220, 38, 38, 0.2) !important; }
        .theme-bullying { background: #FFFAE6; color: #E6B800; border-color: rgba(255, 203, 5, 0.3) !important; }
        .theme-stunting { background: #ecfdf5; color: #059669; border-color: rgba(16, 185, 129, 0.2) !important; }
        .theme-default { background: #f8fafc; color: #64748b; border-color: rgba(100, 116, 139, 0.2) !important; }

        .filter-badge {
            background: #ffffff;
            color: #1e293b;
            padding: 6px 12px;
            margin: 4px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        
        .filter-badge i {
            margin-left: 8px;
            cursor: pointer;
            color: #ef4444;
            transition: transform 0.2s;
        }

        .filter-badge i:hover { transform: scale(1.2); }
        
        #filterModal .modal-header {
            background: #FFCB05;
            color: #333;
            border-bottom: none;
        }

        #filterModal .modal-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        #filterModal .btn-close {
            filter: none;
        }

        #filterModal .modal-content {
            border-radius: 1rem;
            overflow: hidden;
            border: none;
        }

        #filterModal .mb-3 {
            background: #ffffff;
            padding: 1.25rem;
            border-radius: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .quick-date-btn {
            padding: 5px 12px;
            font-size: 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-date-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .quick-date-btn.active {
            background: #FFCB05;
            color: #333;
            border-color: #FFCB05;
        }

        .date-range-divider {
            color: #94a3b8;
            font-weight: bold;
        }

        /* Gaya Checkbox Kategori Premium */
        .filter-scroll-container {
            max-height: 180px;
            overflow-y: auto;
            padding: 4px;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            background: #fbfcfd;
        }

        .filter-search-input {
            font-size: 0.8rem;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 8px;
            border: 1.5px solid #f1f5f9;
            background: #fff;
        }

        #filterModal .form-check-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            width: 100%;
            background-color: #ffffff;
            color: #475569;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        #filterModal .form-check-input:checked + .form-check-label {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            font-weight: 700;
        }

        /* Specific Theme Colors for Filter */
        .filter-pernikahan:checked + .form-check-label { background: #fff1f2 !important; color: #e11d48 !important; border-color: #fda4af !important; }
        .filter-kekerasan:checked + .form-check-label { background: #fef2f2 !important; color: #dc2626 !important; border-color: #fecaca !important; }
        .filter-bullying:checked + .form-check-label { background: #FFFAE6 !important; color: #B45309 !important; border-color: #FFCB05 !important; }
        .filter-stunting:checked + .form-check-label { background: #ecfdf5 !important; color: #059669 !important; border-color: #10b981 !important; }
        .filter-default:checked + .form-check-label { background: #f8fafc !important; color: #475569 !important; border-color: #e2e8f0 !important; }

        /* Status Filter Colors */
        .filter-baru:checked + .form-check-label { background: #f1f5f9 !important; color: #475569 !important; border-color: #cbd5e1 !important; }
        .filter-diproses:checked + .form-check-label { background: #fffbeb !important; color: #d97706 !important; border-color: #fde68a !important; }
        .filter-selesai:checked + .form-check-label { background: #f0fdf4 !important; color: #16a34a !important; border-color: #bbf7d0 !important; }
        .filter-ditolak:checked + .form-check-label { background: #fef2f2 !important; color: #dc2626 !important; border-color: #fecaca !important; }

        /* Gaya Footer Modal */
        #filterModal .modal-footer {
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        #filterModal .btn {
            border-radius: 0.75rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }

        #filterModal .btn-primary {
            background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%);
            border: none;
            color: #333;
        }

        #filterModal .btn-primary:hover {
            opacity: 0.9;
        }

        #filterModal .btn-secondary {
            background-color: #f1f3f5;
            color: #333;
            border: 1px solid #ced4da;
        }

        #filterModal .btn-secondary:hover {
            background-color: #e2e6ea;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            padding: 0.75rem 0;
        }
        .pagination-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .pagination-wrapper .pagination {
            margin: 0;
        }
        .pagination-wrapper .page-link {
            padding: 0.4rem 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="glass-card overflow-hidden mb-5">
        <div class="p-4 bg-white bg-opacity-50 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Daftar Pengaduan</h4>
                <p class="text-muted small mb-0">Kelola dan tindak lanjuti pengaduan dari masyarakat</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.laporan.refresh') }}" class="btn btn-light shadow-sm" style="border-radius: 10px;" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise" style="color: #FFCB05;"></i>
                </a>
                <a href="{{ route('admin.laporan.downloadList') }}" id="downloadListBtn" class="btn btn-light shadow-sm" style="border-radius: 10px; border: 1px solid rgba(255, 203, 5, 0.2); color: #B45309;">
                    <i class="bi bi-download me-2"></i>Ekspor CSV
                </a>
            </div>
        </div>
        <div class="p-4">
            <!-- Filter dan Pencarian -->
            <div class="row g-3 mb-4">
                <div class="col-md-9">
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text border-0 bg-white ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0 py-2" placeholder="Cari nama pelapor atau detail kejadian..." id="search-input">
                        <button class="btn btn-light border-start px-4 fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" style="background: #f8faff; color: #FFCB05;">
                            <i class="bi bi-funnel me-2"></i>Filter <span class="badge ms-1" id="filterCount" style="display:none; background: #FFCB05; color: #333;">0</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Area Filter Aktif -->
            <div class="active-filters d-flex flex-wrap gap-2 mb-3" id="activeFilters"></div>

            <!-- Tabel Pengaduan -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Nama Pelapor</th>
                            <th>Tipe Kasus</th>
                            <th>Tanggal Kejadian</th>
                            <th>Status</th>
                            <th>Waktu Lapor</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                            @php
                                $kategori = strtolower($item['case_type'] ?? ($item['kategori'] ?? 'lainnya'));
                                $kategoriBadgeClass = match($kategori) {
                                    'pernikahan anak' => 'bg-pernikahan',
                                    'kekerasan anak' => 'bg-kekerasan',
                                    'bullying' => 'bg-bullying',
                                    'stunting' => 'bg-stunting',
                                    default => 'bg-secondary',
                                };
                                $kategoriDisplay = $item['case_type'] ?? ($item['kategori'] ?? '-');

                                $rawStatus = $item['report_status'] ?? ($item['status'] ?? 'baru');
                                $status = strtolower(trim($rawStatus));
                                if (in_array($status, ['belum ditangani', 'belum_ditangani', 'pending', ''])) {
                                    $status = 'baru';
                                }
                                $badgeColor = match($status) {
                                    'selesai' => 'success',
                                    'diproses' => 'warning',
                                    'ditolak' => 'danger',
                                    'dibatalkan' => 'secondary',
                                    default => 'secondary',
                                };
                                $tanggalKejadian = $item['incident_date'] ?? null;
                                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                                $parsedKejadian = null;
                                $parsedBuat = null;
                                if ($tanggalKejadian !== null && $tanggalKejadian !== '') {
                                    try {
                                        $parsedKejadian = $tanggalKejadian instanceof \DateTimeInterface
                                            ? \Carbon\Carbon::instance($tanggalKejadian)
                                            : \Carbon\Carbon::parse($tanggalKejadian);
                                    } catch (\Throwable $e) {
                                        try {
                                            $parsedKejadian = \Carbon\Carbon::createFromLocaleFormat('d M Y', 'id', $tanggalKejadian);
                                        } catch (\Throwable $e2) {
                                            $parsedKejadian = null;
                                        }
                                    }
                                }
                                if ($tanggalBuat !== null && $tanggalBuat !== '') {
                                    try {
                                        if ($tanggalBuat instanceof \Google\Cloud\Core\Timestamp) {
                                            $parsedBuat = \Carbon\Carbon::instance($tanggalBuat->get());
                                        } elseif (is_numeric($tanggalBuat)) {
                                            $parsedBuat = \Carbon\Carbon::createFromTimestampMs((int)$tanggalBuat);
                                        } elseif ($tanggalBuat instanceof \DateTimeInterface) {
                                            $parsedBuat = \Carbon\Carbon::instance($tanggalBuat);
                                        } else {
                                            $parsedBuat = \Carbon\Carbon::parse($tanggalBuat);
                                        }
                                    } catch (\Throwable $e) {
                                        $parsedBuat = null;
                                    }
                                }
                            @endphp
                            <tr class="laporan-row" 
                                data-report-id="{{ $item['id'] }}"
                                data-kategori="{{ $kategori }}"
                                data-daerah="{{ $item['daerah'] ?? '' }}"
                                data-tanggal="{{ $parsedBuat ? $parsedBuat->format('Y-m-d') : '' }}"
                                data-status="{{ $status }}">
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $item['user_name'] ?? ($item['nama'] ?? '-') }}</div>
                                    <div class="text-muted small">{{ $item['daerah'] ?? '-' }}</div>
                                </td>
                                <td>
                                    @php
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
                                    @endphp
                                    <span class="badge-category {{ $kategoriTheme }}">
                                        <i class="bi {{ $categoryIcon }}"></i>
                                        {{ $kategoriDisplay }}
                                    </span>
                                </td>
                                <td class="text-nowrap small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $parsedKejadian ? $parsedKejadian->locale('id')->translatedFormat('d M Y') : ($tanggalKejadian ?: '-') }}
                                </td>
                                @php
                                    $displayStatus = match($status) {
                                        'baru' => 'Belum Ditangani',
                                        'dibatalkan' => 'Dibatalkan User',
                                        default => ucfirst($status)
                                    };
                                    $statusPillClass = 'status-' . $status;
                                @endphp
                                <td><span class="status-pill {{ $statusPillClass }}">{{ $displayStatus }}</span></td>
                                <td class="text-nowrap">
                                    <div class="small fw-semibold {{ $status === 'baru' ? 'text-primary' : 'text-muted' }}">
                                        {{ $parsedBuat ? $parsedBuat->locale('id')->translatedFormat('d M Y') : ($tanggalBuat ?: '-') }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">
                                        {{ $parsedBuat ? $parsedBuat->format('H:i') . ' WIB' : '' }}
                                    </div>
                                </td>
                                <td class="pe-3 text-end">
                                    <button class="btn btn-primary btn-sm px-3 shadow-sm border-0" style="border-radius: 8px; background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); color: #333;" onclick="openDetailLaporan('{{ $item['id'] }}')">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div id="paginationWrapper" class="pagination-wrapper d-none">
                <div class="pagination-info" id="paginationInfo"></div>
                <nav aria-label="Navigasi halaman pengaduan">
                    <ul class="pagination mb-0" id="paginationNav"></ul>
                </nav>
            </div>
        </div>
    </div>


@endsection

@section('modals')
    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Pilih Filter Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <!-- Filter Daerah (Searchable) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Daerah Kejadian</label>
                            <input type="text" class="form-control filter-search-input" id="searchDaerah" placeholder="Cari kota/daerah...">
                            <div class="filter-scroll-container" id="daerahChecklistContainer">
                                @forelse($daerahList ?? [] as $daerah)
                                    <div class="px-3 py-1 daerah-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="daerah-{{ \Illuminate\Support\Str::slug($daerah) }}" name="daerahFilter" value="{{ $daerah }}">
                                            <label class="form-check-label border-0 shadow-none bg-transparent p-0" for="daerah-{{ \Illuminate\Support\Str::slug($daerah) }}">
                                                {{ $daerah }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted small">Tidak ada data daerah.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="row">
                            <!-- Filter Kategori -->
                            <div class="col-md-7 mb-4">
                                <label class="form-label fw-bold mb-2">Kategori Kasus</label>
                                <div class="filter-scroll-container">
                                    <div id="kategoriFilterContainer" class="row g-2 m-0 p-0">
                                        @forelse($kategoriList ?? [] as $kategori)
                                            @php
                                                $kategoriValue = is_string($kategori) ? strtolower($kategori) : $kategori;
                                                $kategoriId = 'kategori-' . \Illuminate\Support\Str::slug($kategoriValue);
                                                $themeClass = match($kategoriValue) {
                                                    'pernikahan anak' => 'filter-pernikahan',
                                                    'kekerasan anak' => 'filter-kekerasan',
                                                    'bullying' => 'filter-bullying',
                                                    'stunting' => 'filter-stunting',
                                                    default => 'filter-default',
                                                };
                                            @endphp
                                            <div class="col-12">
                                                <input class="form-check-input d-none {{ $themeClass }}" type="checkbox" id="{{ $kategoriId }}" name="kategoriFilter" value="{{ $kategoriValue }}">
                                                <label class="form-check-label" for="{{ $kategoriId }}">
                                                    {{ $kategori }}
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12 text-muted small p-2 text-center">Tidak ada kategori.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Status -->
                            <div class="col-md-5 mb-4">
                                <label class="form-label fw-bold mb-2">Status</label>
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check p-0 m-0">
                                        <input class="form-check-input d-none filter-baru" type="checkbox" id="statusBaru" value="baru">
                                        <label class="form-check-label" for="statusBaru">
                                            <i class="bi bi-clock"></i> Baru
                                        </label>
                                    </div>
                                    <div class="form-check p-0 m-0">
                                        <input class="form-check-input d-none filter-diproses" type="checkbox" id="statusDiproses" value="diproses">
                                        <label class="form-check-label" for="statusDiproses">
                                            <i class="bi bi-arrow-repeat"></i> Proses
                                        </label>
                                    </div>
                                    <div class="form-check p-0 m-0">
                                        <input class="form-check-input d-none filter-selesai" type="checkbox" id="statusSelesai" value="selesai">
                                        <label class="form-check-label" for="statusSelesai">
                                            <i class="bi bi-check-circle"></i> Selesai
                                        </label>
                                    </div>
                                    <div class="form-check p-0 m-0">
                                        <input class="form-check-input d-none filter-ditolak" type="checkbox" id="statusDitolak" value="ditolak">
                                        <label class="form-check-label" for="statusDitolak">
                                            <i class="bi bi-x-circle"></i> Tolak
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Waktu (Bottom) -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Waktu Lapor</label>
                                <div class="d-flex gap-1">
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate('today', this)">Hari</button>
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate('week', this)">Minggu</button>
                                    <button type="button" class="quick-date-btn" onclick="setQuickDate('month', this)">Bulan</button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" class="form-control form-control-sm border-light bg-light" id="startDate" max="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" style="border-radius: 10px;">
                                <span class="date-range-divider">-</span>
                                <input type="date" class="form-control form-control-sm border-light bg-light" id="endDate" max="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" style="border-radius: 10px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="resetFilters">Reset Filter</button>
                    <button type="button" class="btn btn-primary" id="applyFilters" data-bs-dismiss="modal">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

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
    <script>
        // Fungsi saat dokumen siap
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const rows = document.querySelectorAll('table tbody tr.laporan-row');
            const activeFiltersContainer = document.getElementById('activeFilters');
            const filterCountBadge = document.getElementById('filterCount');
            
            // Pencarian Daerah (client-side filter)
            document.getElementById('searchDaerah').addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.daerah-item').forEach(item => {
                    const text = item.innerText.toLowerCase();
                    item.style.display = text.includes(term) ? 'block' : 'none';
                });
            });


            // Inisialisasi state filter
            let activeFilters = {
                daerah: [],
                dateStart: '',
                dateEnd: '',
                kategori: [],
                status: []
            };

            // Pagination
            const perPage = 10;
            let currentPage = 1;
            
            // Terapkan filter pencarian
            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilters();
            });
            
            // Tombol terapkan filter
            document.getElementById('applyFilters').addEventListener('click', function() {
                // Ambil filter daerah
                activeFilters.daerah = [];
                document.querySelectorAll('input[name="daerahFilter"]:checked').forEach(checkbox => {
                    activeFilters.daerah.push(checkbox.value);
                });
                
                // Ambil filter range tanggal
                let dateStart = document.getElementById('startDate').value;
                let dateEnd = document.getElementById('endDate').value;
                
                const today = new Date();
                const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                if (dateStart && dateStart > todayStr) {
                    dateStart = todayStr;
                    document.getElementById('startDate').value = todayStr;
                }
                if (dateEnd && dateEnd > todayStr) {
                    dateEnd = todayStr;
                    document.getElementById('endDate').value = todayStr;
                }
                
                activeFilters.dateStart = dateStart;
                activeFilters.dateEnd = dateEnd;
                
                // Ambil filter kategori (dinamis)
                activeFilters.kategori = [];
                document.querySelectorAll('#kategoriFilterContainer input[name="kategoriFilter"]:checked').forEach(checkbox => {
                    activeFilters.kategori.push(checkbox.value);
                });
                
                // Ambil filter status
                activeFilters.status = [];
                document.querySelectorAll('input[id^="status"]:checked').forEach(checkbox => {
                    activeFilters.status.push(checkbox.value);
                });
                
                // Update UI dan terapkan filter
                updateActiveFiltersUI();
                currentPage = 1;
                applyFilters();
            });

            // Fungsi Tombol Cepat Tanggal
            window.setQuickDate = function(type, btn) {
                const startInput = document.getElementById('startDate');
                const endInput = document.getElementById('endDate');
                const today = new Date();
                let start = new Date();
                
                // Reset active state
                document.querySelectorAll('.quick-date-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                if (type === 'today') {
                    start = today;
                } else if (type === 'week') {
                    start.setDate(today.getDate() - 7);
                } else if (type === 'month') {
                    start.setMonth(today.getMonth() - 1);
                }

                const formatDate = (date) => {
                    let d = new Date(date),
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();

                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;

                    return [year, month, day].join('-');
                };

                startInput.value = formatDate(start);
                endInput.value = formatDate(today);
            };
            
            // Reset filter
            document.getElementById('resetFilters').addEventListener('click', function() {
                // Reset form
                document.getElementById('filterForm').reset();
                
                // Clear active filters
                activeFilters = {
                    daerah: [],
                    dateStart: '',
                    dateEnd: '',
                    kategori: [],
                    status: []
                };
                
                // Update UI
                updateActiveFiltersUI();
                currentPage = 1;
                applyFilters();
            });
            
            // Fungsi update UI filter aktif
            function updateActiveFiltersUI() {
                activeFiltersContainer.innerHTML = '';
                let filterCount = 0;
                
                // Tambah filter daerah
                activeFilters.daerah.forEach(daerah => {
                    addFilterBadge('Daerah: ' + daerah, () => {
                        activeFilters.daerah = activeFilters.daerah.filter(d => d !== daerah);
                        // Uncheck checkbox
                        const cb = document.getElementById('daerah-' + daerah.replace(/\s+/g, '-').toLowerCase());
                        if (cb) cb.checked = false;
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Tambah filter range tanggal
                if (activeFilters.dateStart && activeFilters.dateEnd) {
                    const formattedStartDate = formatDate(activeFilters.dateStart);
                    const formattedEndDate = formatDate(activeFilters.dateEnd);
                    addFilterBadge(`Periode: ${formattedStartDate} - ${formattedEndDate}`, () => {
                        activeFilters.dateStart = '';
                        activeFilters.dateEnd = '';
                        document.getElementById('startDate').value = '';
                        document.getElementById('endDate').value = '';
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                }
                
                // Tambah filter kategori (dinamis)
                activeFilters.kategori.forEach(kategori => {
                    const displayKategori = kategori.charAt(0).toUpperCase() + kategori.slice(1);
                    addFilterBadge('Kategori: ' + displayKategori, () => {
                        activeFilters.kategori = activeFilters.kategori.filter(k => k !== kategori);
                        const container = document.getElementById('kategoriFilterContainer');
                        if (container) {
                            container.querySelectorAll('input[name="kategoriFilter"]').forEach(cb => {
                                if (cb.value === kategori) cb.checked = false;
                            });
                        }
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Tambah filter status
                activeFilters.status.forEach(status => {
                    const statusLabelMap = {
                        'baru': 'Belum Ditangani',
                        'diproses': 'Diproses',
                        'selesai': 'Selesai',
                        'ditolak': 'Ditolak'
                    };
                    const statusLabel = statusLabelMap[status] || (status.charAt(0).toUpperCase() + status.slice(1));
                    addFilterBadge('Status: ' + statusLabel, () => {
                        activeFilters.status = activeFilters.status.filter(s => s !== status);
                        const statusCheckbox = document.getElementById('status' + status.charAt(0).toUpperCase() + status.slice(1));
                        if (statusCheckbox) {
                            statusCheckbox.checked = false;
                        }
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Update badge count filter
                filterCountBadge.textContent = filterCount;
                filterCountBadge.style.display = filterCount > 0 ? 'inline-flex' : 'none';
            }
            
            // Fungsi tambah filter badge
            function addFilterBadge(text, removeCallback) {
                const badge = document.createElement('span');
                badge.className = 'filter-badge';
                badge.innerHTML = text + ' <i class="bi bi-x-circle"></i>';
                badge.querySelector('i').addEventListener('click', removeCallback);
                activeFiltersContainer.appendChild(badge);
            }
            
            // Format tanggal untuk display
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }
            
            // Terapkan semua filter
            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                
                rows.forEach(row => {
                    const namaPelapor = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const daerah = row.getAttribute('data-daerah');
                    const tanggal = row.getAttribute('data-tanggal');
                    const kategori = row.getAttribute('data-kategori');
                    const status = row.getAttribute('data-status');
                    
                    // Cek search term
                    const matchSearch = namaPelapor.includes(searchTerm);
                    
                    // Cek filter daerah
                    const matchDaerah = activeFilters.daerah.length === 0 || 
                                        activeFilters.daerah.includes(daerah);
                    
                    // Cek range tanggal
                    let matchDate = true;
                    if (activeFilters.dateStart && activeFilters.dateEnd) {
                        matchDate = tanggal >= activeFilters.dateStart && 
                                    tanggal <= activeFilters.dateEnd;
                    }
                    
                    // Cek kategori
                    const matchKategori = activeFilters.kategori.length === 0 || 
                                        activeFilters.kategori.includes(kategori);
                    
                    // Cek status
                    const matchStatus = activeFilters.status.length === 0 || 
                                        activeFilters.status.includes(status);
                    
                    // Tampilkan atau sembunyikan row berdasarkan semua filter
                    row.style.display = (matchSearch && matchDaerah && matchDate && 
                                        matchKategori && matchStatus) ? '' : 'none';
                });

                // Terapkan pagination pada baris yang tampil
                const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                const totalVisible = visibleRows.length;
                const totalPages = Math.max(1, Math.ceil(totalVisible / perPage));
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                visibleRows.forEach((row, i) => {
                    row.style.display = (i >= start && i < end) ? '' : 'none';
                });

                // Perbarui UI pagination dan URL download
                updatePaginationUI(totalVisible, totalPages);
                updateDownloadLink();
            }

            // Update tampilan pagination (info + navigasi)
            function updatePaginationUI(totalVisible, totalPages) {
                const wrapper = document.getElementById('paginationWrapper');
                const infoEl = document.getElementById('paginationInfo');
                const navEl = document.getElementById('paginationNav');
                if (!wrapper || !infoEl || !navEl) return;

                if (totalVisible === 0) {
                    wrapper.classList.add('d-none');
                    return;
                }
                wrapper.classList.remove('d-none');

                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(currentPage * perPage, totalVisible);
                infoEl.textContent = `Menampilkan ${start}–${end} dari ${totalVisible} pengaduan`;

                navEl.innerHTML = '';
                const addPageItem = (label, pageNum, disabled, active) => {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.textContent = label;
                    if (!disabled) {
                        a.addEventListener('click', function(e) {
                            e.preventDefault();
                            currentPage = pageNum;
                            applyFilters();
                        });
                    }
                    li.appendChild(a);
                    navEl.appendChild(li);
                };

                addPageItem('Sebelumnya', currentPage - 1, currentPage <= 1, false);
                const maxButtons = 5;
                let from = Math.max(1, currentPage - Math.floor(maxButtons / 2));
                let to = Math.min(totalPages, from + maxButtons - 1);
                if (to - from + 1 < maxButtons) from = Math.max(1, to - maxButtons + 1);
                for (let p = from; p <= to; p++) {
                    addPageItem(p, p, false, p === currentPage);
                }
                addPageItem('Selanjutnya', currentPage + 1, currentPage >= totalPages, false);
            }
            
            // Perbarui link Download List dengan filter + pencarian saat ini
            function updateDownloadLink() {
                const baseUrl = '{{ route("admin.laporan.downloadList") }}';
                const params = [];
                if (activeFilters.daerah.length) params.push('daerah=' + encodeURIComponent(activeFilters.daerah.join(',')));
                if (activeFilters.dateStart) params.push('date_start=' + encodeURIComponent(activeFilters.dateStart));
                if (activeFilters.dateEnd) params.push('date_end=' + encodeURIComponent(activeFilters.dateEnd));
                if (activeFilters.kategori.length) params.push('kategori=' + encodeURIComponent(activeFilters.kategori.join(',')));
                if (activeFilters.status.length) params.push('status=' + encodeURIComponent(activeFilters.status.join(',')));
                const searchVal = searchInput.value.trim();
                if (searchVal) params.push('search=' + encodeURIComponent(searchVal));
                const url = params.length ? baseUrl + '?' + params.join('&') : baseUrl;
                const btn = document.getElementById('downloadListBtn');
                if (btn) btn.setAttribute('href', url);
            }
            
            // Setup awal UI filter dan pagination
            updateActiveFiltersUI();
            updateDownloadLink();
            applyFilters();

            // Event listener untuk modal show
            document.getElementById('filterModal').addEventListener('show.bs.modal', function () {
                // Check checkboxes daerah
                document.querySelectorAll('input[name="daerahFilter"]').forEach(cb => {
                    cb.checked = activeFilters.daerah.includes(cb.value);
                });

                // Reset input tanggal
                document.getElementById('startDate').value = activeFilters.dateStart;
                document.getElementById('endDate').value = activeFilters.dateEnd;

                // Reset checkbox kategori
                document.querySelectorAll('input[name="kategoriFilter"]').forEach(checkbox => {
                    const value = checkbox.value;
                    checkbox.checked = activeFilters.kategori.includes(value);
                });

                // Reset checkbox status
                document.querySelectorAll('input[id^="status"]').forEach(checkbox => {
                    const value = checkbox.value;
                    checkbox.checked = activeFilters.status.includes(value);
                });
            });
        });

        let detailModal = null;
        function openDetailLaporan(id) {
            const modalEl = document.getElementById('detailPengaduanModal');
            if (!detailModal) {
                detailModal = new bootstrap.Modal(modalEl);
            }
            
            // Reset content to spinner
            document.getElementById('detailPengaduanContent').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">Memuat data...</div>
                </div>
            `;
            
            detailModal.show();

            fetch(`/admin/laporan/${id}`, {
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
                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message,
                        icon: 'success'
                    }).then(() => {
                        if (chatUrl) {
                            if (selectedStatus === 'ditolak') {
                                window.location.href = chatUrl + '?from=reject';
                            } else if (selectedStatus === 'selesai') {
                                window.location.href = chatUrl + '?from=done';
                            } else {
                                location.reload();
                            }
                        } else {
                            location.reload();
                        }
                    });
                });
            }
        });

        /* DELETE PENGADUAN */
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('#delete-pengaduan');
            if (deleteBtn) {
                const url = deleteBtn.dataset.url;

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Pengaduan akan dihapus permanen',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(() => location.reload());
                    }
                });
            }
        });
    </script>
@endsection