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
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a73e8;
            margin-bottom: 1.2rem;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            color: #2c3e50;
            border-left: 4px solid #1a73e8;
            padding-left: 10px;
        }
        .label-key {
            font-weight: 500;
            color: #6c757d;
        }
        .detail-row {
            margin-bottom: 0.8rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-value {
            font-weight: 500;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
            border-radius: 30px;
            letter-spacing: 0.3px;
        }
        .laporan-content {
            background-color: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            border-left: 4px solid #6c757d;
        }
        .btn {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-action {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
        }
        .btn i {
            margin-right: 0.5rem;
        }
        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        .page-title {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        .page-title i {
            font-size: 1.8rem;
            margin-right: 0.8rem;
            color: #1a73e8;
        }
        .form-select, .form-control {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-select:focus, .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.15);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .back-button {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            background-color: #f1f8ff;
            border: 2px solid #1a73e8;
            border-radius: 8px;
            color: #1a73e8;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .back-button:hover {
            background-color: #1a73e8;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .divider {
            width: 100%;
            height: 1px;
            background-color: #dee2e6;
            margin: 2rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <!-- Header dengan tombol kembali -->
                <div class="page-header">
                    <h3 class="page-title mb-0">
                        <i class="bi bi-file-text"></i>
                        <span>Detail Laporan</span>
                    </h3>
                    <a href="{{ route('admin.laporan') }}" class="back-button">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <!-- Card Laporan & Aksi -->
                <div class="card">
                    <div class="card-body">
                        <!-- Informasi Laporan -->
                        <h4 class="card-title">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            {{ $laporan['judul'] ?? 'Tidak Ada Judul' }}
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <div class="label-key mb-1">Kategori</div>
                                    <div class="detail-value">{{ $laporan['kategori'] ?? '-' }}</div>
                                </div>
                                
                                <div class="detail-row">
                                    <div class="label-key mb-1">Nama Pelapor</div>
                                    <div class="detail-value">{{ $laporan['nama'] ?? '-' }}</div>
                                </div>
                                
                                <div class="detail-row">
                                    <div class="label-key mb-1">Daerah</div>
                                    <div class="detail-value">{{ $laporan['daerah'] ?? '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <div class="label-key mb-1">Role</div>
                                    <div class="detail-value">{{ $laporan['role'] ?? '-' }}</div>
                                </div>
                                
                                <div class="detail-row">
                                    <div class="label-key mb-1">Tanggal Laporan</div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::parse($laporan['create_at'] ?? now())->translatedFormat('d F Y, H:i') }}
                                    </div>
                                </div>
                                
                                <div class="detail-row">
                                    <div class="label-key mb-1">No HP</div>
                                    <div class="detail-value">{{ $laporan['no_hp'] ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">Isi Laporan</div>
                        <div class="laporan-content mb-4">
                            {{ $laporan['isi laporan'] ?? '-' }}
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <div class="label-key mb-1">Status Saat Ini</div>
                                @php
                                    $statusClass = [
                                        'baru' => 'bg-secondary',
                                        'diproses' => 'bg-warning',
                                        'selesai' => 'bg-success',
                                        'ditolak' => 'bg-danger'
                                    ][$laporan['status']] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }} badge-status">
                                    {{ ucfirst($laporan['status']) }}
                                </span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="divider"></div>
                        
                        <!-- Bagian Aksi -->
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-gear me-2"></i>
                            Kelola Laporan
                        </h5>

                        <!-- Form Ubah Status -->
                        <form action="{{ route('admin.laporan.setStatus', $laporan['id']) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="status" class="form-label fw-bold">Ubah Status</label>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="baru">Baru</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 btn-action">
                                        <i class="bi bi-check-circle"></i> Perbarui Status
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Baris Tombol Aksi -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('admin.laporan.download', $laporan['id']) }}" class="btn btn-warning w-100 btn-action">
                                    <i class="bi bi-download"></i> Download PDF
                                </a>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.laporan.delete', $laporan['id']) }}" class="m-0" id="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger w-100 btn-action" id="delete-button">
                                        <i class="bi bi-trash"></i> Hapus Laporan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Untuk menampilkan konfirmasi hapus dengan SweetAlert
        document.getElementById('delete-button').addEventListener('click', function() {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus laporan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    </script>
</body>
</html>