<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Detail Laporan</h2>
    <div class="section"><span class="label">Judul:</span> {{ $laporan['judul'] ?? '-' }}</div>
    <div class="section"><span class="label">Kategori:</span> {{ $laporan['kategori'] ?? '-' }}</div>
    <div class="section"><span class="label">Nama:</span> {{ $laporan['nama'] ?? '-' }}</div>
    <div class="section"><span class="label">No HP:</span> {{ $laporan['no_hp'] ?? '-' }}</div>
    <div class="section"><span class="label">Daerah:</span> {{ $laporan['daerah'] ?? '-' }}</div>
    <div class="section"><span class="label">Role:</span> {{ $laporan['role'] ?? '-' }}</div>
    <div class="section"><span class="label">Status:</span> {{ ucfirst($laporan['status']) }}</div>
    <div class="section"><span class="label">Tanggal:</span> 
        {{ \Carbon\Carbon::parse($laporan['create_at']->get()->format('Y-m-d H:i')) }}
    </div>
    <div class="section"><span class="label">Isi Laporan:</span><br> {!! nl2br(e($laporan['isi laporan'] ?? '-')) !!}</div>
</body>
</html>
