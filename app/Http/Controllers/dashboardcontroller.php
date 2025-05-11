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
        $laporanSnapshots  = $this->firestore->collection('laporan')->documents();
        $articlesSnapshots = $this->firestore->collection('articles')->documents();

        // Hitung jumlah dokumen
        $totalUsers    = $usersSnapshots->size();
        $totalLaporan  = $laporanSnapshots->size();
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

        foreach ($laporanSnapshots as $doc) {
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
                case 'baru':
                    $laporanBaru++;
                    break;
                case 'ditolak':
                    $laporanDitolak++;
                    break;
            }

            $kategori = $data['kategori'] ?? 'Tidak diketahui';
            $daerah = $data['daerah'] ?? 'Tidak diketahui';

            // Hitung total per kategori
            if (!isset($kategoriCount[$kategori])) {
                $kategoriCount[$kategori] = 0;
            }
            $kategoriCount[$kategori]++;

            // Hitung total per daerah
            if (!isset($daerahCount[$daerah])) {
                $daerahCount[$daerah] = 0;
            }
            $daerahCount[$daerah]++;

            // Hitung kategori per daerah
            if (!isset($kategoriPerDaerah[$daerah])) {
                $kategoriPerDaerah[$daerah] = [];
            }
            if (!isset($kategoriPerDaerah[$daerah][$kategori])) {
                $kategoriPerDaerah[$daerah][$kategori] = 0;
            }
            $kategoriPerDaerah[$daerah][$kategori]++;
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
