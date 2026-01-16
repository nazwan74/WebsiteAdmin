<?php

namespace App\Http\Controllers;

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

    public function index()
    {
        // Cek apakah admin sudah login
        if (!Session::has('admin')) {
            return redirect()->route('admin.login');
        }

        // Ambil data dari koleksi Firestore
        $usersSnapshots    = $this->firestore->collection('users')->documents();
        $articlesSnapshots = $this->firestore->collection('articles')->documents();

        // Struktur baru laporan: laporan/{kategoriDoc}/{userIdSubcollection}/{laporanId}
        $kategoriMap = [
            'kekerasan_anak', 'bullying', 'pernikahan_anak', 'stunting'
        ];

        // Hitung jumlah dokumen
        $totalUsers    = $usersSnapshots->size();
        $totalLaporan  = 0;
        $totalArticles = $articlesSnapshots->size();

        // Hitung status laporan
        $laporanSelesai = 0;
        $laporanDiproses = 0;
        $laporanBaru = 0;
        $laporanDitolak = 0;

        // Untuk perhitungan
        $kategoriCount = [];
        $daerahCount = [];
        $kategoriPerDaerah = [];

        foreach ($kategoriMap as $kategoriKey) {
            $kategoriDocRef = $this->firestore->collection('laporan')->document($kategoriKey);
            foreach ($kategoriDocRef->collections() as $userCollection) {
                foreach ($userCollection->documents() as $doc) {
                    if (!$doc->exists()) continue;
                    $totalLaporan++;
                    $data = $doc->data();

                    // Hitung status
                    $status = strtolower($data['status'] ?? 'baru');
                    switch ($status) {
                        case 'selesai':
                            $laporanSelesai++;
                            break;
                        case 'diproses':
                            $laporanDiproses++;
                            break;
                        case 'ditolak':
                            $laporanDitolak++;
                            break;
                        default:
                            $laporanBaru++;
                            break;
                    }

                    $kategoriDisplay = match ($kategoriKey) {
                        'kekerasan_anak' => 'Kekerasan Anak',
                        'bullying' => 'Bullying',
                        'pernikahan_anak' => 'Pernikahan Anak',
                        'stunting' => 'Stunting',
                        default => 'Tidak diketahui'
                    };
                    $daerah = $data['tempat_kejadian'] ?? ($data['daerah'] ?? 'Tidak diketahui');

                    // Hitung total per kategori
                    if (!isset($kategoriCount[$kategoriDisplay])) {
                        $kategoriCount[$kategoriDisplay] = 0;
                    }
                    $kategoriCount[$kategoriDisplay]++;

                    // Hitung total per daerah
                    if (!isset($daerahCount[$daerah])) {
                        $daerahCount[$daerah] = 0;
                    }
                    $daerahCount[$daerah]++;

                    // Hitung kategori per daerah
                    if (!isset($kategoriPerDaerah[$daerah])) {
                        $kategoriPerDaerah[$daerah] = [];
                    }
                    if (!isset($kategoriPerDaerah[$daerah][$kategoriDisplay])) {
                        $kategoriPerDaerah[$daerah][$kategoriDisplay] = 0;
                    }
                    $kategoriPerDaerah[$daerah][$kategoriDisplay]++;
                }
            }
        }

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
            'totalUsers'        => $totalUsers,
            'totalLaporan'      => $totalLaporan,
            'totalArticles'     => $totalArticles,
            'totalSelesai'      => $laporanSelesai,
            'totalDiproses'     => $laporanDiproses,
            'totalBaru'         => $laporanBaru,
            'totalDitolak'      => $laporanDitolak,
            'topKategori'       => $topKategori,
            'topDaerahKategori' => $topDaerahKategori,
        ]);
    }
}
