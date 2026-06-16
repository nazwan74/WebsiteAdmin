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

    /**
     * Tipe kasus pengaduan (8 kategori) untuk filter dan grafik dashboard.
     */
    protected static function tipeKasusList(): array
    {
        return [
            'Eksploitasi Anak',
            'Kekerasan Fisik',
            'Kekerasan Psikis',
            'Kekerasan Seksual',
            'Penelantaran Anak',
            'Perdagangan Anak',
            'Perundungan',
            'Yang lain',
        ];
    }

    /**
     * Warna grafik per tipe kasus pengaduan [background, hover].
     */
    protected static function tipeKasusChartColors(): array
    {
        return [
            'Eksploitasi Anak'   => ['#7c3aed', '#6d28d9'],
            'Kekerasan Fisik'    => ['#dc2626', '#b91c1c'],
            'Kekerasan Psikis'   => ['#f97316', '#ea580c'],
            'Kekerasan Seksual'  => ['#be123c', '#9f1239'],
            'Penelantaran Anak'  => ['#0ea5e9', '#0284c7'],
            'Perdagangan Anak'   => ['#6366f1', '#4f46e5'],
            'Perundungan'        => ['#E6B800', '#ca8a04'],
            'Yang lain'          => ['#94a3b8', '#64748b'],
        ];
    }

    /**
     * Normalisasi teks tipe kasus dari laporan ke label kanonik pengaduan.
     */
    public static function normalizeTipeKasus(string $raw): string
    {
        $norm = mb_strtolower(trim(preg_replace('/[\s_]+/u', ' ', $raw)), 'UTF-8');
        if ($norm === '' || $norm === 'tidak diketahui' || $norm === '-') {
            return 'Yang lain';
        }

        $aliases = [
            'eksploitasi anak' => 'Eksploitasi Anak',
            'kekerasan fisik' => 'Kekerasan Fisik',
            'kekerasan psikis' => 'Kekerasan Psikis',
            'kekerasan psikologis' => 'Kekerasan Psikis',
            'kekerasan seksual' => 'Kekerasan Seksual',
            'penelantaran anak' => 'Penelantaran Anak',
            'perdagangan anak' => 'Perdagangan Anak',
            'perundungan' => 'Perundungan',
            'bullying' => 'Perundungan',
            'yang lain' => 'Yang lain',
            'yang lainnya' => 'Yang lain',
            'lainnya' => 'Yang lain',
            'other' => 'Yang lain',
        ];

        if (isset($aliases[$norm])) {
            return $aliases[$norm];
        }

        foreach (self::tipeKasusList() as $canonical) {
            if ($norm === mb_strtolower($canonical, 'UTF-8')) {
                return $canonical;
            }
        }

        return 'Yang lain';
    }

    public function __construct()
    {
        // Menggunakan singleton dari FirebaseServiceProvider
        $this->firestore = app('firebase.firestore');
    }

    /**
     * Normalisasi status pengaduan ke 4 status utama.
     */
    public static function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        if (in_array($status, ['baru', 'belum ditangani', 'pending'])) {
            return 'belum ditangani';
        }
        if (in_array($status, ['proses', 'diproses', 'sedang diproses', 'running'])) {
            return 'diproses';
        }
        if (in_array($status, ['selesai', 'done', 'resolved'])) {
            return 'selesai';
        }
        if (in_array($status, ['ditolak', 'dibatalkan', 'cancel', 'rejected'])) {
            return 'dibatalkan';
        }
        return $status;
    }

    public function index(Request $request)
    {
        // Cek apakah admin sudah login
        if (!Session::has('admin')) {
            return redirect()->route('admin.login');
        }

        // Jika role adalah admin_artikel, hapus akses dashboard & arahkan ke halaman artikel
        if (Session::get('admin.role') === 'admin_artikel') {
            return redirect()->route('admin.articel.index');
        }

        // Default settings untuk dashboard
        $defaultSettings = [
            'show_stats_users' => true,
            'show_stats_reports' => true,
            'show_stats_resolved' => true,
            'show_stats_articles' => true,
            'show_chart_daerah' => true,
            'chart_daerah_type' => 'bar',
            'show_chart_tren' => true,
            'chart_tren_type' => 'line',
            'show_chart_usia' => true,
            'chart_usia_type' => 'bar',
            'show_table_terbaru' => true,
            'table_terbaru_limit' => 10,
            'layout_order' => ['stats', 'chart_daerah', 'tren_usia', 'table_terbaru']
        ];

        $adminUid = Session::get('admin.uid');
        $adminRole = Session::get('admin.role');
        $dashboardSettings = $defaultSettings;

        if ($adminRole === 'super_admin') {
            try {
                $adminDoc = $this->firestore->collection('admins')->document($adminUid)->snapshot();
                if ($adminDoc->exists()) {
                    $adminData = $adminDoc->data();
                    if (isset($adminData['dashboard_settings']) && is_array($adminData['dashboard_settings'])) {
                        $dashboardSettings = array_merge($defaultSettings, $adminData['dashboard_settings']);
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Dashboard settings fetch failed: ' . $e->getMessage());
            }
        }

        // Hapus komponen yang tidak lagi diperlukan (Tren, Tabel Terbaru) dari layout_order
        $dashboardSettings['layout_order'] = array_values(array_filter($dashboardSettings['layout_order'], function($item) {
            return !in_array($item, ['tren_usia', 'table_terbaru', 'stats']);
        }));
        // Pastikan stats_baru, chart_daerah, chart_usia, chart_artikel ada
        $expectedLayout = ['stats_baru', 'chart_daerah', 'chart_usia', 'chart_artikel'];
        $dashboardSettings['layout_order'] = $expectedLayout; // Force layout untuk saat ini karena perombakan total

        // Gunakan Cache untuk menyimpan RAW data dashboard
        $cachedData = Cache::remember('dashboard_base_data_v3', 300, function () {
            $usersSnapshots    = $this->firestore->collection('users')->documents();
            $articlesSnapshots = $this->firestore->collection('articles')->documents();

            $totalUsers = $usersSnapshots->size();
            
            $articlesList = [];
            foreach ($articlesSnapshots as $doc) {
                if ($doc->exists()) {
                    $artData = $doc->data();
                    
                    // Parse release date
                    $tz = config('app.timezone', 'Asia/Jakarta');
                    $dateObj = null;
                    $released = $artData['releasedDate'] ?? null;
                    if ($released) {
                        try {
                            if (is_numeric($released)) {
                                $dateObj = Carbon::createFromTimestampMs((int) $released)->setTimezone($tz);
                            } elseif ($released instanceof \DateTimeInterface) {
                                $dateObj = Carbon::instance($released)->setTimezone($tz);
                            } elseif (is_array($released) && isset($released['seconds'])) {
                                $dateObj = Carbon::createFromTimestamp($released['seconds'])->setTimezone($tz);
                            } else {
                                $dateObj = Carbon::parse($released)->setTimezone($tz);
                            }
                        } catch (\Throwable $e) {}
                    }

                    $articlesList[] = [
                        'id' => $doc->id(),
                        'title' => $artData['title'] ?? '-',
                        'articleType' => $artData['articleType'] ?? '-',
                        'releasedDate' => $artData['releasedDate'] ?? null,
                        'release_year' => $dateObj ? $dateObj->format('Y') : null,
                        'release_month' => $dateObj ? $dateObj->format('n') : null,
                        'release_ym' => $dateObj ? $dateObj->format('Y-m') : null,
                        'updateDate' => $artData['updateDate'] ?? null,
                        'description' => $artData['description'] ?? '-',
                    ];
                }
            }
            
            $reportsList = [];
            $processReportDoc = function ($doc) use (&$reportsList) {
                if (!$doc->exists()) return;
                $data = $doc->data();
                if (!isset($data['report_status']) && !isset($data['case_type']) && !isset($data['user_name'])) {
                    return; 
                }
                
                $statusRaw = strtolower($data['report_status'] ?? ($data['status'] ?? 'baru'));
                $status = self::normalizeStatus($statusRaw);
                $kategoriDisplay = $data['case_type'] ?? 'Tidak diketahui';
                $daerah = $data['incident_city'] ?? ($data['incident_location'] ?? 'Tidak diketahui');

                $tz = config('app.timezone', 'Asia/Jakarta');
                $dateObj = null;
                $created = $data['created_date'] ?? null;
                if ($created) {
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

                $childAge = $data['child_age'] ?? null;
                $ageCategory = 'Tidak diketahui';
                if ($childAge !== null && $childAge !== '') {
                    $ageStr = strtolower(trim((string) $childAge));
                    preg_match('/(\d+)/', $ageStr, $matches);
                    $ageNum = isset($matches[1]) ? (int) $matches[1] : null;

                    if ($ageNum !== null) {
                        $isBulan = (str_contains($ageStr, 'bulan') || str_contains($ageStr, 'bln') || str_contains($ageStr, 'month'));
                        $ageNumYears = $isBulan ? ($ageNum / 12) : $ageNum;

                        if ($ageNumYears >= 0 && $ageNumYears <= 5) {
                            $ageCategory = '0–5 Tahun';
                        } elseif ($ageNumYears > 5 && $ageNumYears <= 10) {
                            $ageCategory = '6–10 Tahun';
                        } elseif ($ageNumYears > 10 && $ageNumYears <= 15) {
                            $ageCategory = '11–15 Tahun';
                        } elseif ($ageNumYears > 15 && $ageNumYears <= 18) {
                            $ageCategory = '16–18 Tahun';
                        }
                    }
                }

                $reportsList[] = [
                    'id' => $doc->id(),
                    'kategori' => $kategoriDisplay,
                    'daerah' => $daerah,
                    'status' => $status,
                    'year' => $dateObj ? $dateObj->format('Y') : null,
                    'month' => $dateObj ? $dateObj->format('n') : null,
                    'date_string' => $dateObj ? $dateObj->format('Y-m-d') : null,
                    'created_at' => $dateObj ? $dateObj->locale('id')->translatedFormat('d M Y') : '-',
                    'child_age' => $childAge ?? '-',
                    'ageCategory' => $ageCategory,
                ];
            };

            try {
                $reportSnapshot = $this->firestore->collection('report')->documents();
                foreach ($reportSnapshot as $doc) { $processReportDoc($doc); }
            } catch (\Throwable $e) {
                \Log::error('Dashboard Report Fetch Error: ' . $e->getMessage());
            }

            return [
                'totalUsers' => $totalUsers,
                'articlesList' => $articlesList,
                'reportsList' => $reportsList,
            ];
        });

        $totalUsers = $cachedData['totalUsers'];
        $allArticles = $cachedData['articlesList'];
        $allReports = $cachedData['reportsList'];

        // 1. FILTER GLOBAL
        $globalStartDate = $request->input('start_date');
        $globalEndDate = $request->input('end_date');
        $globalDaerah = $request->input('global_daerah');
        $globalTipe = $request->input('global_tipe');
        $globalStatus = $request->input('global_status');
        $globalUsia = $request->input('global_usia');

        $filteredReports = array_filter($allReports, function($r) use ($globalStartDate, $globalEndDate, $globalDaerah, $globalTipe, $globalStatus, $globalUsia) {
            if ($globalStartDate && $r['date_string'] && $r['date_string'] < $globalStartDate) return false;
            if ($globalEndDate && $r['date_string'] && $r['date_string'] > $globalEndDate) return false;
            if ($globalDaerah && $this->normalizeToKalbarDaerah($r['daerah']) !== $this->normalizeToKalbarDaerah($globalDaerah)) return false;
            if ($globalTipe && self::normalizeTipeKasus((string) $r['kategori']) !== self::normalizeTipeKasus((string) $globalTipe)) {
                return false;
            }
            if ($globalStatus && strtolower($r['status']) !== strtolower($globalStatus)) return false;
            if ($globalUsia && $r['ageCategory'] !== $globalUsia) return false;
            return true;
        });

        // Dropdown filter global values
        $listAllDaerah = self::kalbarDaerahList();
        $listAllTipe = self::tipeKasusList();
        $listAllStatus = ['belum ditangani', 'diproses', 'selesai', 'dibatalkan'];

        // 2. KARTU STATISTIK BERDASARKAN FILTER
        $totalKejadian = count($filteredReports);
        $countBaru = count(array_filter($filteredReports, fn($r) => $r['status'] === 'belum ditangani'));
        $countProses = count(array_filter($filteredReports, fn($r) => $r['status'] === 'diproses'));
        $countSelesai = count(array_filter($filteredReports, fn($r) => $r['status'] === 'selesai'));
        $countDibatalkan = count(array_filter($filteredReports, fn($r) => $r['status'] === 'dibatalkan'));

        // 3. GRAFIK ANALISIS DATA MAPPING

        // A. Tren Kasus per Bulan (Line Chart)
        $trendCounts = [];
        foreach ($filteredReports as $r) {
            if ($r['year'] && $r['month']) {
                $ym = sprintf('%04d-%02d', $r['year'], $r['month']);
                $trendCounts[$ym] = ($trendCounts[$ym] ?? 0) + 1;
            }
        }
        ksort($trendCounts);
        $trendLabels = [];
        $trendData = [];
        foreach ($trendCounts as $ym => $count) {
            $parts = explode('-', $ym);
            $label = Carbon::createFromDate($parts[0], $parts[1], 1)->locale('id')->translatedFormat('M Y');
            $trendLabels[] = $label;
            $trendData[] = $count;
        }


        // C. Sebaran Tipe Kasus per Daerah (Stacked Horizontal Bar Chart)
        $kalbarList = self::kalbarDaerahList();
        $tipeList = self::tipeKasusList();
        $tipePerDaerah = [];
        foreach ($kalbarList as $daerah) {
            $tipePerDaerah[$daerah] = array_fill_keys($tipeList, 0);
        }
        foreach ($filteredReports as $r) {
            $canonical = $this->normalizeToKalbarDaerah((string) $r['daerah']);
            if ($canonical === null) {
                continue;
            }
            $tipe = self::normalizeTipeKasus((string) $r['kategori']);
            if (isset($tipePerDaerah[$canonical][$tipe])) {
                $tipePerDaerah[$canonical][$tipe]++;
            }
        }

        $tipeDaerahLabels = $kalbarList;
        $tipeDaerahDatasets = [];
        foreach ($tipeList as $tipe) {
            $colors = self::tipeKasusChartColors()[$tipe] ?? ['#94a3b8', '#64748b'];
            $tipeDaerahDatasets[] = [
                'label' => $tipe,
                'data' => array_map(fn ($d) => $tipePerDaerah[$d][$tipe], $kalbarList),
                'backgroundColor' => $colors[0],
                'hoverBackgroundColor' => $colors[1],
            ];
        }

        // D. Distribusi Usia Korban per Daerah (Stacked Bar Chart)
        $kalbarList = self::kalbarDaerahList();
        $usiaPerDaerah = [];
        foreach ($kalbarList as $daerah) {
            $usiaPerDaerah[$daerah] = [
                '0–5 Tahun' => 0,
                '6–10 Tahun' => 0,
                '11–15 Tahun' => 0,
                '16–18 Tahun' => 0
            ];
        }

        foreach ($filteredReports as $r) {
            $canonical = $this->normalizeToKalbarDaerah((string) $r['daerah']);
            if ($canonical !== null && isset($usiaPerDaerah[$canonical][$r['ageCategory']])) {
                $usiaPerDaerah[$canonical][$r['ageCategory']]++;
            }
        }

        $usiaDaerahLabels = $kalbarList;
        $usiaDataset0_5 = [];
        $usiaDataset6_10 = [];
        $usiaDataset11_15 = [];
        $usiaDataset16_18 = [];

        foreach ($kalbarList as $daerah) {
            $usiaDataset0_5[] = $usiaPerDaerah[$daerah]['0–5 Tahun'];
            $usiaDataset6_10[] = $usiaPerDaerah[$daerah]['6–10 Tahun'];
            $usiaDataset11_15[] = $usiaPerDaerah[$daerah]['11–15 Tahun'];
            $usiaDataset16_18[] = $usiaPerDaerah[$daerah]['16–18 Tahun'];
        }

        // E. Perbandingan Status Laporan per Daerah (Stacked Bar Chart)
        $statusList = ['belum ditangani', 'diproses', 'selesai', 'dibatalkan'];
        $statusPerDaerah = [];
        foreach ($kalbarList as $daerah) {
            $statusPerDaerah[$daerah] = array_fill_keys($statusList, 0);
        }
        foreach ($filteredReports as $r) {
            $canonical = $this->normalizeToKalbarDaerah((string) $r['daerah']);
            if ($canonical === null) {
                continue;
            }
            $status = $r['status'] ?? 'belum ditangani';
            if (isset($statusPerDaerah[$canonical][$status])) {
                $statusPerDaerah[$canonical][$status]++;
            }
        }

        $statusDaerahLabels = $kalbarList;
        $statusDaerahDatasets = [
            [
                'label' => 'Belum Ditangani',
                'data' => array_map(fn ($d) => $statusPerDaerah[$d]['belum ditangani'], $kalbarList),
                'backgroundColor' => '#64748b',
                'hoverBackgroundColor' => '#475569',
            ],
            [
                'label' => 'Diproses',
                'data' => array_map(fn ($d) => $statusPerDaerah[$d]['diproses'], $kalbarList),
                'backgroundColor' => '#fbbf24',
                'hoverBackgroundColor' => '#f59e0b',
            ],
            [
                'label' => 'Selesai',
                'data' => array_map(fn ($d) => $statusPerDaerah[$d]['selesai'], $kalbarList),
                'backgroundColor' => '#10b981',
                'hoverBackgroundColor' => '#059669',
            ],
            [
                'label' => 'Dibatalkan',
                'data' => array_map(fn ($d) => $statusPerDaerah[$d]['dibatalkan'], $kalbarList),
                'backgroundColor' => '#ef4444',
                'hoverBackgroundColor' => '#dc2626',
            ],
        ];

        return view('admin.dashboard', [
            'dashboardSettings'  => $dashboardSettings,
            'listAllDaerah'      => $listAllDaerah,
            'listAllTipe'        => $listAllTipe,
            'listAllStatus'      => $listAllStatus,
            'listAllUsia'        => ['0–5 Tahun', '6–10 Tahun', '11–15 Tahun', '16–18 Tahun'],
            
            'globalStartDate'    => $globalStartDate,
            'globalEndDate'      => $globalEndDate,
            'globalDaerah'       => $globalDaerah,
            'globalTipe'         => $globalTipe,
            'globalStatus'       => $globalStatus,
            'globalUsia'         => $globalUsia,
            
            'totalKejadian'      => $totalKejadian,
            'countBaru'          => $countBaru,
            'countProses'        => $countProses,
            'countSelesai'       => $countSelesai,
            'countDibatalkan'    => $countDibatalkan,
            
            'trendLabels'        => $trendLabels,
            'trendData'          => $trendData,
            
            'tipeDaerahLabels'   => $tipeDaerahLabels,
            'tipeDaerahDatasets' => $tipeDaerahDatasets,
            
            'usiaDaerahLabels'   => $usiaDaerahLabels,
            'usiaDataset0_5'     => $usiaDataset0_5,
            'usiaDataset6_10'    => $usiaDataset6_10,
            'usiaDataset11_15'   => $usiaDataset11_15,
            'usiaDataset16_18'   => $usiaDataset16_18,

            'statusDaerahLabels'   => $statusDaerahLabels,
            'statusDaerahDatasets' => $statusDaerahDatasets,
            
            'filteredReports'    => array_values($filteredReports),
        ]);
    }

    public function refresh()
    {
        Cache::forget('dashboard_base_data_v3');
        return redirect()->route('admin.dashboard')->with('success', 'Data dashboard berhasil diperbarui.');
    }

    public function saveSettings(Request $request)
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        $request->validate([
            'show_stats_users' => 'nullable|boolean',
            'show_stats_reports' => 'nullable|boolean',
            'show_stats_resolved' => 'nullable|boolean',
            'show_stats_articles' => 'nullable|boolean',
            'show_chart_daerah' => 'nullable|boolean',
            'chart_daerah_type' => 'required|in:bar,line,pie,doughnut,polarArea',
            'show_chart_tren' => 'nullable|boolean',
            'chart_tren_type' => 'required|in:line,bar',
            'show_chart_usia' => 'nullable|boolean',
            'chart_usia_type' => 'required|in:bar,pie,doughnut,polarArea',
            'show_table_terbaru' => 'nullable|boolean',
            'table_terbaru_limit' => 'required|integer|in:5,10,15,20',
            'layout_order' => 'required|array|min:4|max:4',
            'layout_order.*' => 'required|string|in:stats,chart_daerah,tren_usia,table_terbaru',
        ]);

        $settings = [
            'show_stats_users' => $request->has('show_stats_users'),
            'show_stats_reports' => $request->has('show_stats_reports'),
            'show_stats_resolved' => $request->has('show_stats_resolved'),
            'show_stats_articles' => $request->has('show_stats_articles'),
            'show_chart_daerah' => $request->has('show_chart_daerah'),
            'chart_daerah_type' => $request->input('chart_daerah_type', 'bar'),
            'show_chart_tren' => $request->has('show_chart_tren'),
            'chart_tren_type' => $request->input('chart_tren_type', 'line'),
            'show_chart_usia' => $request->has('show_chart_usia'),
            'chart_usia_type' => $request->input('chart_usia_type', 'bar'),
            'show_table_terbaru' => $request->has('show_table_terbaru'),
            'table_terbaru_limit' => (int) $request->input('table_terbaru_limit', 10),
            'layout_order' => $request->input('layout_order'),
        ];

        try {
            $adminUid = Session::get('admin.uid');
            $this->firestore->collection('admins')->document($adminUid)->update([
                ['path' => 'dashboard_settings', 'value' => $settings]
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan dashboard berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan setelan dashboard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetSettings()
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        try {
            $adminUid = Session::get('admin.uid');
            $this->firestore->collection('admins')->document($adminUid)->update([
                ['path' => 'dashboard_settings', 'value' => null]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan dashboard berhasil direset ke default.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mereset setelan dashboard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mereset pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }
}
