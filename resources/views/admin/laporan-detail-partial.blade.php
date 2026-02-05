{{-- ================================
    DETAIL LAPORAN (PARTIAL / MODAL)
================================ --}}

<div class="container-fluid">

    <h5 class="mb-3 fw-bold">
        <i class="bi bi-file-text"></i> Detail Laporan
    </h5>

    @php
        $kategori = strtolower($laporan['kategori'] ?? ($laporan['case_type'] ?? ''));
        $kategoriClass = match($kategori) {
            'pernikahan anak' => 'danger',
            'kekerasan anak' => 'danger',
            'bullying' => 'warning',
            'stunting' => 'success',
            default => 'secondary'
        };
    @endphp

    <span class="badge bg-{{ $kategoriClass }} mb-3">
        <i class="bi bi-tag"></i> {{ $laporan['kategori'] ?? ($laporan['case_type'] ?? '-') }}
    </span>

    {{-- Field detail: case_type, child_age, created_date, detail_description, evidence_image, incident_city, incident_date, incident_location, phone_number, report_status, reporter_role, user_name, victim_relationship --}}
    <div class="row g-3 mb-3">

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Tipe Kasus</small>
                <div class="fw-semibold">{{ $laporan['case_type'] ?? ($laporan['kategori'] ?? '-') }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Status Laporan</small><br>
                @php
                    $statusVal = $laporan['report_status'] ?? ($laporan['status'] ?? 'baru');
                    $statusClass = [
                        'baru' => 'secondary',
                        'diproses' => 'warning',
                        'selesai' => 'success',
                        'ditolak' => 'danger'
                    ][strtolower($statusVal)] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($statusVal) }}</span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Nama Pelapor</small>
                <div>{{ $laporan['user_name'] ?? ($laporan['nama'] ?? '-') }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">No HP</small>
                <div>{{ $laporan['phone_number'] ?? ($laporan['no_hp'] ?? '-') }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Role Pelapor</small>
                <div>{{ $laporan['reporter_role'] ?? '-' }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Hubungan Korban</small>
                <div>{{ $laporan['victim_relationship'] ?? '-' }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Usia Anak</small>
                <div>{{ $laporan['child_age'] ?? '-' }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Tanggal Laporan</small>
                <div>
                    @if(!empty($laporan['created_date'] ?? $laporan['create_at'] ?? null))
                        {{ \Carbon\Carbon::parse($laporan['created_date'] ?? $laporan['create_at'])->translatedFormat('d F Y, H:i') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Kota Kejadian</small>
                <div>{{ $laporan['incident_city'] ?? ($laporan['daerah'] ?? '-') }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Lokasi Kejadian</small>
                <div>{{ $laporan['incident_location'] ?? '-' }}</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <small class="text-muted">Tanggal Kejadian</small>
                <div>
                    @if(!empty($laporan['incident_date']))
                        @php
                            try {
                                $incDate = $laporan['incident_date'] instanceof \DateTimeInterface
                                    ? \Carbon\Carbon::instance($laporan['incident_date'])
                                    : \Carbon\Carbon::parse($laporan['incident_date']);
                                echo $incDate->translatedFormat('d F Y');
                            } catch (\Throwable $e) {
                                echo $laporan['incident_date'];
                            }
                        @endphp
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Detail Description / Isi Laporan --}}
    <div class="mb-3">
        <h6 class="fw-bold">
            <i class="bi bi-chat-left-text"></i>Isi Laporan
        </h6>
        <div class="border rounded p-3 bg-light">
            {{ $laporan['detail_description'] ?? ($laporan['deskripsi_lengkap'] ?? '-') }}
        </div>
    </div>

    {{-- Evidence Image --}}
    @if(!empty($laporan['evidence_image']))
    <div class="mb-3">
        <h6 class="fw-bold">
            <i class="bi bi-image"></i> Bukti Kejadian
        </h6>
        <div class="border rounded p-3 bg-light">
            @php
                $evidenceImg = $laporan['evidence_image'];
                $evidenceUrls = is_array($evidenceImg) ? $evidenceImg : (is_string($evidenceImg) ? [$evidenceImg] : []);
            @endphp
            @foreach($evidenceUrls as $url)
                @if(!empty($url) && is_string($url))
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="d-inline-block me-2 mb-2">
                        <img src="{{ $url }}" alt="Bukti" class="img-thumbnail" style="max-height: 200px; max-width: 100%; object-fit: contain;">
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <hr>

    {{-- UPDATE STATUS --}}
    <div class="mb-4">
        <h6 class="fw-bold mb-2">
            <i class="bi bi-gear"></i> Ubah Status
        </h6>

        <form id="statusForm"
            action="{{ route('admin.laporan.setStatus', $laporan['id']) }}"
            method="POST">

            @csrf

            <div class="row g-2">
                <div class="col-md-8">
                    <select name="status" class="form-select" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="baru" @selected($laporan['status']=='baru')>Baru</option>
                        <option value="diproses" @selected($laporan['status']=='diproses')>Diproses</option>
                        <option value="selesai" @selected($laporan['status']=='selesai')>Selesai</option>
                        <option value="ditolak" @selected($laporan['status']=='ditolak')>Ditolak</option>
                    </select>
                </div>

                <div class="col-md-4 d-grid">
                    <button class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update
                    </button>
                </div>
            </div>
        </form>
    </div>

    <hr>

    {{-- AKSI --}}
    <div class="d-flex justify-content-end gap-2 flex-wrap">

        {{-- CHAT --}}
        <a href="{{ route('admin.laporan.chat', $laporan['id']) }}"
            class="btn btn-outline-primary btn-sm">
            <i class="bi bi-chat-dots"></i> Chat
        </a>

        {{-- DOWNLOAD --}}
        <a href="{{ route('admin.laporan.download', $laporan['id']) }}"
            class="btn btn-outline-warning btn-sm">
            <i class="bi bi-download"></i> Download
        </a>

        {{-- DELETE --}}
        <button
            id="delete-laporan"
            data-url="{{ route('admin.laporan.delete', $laporan['id']) }}"
            class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i> Hapus
        </button>

    </div>

</div>
