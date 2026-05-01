<div class="container-fluid py-2">
    @php
        $kategori = strtolower($laporan['kategori'] ?? ($laporan['case_type'] ?? ''));
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
            default => 'theme-default'
        };
        $statusVal = $laporan['report_status'] ?? ($laporan['status'] ?? 'baru');
        $statusKey = strtolower($statusVal);
        $statusPillClass = 'status-' . $statusKey;
        $statusDisplay = $statusKey === 'baru' ? 'Belum Ditangani' : ucfirst($statusVal);
    @endphp

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <span class="badge-category {{ $kategoriTheme }} mb-2">
                <i class="bi {{ $categoryIcon }}"></i>
                {{ $laporan['kategori'] ?? ($laporan['case_type'] ?? '-') }}
            </span>
            <h5 class="fw-bold text-dark mb-0">ID Pengaduan: #{{ $laporan['id'] }}</h5>
        </div>
        <span class="status-pill {{ $statusPillClass }}">{{ $statusDisplay }}</span>
    </div>

    <div class="row g-4 mb-4">
        <!-- Section: Reporter Info -->
        <div class="col-md-6">
            <div class="p-3 bg-light bg-opacity-50 rounded-4 border h-100">
                <h6 class="fw-bold small text-uppercase text-muted mb-3" style="letter-spacing: 0.05em;">Informasi Pelapor</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Nama Lengkap</small>
                    <span class="fw-bold">{{ $laporan['user_name'] ?? ($laporan['nama'] ?? '-') }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Kontak / No. HP</small>
                    <span class="fw-bold text-primary">{{ $laporan['phone_number'] ?? ($laporan['no_hp'] ?? '-') }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Peran Pelapor</small>
                    <span class="badge bg-white text-dark border fw-normal">{{ $laporan['reporter_role'] ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Section: Victim Info -->
        <div class="col-md-6">
            <div class="p-3 bg-light bg-opacity-50 rounded-4 border h-100">
                <h6 class="fw-bold small text-uppercase text-muted mb-3" style="letter-spacing: 0.05em;">Informasi Korban</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Usia Anak</small>
                    <span class="fw-bold">{{ $laporan['child_age'] ?? '-' }} Tahun</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Hubungan dengan Pelapor</small>
                    <span class="fw-bold">{{ $laporan['victim_relationship'] ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Section: Incident Info -->
        <div class="col-12">
            <div class="p-3 bg-white rounded-4 border shadow-sm">
                <h6 class="fw-bold small text-uppercase text-muted mb-3" style="letter-spacing: 0.05em;">Detail Kejadian</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Waktu Lapor</small>
                        <span class="fw-semibold">
                            @if(!empty($laporan['created_date'] ?? $laporan['create_at'] ?? null))
                                {{ \Carbon\Carbon::parse($laporan['created_date'] ?? $laporan['create_at'])->translatedFormat('d M Y, H:i') }}
                            @else - @endif
                        </span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Waktu Kejadian</small>
                        <span class="fw-semibold">
                            @if(!empty($laporan['incident_date']))
                                @php
                                    try {
                                        $incDate = $laporan['incident_date'] instanceof \DateTimeInterface
                                            ? \Carbon\Carbon::instance($laporan['incident_date'])
                                            : \Carbon\Carbon::parse($laporan['incident_date']);
                                        echo $incDate->translatedFormat('d M Y');
                                    } catch (\Throwable $e) { echo $laporan['incident_date']; }
                                @endphp
                            @else - @endif
                        </span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Lokasi</small>
                        <span class="fw-semibold">{{ $laporan['incident_city'] ?? ($laporan['daerah'] ?? '-') }}</span>
                        <div class="small text-muted">{{ $laporan['incident_location'] ?? '-' }}</div>
                    </div>
                </div>
                
                <hr class="my-3 opacity-10">
                
                <div>
                    <small class="text-muted d-block mb-1">Deskripsi Pengaduan</small>
                    <div class="p-3 bg-light rounded-3 small" style="line-height: 1.6;">
                        {{ $laporan['detail_description'] ?? ($laporan['deskripsi_lengkap'] ?? '-') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Evidence -->
        @if(!empty($laporan['evidence_image']))
        <div class="col-12">
            <h6 class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing: 0.05em;">Lampiran Bukti</h6>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $evidenceImg = $laporan['evidence_image'];
                    $evidenceUrls = is_array($evidenceImg) ? $evidenceImg : (is_string($evidenceImg) ? [$evidenceImg] : []);
                @endphp
                @foreach($evidenceUrls as $url)
                    @if(!empty($url) && is_string($url))
                        <a href="{{ $url }}" target="_blank" class="evidence-thumb shadow-sm rounded-3 overflow-hidden border">
                            <img src="{{ $url }}" alt="Bukti" style="width: 100px; height: 100px; object-fit: cover;">
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Update Status & Actions -->
    <div class="glass-card p-4 bg-primary bg-opacity-10 border-primary border-opacity-10 rounded-4">
        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-arrow-repeat" style="color: #FFCB05;"></i> Tindak Lanjut Pengaduan
        </h6>
        
        <form id="statusForm" action="{{ route('admin.laporan.setStatus', $laporan['id']) }}" data-chat-url="{{ route('admin.laporan.chat', $laporan['id']) }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="{{ $source ?? 'laporan' }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <select name="status" class="form-select border-0 shadow-sm" style="border-radius: 10px;" required>
                        <option value="">-- Pilih Status Baru --</option>
                        <option value="baru" @selected($statusKey=='baru')>Belum Ditangani</option>
                        <option value="diproses" @selected($statusKey=='diproses')>Diproses</option>
                        <option value="selesai" @selected($statusKey=='selesai')>Selesai (Tutup Pengaduan)</option>
                        <option value="ditolak" @selected($statusKey=='ditolak')>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1 shadow-sm px-3 border-0" style="background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); border-radius: 10px; color: #333;">
                        Update Status
                    </button>
                    <a href="{{ route('admin.laporan.chat', $laporan['id']) }}" class="btn btn-white border shadow-sm px-3" style="border-radius: 10px;" title="Buka Chat">
                        <i class="bi bi-chat-dots-fill" style="color: #FFCB05;"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.laporan.download', $laporan['id']) }}" class="text-decoration-none text-muted small hover-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Pengaduan
            </a>
            <button id="delete-pengaduan" data-url="{{ route('admin.laporan.delete', $laporan['id']) }}" class="btn btn-link text-danger text-decoration-none p-0 small">
                <i class="bi bi-trash me-1"></i> Hapus Pengaduan
            </button>
        </div>
    </div>
</div>

<style>
    .evidence-thumb {
        transition: transform 0.2s;
        display: block;
    }
    .evidence-thumb:hover {
        transform: scale(1.05);
    }
    .btn-white {
        background: white;
        color: #333;
    }
    .hover-primary:hover {
        color: #FFCB05 !important;
    }
</style>