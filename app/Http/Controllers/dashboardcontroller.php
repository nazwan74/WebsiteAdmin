<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class dashboardcontroller extends Controller
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
        foreach ($laporanSnapshots as $doc) {
            $data = $doc->data();
            if (isset($data['status']) && strtolower($data['status']) === 'selesai') {
                $laporanSelesai++;
            }
        }

        // Kirim data ke view dashboard
        return view('admin.dashboard', [
            'totalUsers'    => $totalUsers,
            'totalLaporan'  => $totalLaporan,
            'totalArticles' => $totalArticles,
            'totalSelesai'  => $laporanSelesai,
        ]);
    }
}
