<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Gaya Kustom -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        
        /* Gaya Card */
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Gaya Section Title */
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            color: #2c3e50;
            border-left: 4px solid #1a73e8;
            padding-left: 10px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Gaya Label dan Detail */
        .label-key {
            font-weight: 500;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .detail-row {
            margin-bottom: 0.8rem;
            padding: 0.8rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .detail-row:hover {
            background-color: #f1f8ff;
        }
        
        .detail-value {
            font-weight: 500;
            color: #2c3e50;
            margin-top: 0.3rem;
        }
        
        /* Gaya Badge */
        .badge-status {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
            border-radius: 30px;
            letter-spacing: 0.3px;
        }
        
        /* Gaya Konten Laporan */
        .laporan-content {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #1a73e8;
            margin: 1rem 0;
            line-height: 1.6;
        }
        
        /* Gaya Tombol */
        .btn {
            border-radius: 8px;
            padding: 0.8rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-action {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            gap: 0.5rem;
        }
        
        .btn i {
            font-size: 1.1rem;
        }
        
        /* Gaya Alert */
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        /* Gaya Page Title */
        .page-title {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: #2c3e50;
            font-size: 1.8rem;
        }
        
        .page-title i {
            font-size: 2rem;
            margin-right: 0.8rem;
            color: #1a73e8;
        }
        
        /* Gaya Form */
        .form-select, .form-control {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.15);
        }
        
        /* Gaya Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Gaya Back Button */
        .back-button {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            background-color: #f1f8ff;
            border: 2px solid #1a73e8;
            border-radius: 8px;
            color: #1a73e8;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            gap: 0.5rem;
        }
        
        .back-button:hover {
            background-color: #1a73e8;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Gaya Divider */
        .divider {
            width: 100%;
            height: 1px;
            background-color: #dee2e6;
            margin: 2rem 0;
        }
        
        /* Gaya Section */
        .status-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 1.5rem 0;
        }
        
        .action-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1.5rem;
        }
        
        /* Gaya Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        /* Gaya Category Badge */
        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 500;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .category-badge.pernikahan { 
            background-color: #ffeef2; 
            color: #b12c2c; 
        }
        
        .category-badge.kekerasan { 
            background-color: #fff0f0; 
            color: #c0392b; 
        }
        
        .category-badge.bullying { 
            background-color: #fff3cd; 
            color: #8a6d3b; 
        }
        
        .category-badge.stunting { 
            background-color: #eafaf2; 
            color: #2e7d32; 
        }
    </style>
</head>

<body>
    <!-- Konten Utama -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header dengan tombol kembali -->
                <div class="page-header">
                    <h3 class="page-title mb-0">
                        <i class="bi bi-file-text"></i>
                        <span>Detail Laporan</span>
                    </h3>
                    <a href="{{ route('admin.laporan') }}" class="back-button">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Daftar</span>
                    </a>
                </div>

                <!-- Pesan Flash -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <!-- Card Laporan & Aksi -->
                <div class="card">
                    <div class="card-body">
                        <!-- Header Laporan -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h4 class="card-title">
                                <i class="bi bi-file-earmark-text"></i>
                                {{ $laporan['judul'] ?? 'Tidak Ada Judul' }}
                            </h4>
                            @php
                                $kategori = strtolower($laporan['kategori'] ?? '');
                                $kategoriClass = match($kategori) {
                                    'pernikahan anak' => 'pernikahan',
                                    'kekerasan anak' => 'kekerasan',
                                    'bullying' => 'bullying',
                                    'stunting' => 'stunting',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="category-badge {{ $kategoriClass }}">
                                <i class="bi bi-tag"></i>
                                {{ $laporan['kategori'] ?? '-' }}
                            </span>
                        </div>
                        
                        <!-- Informasi Detail -->
                        <div class="info-grid">
                            <div class="detail-row">
                                <div class="label-key">
                                    <i class="bi bi-person me-2"></i>Nama Pelapor
                                </div>
                                <div class="detail-value">{{ $laporan['nama'] ?? '-' }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="label-key">
                                    <i class="bi bi-geo-alt me-2"></i>Daerah
                                </div>
                                <div class="detail-value">{{ $laporan['daerah'] ?? '-' }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="label-key">
                                    <i class="bi bi-person-badge me-2"></i>Role
                                </div>
                                <div class="detail-value">{{ $laporan['role'] ?? '-' }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="label-key">
                                    <i class="bi bi-calendar3 me-2"></i>Tanggal Laporan
                                </div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($laporan['create_at'] ?? now())->translatedFormat('d F Y, H:i') }}
                                </div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="label-key">
                                    <i class="bi bi-telephone me-2"></i>No HP
                                </div>
                                <div class="detail-value">{{ $laporan['no_hp'] ?? ($laporan['no_hp'] ?? '-') }}</div>
                            </div>
                        </div>

                        <!-- Isi Laporan -->
                        <div class="section-title">
                            <i class="bi bi-chat-square-text"></i>
                            Isi Laporan
                        </div>
                        <div class="laporan-content">
                            {{ $laporan['deskripsi_lengkap'] ?? ($laporan['isi laporan'] ?? '-') }}
                        </div>

                        

                        <!-- Bagian Status -->
                        <div class="status-section">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="label-key mb-2">Status Saat Ini</div>
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
                                <div class="text-end">
                                    <div class="label-key mb-2">ID Laporan</div>
                                    <div class="detail-value">#{{ $laporan['id'] }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Aksi -->
                        <div class="action-section">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-gear me-2"></i>
                                Kelola Laporan
                            </h5>

                            <!-- Form Ubah Status -->
                            <form action="{{ route('admin.laporan.setStatus', $laporan['id']) }}" method="POST" class="mb-4" id="statusForm">
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
                                            <i class="bi bi-check-circle"></i>
                                            <span>Perbarui Status</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Tombol Aksi -->
                            <div class="row g-3">
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.laporan.chat', $laporan['id']) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                    <a href="{{ route('admin.laporan.download', $laporan['id']) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.laporan.delete', $laporan['id']) }}" class="m-0" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" id="delete-button">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Kustom -->
    <script>
        // Konfirmasi hapus laporan dengan SweetAlert
        document.getElementById('delete-button').addEventListener('click', function() {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus laporan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#28a745',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-secondary',
                    cancelButton: 'btn btn-success'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });

        // Handler update status dengan AJAX
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update badge status
                    const statusBadge = document.querySelector('.badge-status');
                    const statusClass = {
                        'baru': 'bg-secondary',
                        'diproses': 'bg-warning',
                        'selesai': 'bg-success',
                        'ditolak': 'bg-danger'
                    }[formData.get('status')] || 'bg-secondary';
                    
                    statusBadge.className = `badge ${statusClass} badge-status`;
                    statusBadge.textContent = formData.get('status').charAt(0).toUpperCase() + formData.get('status').slice(1);

                    // Tampilkan pesan sukses
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Update chart dashboard jika ada
                    const statusChart = window.statusChart;
                    if (statusChart) {
                        statusChart.data.datasets[0].data = [data.data.totalSelesai, data.data.totalDiproses];
                        statusChart.update();
                    }
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memperbarui status.',
                    icon: 'error'
                });
            });
        });
    </script>
</body>
</html>