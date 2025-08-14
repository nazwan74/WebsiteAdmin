<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan PDF - GESA</title>
    
    <!-- Gaya Kustom untuk PDF -->
    <style>
        /* Gaya Dasar */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
            background-color: #fff;
        }
        
        /* Gaya Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #FFCB05;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #FFCB05;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* Gaya Informasi Laporan */
        .laporan-info {
            margin-bottom: 25px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section h3 {
            color: #FFCB05;
            font-size: 16px;
            margin-bottom: 10px;
            border-left: 4px solid #FFCB05;
            padding-left: 10px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        /* Gaya Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-baru {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .status-diproses {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Gaya Kategori Badge */
        .kategori-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .kategori-pernikahan {
            background-color: #ffeef2;
            color: #b12c2c;
        }
        
        .kategori-kekerasan {
            background-color: #fff0f0;
            color: #c0392b;
        }
        
        .kategori-bullying {
            background-color: #fff9e6;
            color: #8a6d3b;
        }
        
        .kategori-stunting {
            background-color: #eafaf2;
            color: #2e7d32;
        }
        
        /* Gaya Isi Laporan */
        .isi-laporan {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #FFCB05;
            margin-top: 10px;
        }
        
        .isi-laporan p {
            margin: 0;
            line-height: 1.6;
        }
        
        /* Gaya Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        /* Gaya Tabel Responsif */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .info-table .label {
            font-weight: bold;
            color: #555;
            width: 30%;
        }
        
        .info-table .value {
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Header Dokumen -->
    <div class="header">
        <h1>DETAIL LAPORAN GESA</h1>
        <div class="subtitle">Sistem Pelaporan GESA (Gerakan Sayang Anak)</div>
    </div>

    <!-- Informasi Laporan -->
    <div class="laporan-info">
        <!-- Informasi Umum -->
        <div class="info-section">
            <h3>Informasi Umum</h3>
            <table class="info-table">
                <tr>
                    <td class="label">ID Laporan:</td>
                    <td class="value">#{{ $laporan['id'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Judul:</td>
                    <td class="value">{{ $laporan['judul'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori:</td>
                    <td class="value">
                        @php
                            $kategori = strtolower($laporan['kategori'] ?? '');
                            $kategoriClass = match($kategori) {
                                'pernikahan dini' => 'kategori-pernikahan',
                                'kekerasan anak' => 'kategori-kekerasan',
                                'bullying' => 'kategori-bullying',
                                'stunting' => 'kategori-stunting',
                                default => 'kategori-badge'
                            };
                        @endphp
                        <span class="kategori-badge {{ $kategoriClass }}">
                            {{ $laporan['kategori'] ?? '-' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td class="value">
                        @php
                            $status = strtolower($laporan['status'] ?? 'baru');
                            $statusClass = match($status) {
                                'baru' => 'status-baru',
                                'diproses' => 'status-diproses',
                                'selesai' => 'status-selesai',
                                'ditolak' => 'status-ditolak',
                                default => 'status-baru'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($laporan['status'] ?? 'baru') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Tanggal Laporan:</td>
                    <td class="value">
                        {{ \Carbon\Carbon::parse($laporan['create_at'] ?? now())->translatedFormat('d F Y, H:i') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Informasi Pelapor -->
        <div class="info-section">
            <h3>Informasi Pelapor</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Nama:</td>
                    <td class="value">{{ $laporan['nama'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No HP:</td>
                    <td class="value">{{ $laporan['no_hp'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Daerah:</td>
                    <td class="value">{{ $laporan['daerah'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Role:</td>
                    <td class="value">{{ $laporan['role'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Isi Laporan -->
        <div class="info-section">
            <h3>Isi Laporan</h3>
            <div class="isi-laporan">
                {!! nl2br(e($laporan['isi laporan'] ?? 'Tidak ada isi laporan')) !!}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem GESA</p>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>
</body>
</html>
