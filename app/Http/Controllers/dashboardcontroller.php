<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class DashboardController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        // Inisialisasi koneksi Firebase Firestore
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->firestore = $factory->createFirestore()->database();
    }

    public function index(Request $request)
    {
        // Cek apakah admin sudah login
        if (!Session::has('admin')) {
            return redirect()->route('admin.login');
        }

        // Ambil data dari koleksi Firestore
        $usersSnapshots    = $this->firestore->collection('users')->documents();
        $articlesSnapshots = $this->firestore->collection('articles')->documents();

        // Dukung 2 struktur: flat report/{reportId} atau nested report/{kategoriDoc}/{userId}/{reportId}
        $kategoriMap = [
            'kekerasan_anak', 'bullying', 'pernikahan_anak', 'stunting'
        ];

        $totalUsers    = $usersSnapshots->size();
        $totalLaporan  = 0;
        $totalArticles = $articlesSnapshots->size();
        $laporanSelesai = 0;
        $laporanDiproses = 0;
        $laporanBaru = 0;
        $laporanDitolak = 0;
        $kategoriCount = [];
        $daerahCount = [];
        $kategoriPerDaerah = [];
        $trenPerBulan = [];
        $trenPerHari = [];
        $laporanTerbaruCollect = [];

        $processReportDoc = function ($doc) use (
            &$totalLaporan, &$laporanSelesai, &$laporanDiproses, &$laporanBaru, &$laporanDitolak,
            &$kategoriCount, &$daerahCount, &$kategoriPerDaerah, &$trenPerBulan, &$trenPerHari, &$laporanTerbaruCollect
        ) {
            if (!$doc->exists()) return;
            $totalLaporan++;
            $data = $doc->data();
            if (!isset($data['report_status']) && !isset($data['case_type']) && !isset($data['user_name'])) {
                return; // bukan dokumen laporan
            }
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
            $dateSort = $dateObj ? $dateObj->format('Y-m-d H:i:s') : '';
            $laporanTerbaruCollect[] = [
                'id' => $doc->id(),
                'create_at' => $dateObj,
                'sort_at' => $dateSort,
                'kategori' => $kategoriDisplay,
                'daerah' => $daerah,
                'status' => $status,
            ];
        };

        // Coba struktur flat: report/{reportId}
        try {
            $reportSnapshot = $this->firestore->collection('report')->documents();
            foreach ($reportSnapshot as $doc) {
                $processReportDoc($doc);
            }
        } catch (\Throwable $e) {}

        // Jika flat tidak ada data, coba nested: report/{kategoriDoc}/{userId}/{reportId}
        if ($totalLaporan === 0) {
            foreach ($kategoriMap as $kategoriKey) {
                try {
                    $kategoriDocRef = $this->firestore->collection('report')->document($kategoriKey);
                    foreach ($kategoriDocRef->collections() as $userCollection) {
                        foreach ($userCollection->documents() as $doc) {
                            $processReportDoc($doc);
                        }
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

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

        // Daftar tahun untuk dropdown (3 tahun lalu s/d tahun ini)
        $tahunList = range(now()->year - 3, now()->year);
        $tahunList = array_reverse($tahunList);

        // Ambil 4 kategori terbanyak
        arsort($kategoriCount);
        $topKategori = array_slice($kategoriCount, 0, 4, true);

        // Ambil 4 daerah dengan jumlah laporan terbanyak
        arsort($daerahCount);
        $topDaerah = array_slice($daerahCount, 0, 4, true);

        // Ambil kategori terbanyak di tiap top daerah
        $topDaerahKategori = [];
        foreach ($topDaerah as $daerah => $jumlah) {
            if (isset($kategoriPerDaerah[$daerah])) {
                arsort($kategoriPerDaerah[$daerah]);
                $kategoriTerbanyak = array_key_first($kategoriPerDaerah[$daerah]);
                $topDaerahKategori[$daerah] = [
                    'total' => $jumlah,
                    'kategori_terbanyak' => $kategoriTerbanyak,
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
        ]);
    }
}
