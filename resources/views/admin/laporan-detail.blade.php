<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4"><i class="bi bi-info-circle-fill me-2"></i>Detail Laporan</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title">{{ $laporan['judul'] ?? 'Tidak Ada Judul' }}</h4>
                    <hr>
                    <p><strong>Kategori:</strong> {{ $laporan['kategori'] ?? '-' }}</p>
                    <p><strong>Nama Pelapor:</strong> {{ $laporan['nama'] ?? '-' }}</p>
                    <p><strong>Daerah:</strong> {{ $laporan['daerah'] ?? '-' }}</p>
                    <p><strong>Role:</strong> {{ $laporan['role'] ?? '-' }}</p>
                    <p><strong>Tanggal Laporan:</strong> 
                        {{ \Carbon\Carbon::parse($laporan['create_at']->get()->format('Y-m-d H:i')) }}
                    </p>
                    <p><strong>No HP:</strong> {{ $laporan['no_hp'] ?? '-' }}</p>
                    <p><strong>Isi Laporan:</strong><br> {{ $laporan['isi laporan'] ?? '-' }}</p>
                    <p><strong>Status Saat Ini:</strong> 
                        <span class="badge bg-primary">{{ ucfirst($laporan['status']) }}</span>
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.laporan.setStatus', $laporan['id']) }}" method="POST" class="mb-4">
                @csrf
                <div class="row align-items-end g-2">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Ubah Status</label>
                        <select name="status" class="form-select" id="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="baru">Baru</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Set Status
                        </button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.laporan.delete', ['id' => $laporan['id']]) }}" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');" class="mb-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-trash me-1"></i> Hapus Laporan
                </button>
            </form>

            <!-- Tombol Download PDF -->
            <a href="{{ route('admin.laporan.download', $laporan['id']) }}" class="btn btn-warning w-100 mb-3">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>

            <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Laporan
            </a>
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>
