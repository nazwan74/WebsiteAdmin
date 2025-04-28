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
        
        // Ambil data dari koleksi "users", "laporan", dan "articles"
        $usersSnapshots   = $this->firestore->collection('users')->documents();
        $laporanSnapshots = $this->firestore->collection('laporan')->documents();
        $articlesSnapshots = $this->firestore->collection('articles')->documents();

        // Hitung jumlah dokumen di masing-masing koleksi
        $totalUsers    = $usersSnapshots->size();
        $totalLaporan  = $laporanSnapshots->size();
        $totalArticles = $articlesSnapshots->size();

        // Hitung laporan dengan status "selesai"
        $laporanSelesai = 0;

        // Untuk visualisasi
        $kategoriCount = [];
        $daerahCount = [];

        foreach ($laporanSnapshots as $doc) {
            $data = $doc->data();

            // Hitung status selesai
            if (isset($data['status']) && strtolower($data['status']) === 'selesai') {
                $laporanSelesai++;
            }

            // Hitung kategori
            $kategori = $data['kategori'] ?? 'Tidak diketahui';
            if (!isset($kategoriCount[$kategori])) {
                $kategoriCount[$kategori] = 0;
            }
            $kategoriCount[$kategori]++;

            // Hitung daerah
            $daerah = $data['daerah'] ?? 'Tidak diketahui';
            if (!isset($daerahCount[$daerah])) {
                $daerahCount[$daerah] = 0;
            }
            $daerahCount[$daerah]++;
        }

        // Urutkan dan ambil 4 terbesar
        arsort($kategoriCount);
        arsort($daerahCount);

        $topKategori = array_slice($kategoriCount, 0, 4, true);
        $topDaerah   = array_slice($daerahCount, 0, 4, true);

        // Kirim semua data ke view
        return view('admin.dashboard', [
            'totalUsers'    => $totalUsers,
            'totalLaporan'  => $totalLaporan,
            'totalArticles' => $totalArticles,
            'totalSelesai'  => $laporanSelesai,
            'topKategori'   => $topKategori,
            'topDaerah'     => $topDaerah,
        ]);
    }
}
