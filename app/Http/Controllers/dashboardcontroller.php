<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $firestore;

    /**
     * Urutan tetap kabupaten/kota Kalimantan Barat untuk grafik dashboard.
     */
    protected static function kalbarDaerahList(): array
    {
        return [
            'Kabupaten Bengkayang',
            'Kabupaten Kapuas Hulu',
            'Kabupaten Kayong Utara',
            'Kabupaten Ketapang',
            'Kabupaten Kubu Raya',
            'Kabupaten Landak',
            'Kabupaten Melawi',
            'Kabupaten Mempawah',
            'Kabupaten Sambas',
            'Kabupaten Sanggau',
            'Kabupaten Sekadau',
            'Kabupaten Sintang',
            'Kota Pontianak',
            'Kota Singkawang',
        ];
    }

    /**
     * Cocokkan teks kota/daerah dari laporan ke salah satu kab/kota Kalbar, atau null.
     */
    protected function normalizeToKalbarDaerah(string $raw): ?string
    {
        $norm = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $raw)), 'UTF-8');
        if ($norm === '' || $norm === 'tidak diketahui' || $norm === '-') {
            return null;
        }

        $aliases = [
            'kabupaten bengkayang' => 'Kabupaten Bengkayang',
            'bengkayang' => 'Kabupaten Bengkayang',
            'kabupaten kapuas hulu' => 'Kabupaten Kapuas Hulu',
            'kapuas hulu' => 'Kabupaten Kapuas Hulu',
            'kabupaten kayong utara' => 'Kabupaten Kayong Utara',
            'kayong utara' => 'Kabupaten Kayong Utara',
            'kabupaten ketapang' => 'Kabupaten Ketapang',
            'ketapang' => 'Kabupaten Ketapang',
            'kabupaten kubu raya' => 'Kabupaten Kubu Raya',
            'kubu raya' => 'Kabupaten Kubu Raya',
            'kabupaten landak' => 'Kabupaten Landak',
            'landak' => 'Kabupaten Landak',
            'kabupaten melawi' => 'Kabupaten Melawi',
            'melawi' => 'Kabupaten Melawi',
            'kabupaten mempawah' => 'Kabupaten Mempawah',
            'mempawah' => 'Kabupaten Mempawah',
            'kabupaten sambas' => 'Kabupaten Sambas',
            'sambas' => 'Kabupaten Sambas',
            'kabupaten sanggau' => 'Kabupaten Sanggau',
            'sanggau' => 'Kabupaten Sanggau',
            'kabupaten sekadau' => 'Kabupaten Sekadau',
            'sekadau' => 'Kabupaten Sekadau',
            'kabupaten sintang' => 'Kabupaten Sintang',
            'sintang' => 'Kabupaten Sintang',
            'kota pontianak' => 'Kota Pontianak',
            'pontianak' => 'Kota Pontianak',
            'kota singkawang' => 'Kota Singkawang',
            'singkawang' => 'Kota Singkawang',
        ];

        if (isset($aliases[$norm])) {
            return $aliases[$norm];
        }

        foreach (self::kalbarDaerahList() as $canonical) {
            $c = mb_strtolower($canonical, 'UTF-8');
            if ($norm === $c) {
                return $canonical;
            }
            $short = preg_replace('/^(kabupaten|kota)\s+/u', '', $c);
            if ($short !== '' && $norm === $short) {
                return $canonical;
            }
        }

        return null;
    }

    public function __construct()
    {
        // Menggunakan singleton dari FirebaseServiceProvider
        $this->firestore = app('firebase.firestore');
    }

    public function index(Request $request)
    {
        // Cek apakah admin sudah login
        if (!Session::has('admin')) {
            return redirect()->route('admin.login');
        }

        // Gunakan Cache untuk menyimpan data dasar dashboard selama 5 menit (300 detik)
        $cachedData = Cache::remember('dashboard_base_data', 300, function () {
            $kategoriMap = ['kekerasan_anak', 'bullying', 'pernikahan_anak', 'stunting'];
            $usersSnapshots    = $this->firestore->collection('users')->documents();
            $articlesSnapshots = $this->firestore->collection('articles')->documents();

            $totalUsers    = $usersSnapshots->size();
            $totalArticles = $articlesSnapshots->size();
            
            $totalLaporan  = 0;
            $laporanSelesai = 0;
            $laporanDiproses = 0;
            $laporanBaru = 0;
            $laporanDitolak = 0;
            $kategoriCount = [];
            $daerahCount = [];
            $kategoriPerDaerah = [];
            $trenPerBulan = [];
            $trenPerHari = [];
            $usiaCount = ['Bayi (0-11 bulan)' => 0, 'Balita (1-5 tahun)' => 0, 'Prasekolah (5-6 tahun)' => 0, 'Anak-anak (5-11 tahun)' => 0, 'Remaja (10-18 tahun)' => 0];
            $laporanTerbaruCollect = [];

            $processReportDoc = function ($doc) use (
                &$totalLaporan, &$laporanSelesai, &$laporanDiproses, &$laporanBaru, &$laporanDitolak,
                &$kategoriCount, &$daerahCount, &$kategoriPerDaerah, &$trenPerBulan, &$trenPerHari, &$laporanTerbaruCollect,
                &$usiaCount
            ) {
                if (!$doc->exists()) return;
                $data = $doc->data();
                if (!isset($data['report_status']) && !isset($data['case_type']) && !isset($data['user_name'])) {
                    return; 
                }
                
                $totalLaporan++;
                $status = strtolower($data['report_status'] ?? ($data['status'] ?? 'baru'));
                switch ($status) {
                    case 'selesai': $laporanSelesai++; break;
                    case 'diproses': $laporanDiproses++; break;
                    case 'ditolak': $laporanDitolak++; break;
                    default: $laporanBaru++; break;
                }

                $kategoriDisplay = $data['case_type'] ?? 'Tidak diketahui';
                $daerah = $data['incident_city'] ?? ($data['incident_location'] ?? 'Tidak diketahui');
                $kategoriCount[$kategoriDisplay] = ($kategoriCount[$kategoriDisplay] ?? 0) + 1;
                $daerahCount[$daerah] = ($daerahCount[$daerah] ?? 0) + 1;
                
                $kategoriPerDaerah[$daerah] = $kategoriPerDaerah[$daerah] ?? [];
                $kategoriPerDaerah[$daerah][$kategoriDisplay] = ($kategoriPerDaerah[$daerah][$kategoriDisplay] ?? 0) + 1;

                $tz = config('app.timezone', 'Asia/Jakarta');
                $dateObj = null;
                $created = $data['created_date'] ?? null;
                
                if ($created !== null && $created !== '') {
                    try {
                        if (is_numeric($created)) {
                            $dateObj = Carbon::createFromTimestampMs((int) $created)->setTimezone($tz);
                        } elseif ($created instanceof \DateTimeInterface) {
                            $dateObj = Carbon::instance($created)->setTimezone($tz);
                        } elseif (is_array($created) && isset($created['seconds'])) {
                            $dateObj = Carbon::createFromTimestamp($created['seconds'])->setTimezone($tz);
                        } else {
                            $dateObj = Carbon::parse($created)->setTimezone($tz);
                        }
                    } catch (\Throwable $e) {}
                }

                if ($dateObj !== null) {
                    $trenPerBulan[$dateObj->format('Y-m')] = ($trenPerBulan[$dateObj->format('Y-m')] ?? 0) + 1;
                    $trenPerHari[$dateObj->format('Y-m-d')] = ($trenPerHari[$dateObj->format('Y-m-d')] ?? 0) + 1;
                }

                $laporanTerbaruCollect[] = [
                    'id' => $doc->id(),
                    'sort_at' => $dateObj ? $dateObj->format('Y-m-d H:i:s') : '',
                    'created_date' => $data['created_date'] ?? null,
                    'create_at' => $dateObj ? $dateObj->format('Y-m-d H:i:s') : '',
                    'kategori' => $kategoriDisplay,
                    'daerah' => $daerah,
                    'status' => $status,
                ];

                $childAge = $data['child_age'] ?? null;
                if ($childAge !== null && $childAge !== '') {
                    $ageStr = strtolower(trim((string) $childAge));
                    preg_match('/(\d+)/', $ageStr, $matches);
                    $ageNum = isset($matches[1]) ? (int) $matches[1] : null;

                    if ($ageNum !== null) {
                        $isBulan = (str_contains($ageStr, 'bulan') || str_contains($ageStr, 'bln') || str_contains($ageStr, 'month'));
                        if ($isBulan) {
                            if ($ageNum >= 0 && $ageNum <= 11) $usiaCount['Bayi (0-11 bulan)']++;
                            elseif ($ageNum >= 12 && $ageNum <= 60) $usiaCount['Balita (1-5 tahun)']++;
                            elseif ($ageNum > 60 && $ageNum <= 72) $usiaCount['Prasekolah (5-6 tahun)']++;
                            elseif ($ageNum > 72 && $ageNum <= 132) $usiaCount['Anak-anak (5-11 tahun)']++;
                            elseif ($ageNum > 132 && $ageNum <= 216) $usiaCount['Remaja (10-18 tahun)']++;
                        } else {
                            if ($ageNum == 0) $usiaCount['Bayi (0-11 bulan)']++;
                            elseif ($ageNum >= 1 && $ageNum <= 4) $usiaCount['Balita (1-5 tahun)']++;
                            elseif ($ageNum >= 5 && $ageNum <= 6) $usiaCount['Prasekolah (5-6 tahun)']++;
                            elseif ($ageNum >= 7 && $ageNum <= 11) $usiaCount['Anak-anak (5-11 tahun)']++;
                            elseif ($ageNum >= 12 && $ageNum <= 18) $usiaCount['Remaja (10-18 tahun)']++;
                        }
                    }
                }
            };

            // Fetch Reports (Fokus hanya pada struktur FLAT)
            try {
                $reportSnapshot = $this->firestore->collection('report')->documents();
                foreach ($reportSnapshot as $doc) { $processReportDoc($doc); }
            } catch (\Throwable $e) {
                \Log::error('Dashboard Report Fetch Error: ' . $e->getMessage());
            }

            return [
                'totalUsers' => $totalUsers,
                'totalArticles' => $totalArticles,
                'totalLaporan' => $totalLaporan,
                'laporanSelesai' => $laporanSelesai,
                'laporanDiproses' => $laporanDiproses,
                'laporanBaru' => $laporanBaru,
                'laporanDitolak' => $laporanDitolak,
                'kategoriCount' => $kategoriCount,
                'daerahCount' => $daerahCount,
                'kategoriPerDaerah' => $kategoriPerDaerah,
                'trenPerBulan' => $trenPerBulan,
                'trenPerHari' => $trenPerHari,
                'usiaCount' => $usiaCount,
                'laporanTerbaruCollect' => $laporanTerbaruCollect,
            ];
        });

        // Ekstrak data dari cache
        $totalUsers = $cachedData['totalUsers'];
        $totalArticles = $cachedData['totalArticles'];
        $totalLaporan = $cachedData['totalLaporan'];
        $laporanSelesai = $cachedData['laporanSelesai'];
        $laporanDiproses = $cachedData['laporanDiproses'];
        $laporanBaru = $cachedData['laporanBaru'];
        $laporanDitolak = $cachedData['laporanDitolak'];
        $kategoriCount = $cachedData['kategoriCount'];
        $daerahCount = $cachedData['daerahCount'];
        $kategoriPerDaerah = $cachedData['kategoriPerDaerah'];
        $trenPerBulan = $cachedData['trenPerBulan'];
        $trenPerHari = $cachedData['trenPerHari'];
        $usiaCount = $cachedData['usiaCount'];
        $laporanTerbaruCollect = $cachedData['laporanTerbaruCollect'];

        // Urutkan laporan terbaru (tanggal terbaru dulu), ambil 10
        usort($laporanTerbaruCollect, function ($a, $b) {
            return strcmp($b['sort_at'] ?? '', $a['sort_at'] ?? '');
        });
        $laporanTerbaru = array_slice($laporanTerbaruCollect, 0, 10);

        // Filter tren: tahun & bulan (opsional) dari request
        $filterTahun = $request->input('tahun');
        $filterBulan = $request->input('bulan');
        $trenLaporanLabels = [];
        $trenLaporanData = [];
        $trenPeriodLabel = '12 bulan terakhir';

        if ($filterTahun !== null && $filterTahun !== '') {
            $tahun = (int) $filterTahun;
            $bulan = $filterBulan !== null && $filterBulan !== '' ? (int) $filterBulan : null;

            if ($bulan >= 1 && $bulan <= 12) {
                // Tren per hari dalam satu bulan
                $start = Carbon::createFromDate($tahun, $bulan, 1);
                $end = $start->copy()->endOfMonth();
                $cursor = $start->copy();
                while ($cursor->lte($end)) {
                    $key = $cursor->format('Y-m-d');
                    $trenLaporanLabels[] = $cursor->locale('id')->translatedFormat('d M');
                    $trenLaporanData[] = $trenPerHari[$key] ?? 0;
                    $cursor->addDay();
                }
                $trenPeriodLabel = $start->locale('id')->translatedFormat('F Y');
            } else {
                // Tren per bulan dalam satu tahun
                for ($m = 1; $m <= 12; $m++) {
                    $key = sprintf('%04d-%02d', $tahun, $m);
                    $trenLaporanLabels[] = Carbon::createFromDate($tahun, $m, 1)->locale('id')->translatedFormat('M Y');
                    $trenLaporanData[] = $trenPerBulan[$key] ?? 0;
                }
                $trenPeriodLabel = (string) $tahun;
            }
        } else {
            // Default: 12 bulan terakhir
            $start = now()->subMonths(11)->startOfMonth();
            $end = now()->endOfMonth();
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');
                $trenLaporanLabels[] = $cursor->locale('id')->translatedFormat('M Y');
                $trenLaporanData[] = $trenPerBulan[$key] ?? 0;
                $cursor->addMonth();
            }
        }

        // Daftar tahun untuk dropdown
        $tahunList = range(now()->year - 3, now()->year);
        $tahunList = array_reverse($tahunList);

        // Ambil 4 kategori terbanyak
        arsort($kategoriCount);
        $topKategori = array_slice($kategoriCount, 0, 4, true);

        // Ambil 4 daerah terbanyak
        arsort($daerahCount);
        $topDaerah = array_slice($daerahCount, 0, 4, true);

        // Bar chart Kalbar
        $kalbarList = self::kalbarDaerahList();
        $kalbarBarCounts = array_fill_keys($kalbarList, 0);
        foreach ($daerahCount as $raw => $count) {
            $canonical = $this->normalizeToKalbarDaerah((string) $raw);
            if ($canonical !== null) { $kalbarBarCounts[$canonical] += $count; }
        }
        $daerahBarLabels = $kalbarList;
        $daerahBarData = array_values($kalbarBarCounts);

        // Data chart usia
        $usiaBarLabels = array_keys($usiaCount);
        $usiaBarData = array_values($usiaCount);

        // Ambil kategori terbanyak di tiap top daerah
        $topDaerahKategori = [];
        foreach ($topDaerah as $daerah => $jumlah) {
            if (isset($kategoriPerDaerah[$daerah])) {
                arsort($kategoriPerDaerah[$daerah]);
                $topDaerahKategori[$daerah] = [
                    'total' => $jumlah,
                    'kategori_terbanyak' => array_key_first($kategoriPerDaerah[$daerah]),
                ];
            }
        }

        // Kirim ke view
        return view('admin.dashboard', [
            'totalUsers'         => $totalUsers,
            'totalLaporan'       => $totalLaporan,
            'totalArticles'      => $totalArticles,
            'totalSelesai'       => $laporanSelesai,
            'totalDiproses'      => $laporanDiproses,
            'totalBaru'          => $laporanBaru,
            'totalDitolak'       => $laporanDitolak,
            'topKategori'        => $topKategori,
            'topDaerahKategori'   => $topDaerahKategori,
            'trenLaporanLabels'  => $trenLaporanLabels,
            'trenLaporanData'    => $trenLaporanData,
            'trenPeriodLabel'    => $trenPeriodLabel,
            'filterTahun'        => $filterTahun,
            'filterBulan'        => $filterBulan,
            'tahunList'          => $tahunList,
            'laporanTerbaru'     => $laporanTerbaru,
            'daerahBarLabels'    => $daerahBarLabels,
            'daerahBarData'      => $daerahBarData,
            'usiaBarLabels'      => $usiaBarLabels,
            'usiaBarData'        => $usiaBarData,
        ]);
    }

    public function refresh()
    {
        Cache::forget('dashboard_base_data');
        return redirect()->route('admin.dashboard')->with('success', 'Data dashboard berhasil diperbarui.');
    }
}
