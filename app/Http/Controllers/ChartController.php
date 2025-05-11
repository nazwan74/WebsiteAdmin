<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;

class ChartController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send();
        }
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->firestore = $factory->createFirestore()->database();
    }

    public function index()
    {
        // Ambil data dari koleksi laporan
        $laporanSnapshots = $this->firestore->collection('laporan')->documents();
        
        // Inisialisasi array untuk menyimpan data
        $daerahKategoriData = [];
        
        foreach ($laporanSnapshots as $doc) {
            $data = $doc->data();
            $daerah = $data['daerah'] ?? 'Tidak diketahui';
            $kategori = $data['kategori'] ?? 'Tidak diketahui';
            
            // Inisialisasi array untuk daerah jika belum ada
            if (!isset($daerahKategoriData[$daerah])) {
                $daerahKategoriData[$daerah] = [];
            }
            
            // Inisialisasi counter untuk kategori jika belum ada
            if (!isset($daerahKategoriData[$daerah][$kategori])) {
                $daerahKategoriData[$daerah][$kategori] = 0;
            }
            
            // Increment counter
            $daerahKategoriData[$daerah][$kategori]++;
        }
        
        // Siapkan data untuk chart
        $daerahLabels = array_keys($daerahKategoriData);
        $kategoriLabels = [];
        $datasets = [];
        
        // Kumpulkan semua kategori unik
        foreach ($daerahKategoriData as $daerahData) {
            foreach (array_keys($daerahData) as $kategori) {
                if (!in_array($kategori, $kategoriLabels)) {
                    $kategoriLabels[] = $kategori;
                }
            }
        }
        
        // Siapkan dataset untuk setiap kategori
        $colors = [
            '#4e79a7', '#f28e2c', '#e15759', '#76b7b2', 
            '#59a14f', '#edc949', '#af7aa1', '#ff9da7'
        ];
        
        foreach ($kategoriLabels as $index => $kategori) {
            $data = [];
            foreach ($daerahLabels as $daerah) {
                $data[] = $daerahKategoriData[$daerah][$kategori] ?? 0;
            }
            
            $datasets[] = [
                'label' => $kategori,
                'data' => $data,
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 1
            ];
        }
        
        return view('admin.chart.index', [
            'daerahLabels' => $daerahLabels,
            'datasets' => $datasets,
            'title' => 'Distribusi Kasus per Daerah dan Kategori'
        ]);
    }

    private function getChartDataByKategori($kategori)
    {
        $laporanSnapshots = $this->firestore->collection('laporan')
            ->where('kategori', '=', $kategori)
            ->documents();
        
        $daerahData = [];
        
        foreach ($laporanSnapshots as $doc) {
            $data = $doc->data();
            $daerah = $data['daerah'] ?? 'Tidak diketahui';
            
            if (!isset($daerahData[$daerah])) {
                $daerahData[$daerah] = 0;
            }
            
            $daerahData[$daerah]++;
        }
        
        return [
            'labels' => array_keys($daerahData),
            'data' => array_values($daerahData)
        ];
    }

    public function kekerasanAnak()
    {
        $chartData = $this->getChartDataByKategori('kekerasan anak');
        return view('admin.chart.kategori', [
            'title' => 'Distribusi Kasus Kekerasan Anak per Daerah',
            'labels' => $chartData['labels'],
            'data' => $chartData['data'],
            'backgroundColor' => '#e15759'
        ]);
    }

    public function pernikahanAnak()
    {
        $chartData = $this->getChartDataByKategori('pernikahan dini');
        return view('admin.chart.kategori', [
            'title' => 'Distribusi Kasus Pernikahan Anak per Daerah',
            'labels' => $chartData['labels'],
            'data' => $chartData['data'],
            'backgroundColor' => '#4e79a7'
        ]);
    }

    public function bullying()
    {
        $chartData = $this->getChartDataByKategori('bullying');
        return view('admin.chart.kategori', [
            'title' => 'Distribusi Kasus Bullying per Daerah',
            'labels' => $chartData['labels'],
            'data' => $chartData['data'],
            'backgroundColor' => '#f28e2c'
        ]);
    }

    public function stunting()
    {
        // Get data from stunting collection for stunting results
        $stuntingSnapshots = $this->firestore->collection('stunting')->documents();
        
        // Initialize array to store stunting result data
        $daerahStuntingData = [];
        
        foreach ($stuntingSnapshots as $doc) {
            $data = $doc->data();
            $daerah = $data['userCity'] ?? 'Tidak diketahui';
            $stuntingResult = $data['stuntingResult'] ?? 'Tidak diketahui';
            
            // Initialize array for daerah if not exists
            if (!isset($daerahStuntingData[$daerah])) {
                $daerahStuntingData[$daerah] = [];
            }
            
            // Initialize counter for stuntingResult if not exists
            if (!isset($daerahStuntingData[$daerah][$stuntingResult])) {
                $daerahStuntingData[$daerah][$stuntingResult] = 0;
            }
            
            // Increment counter
            $daerahStuntingData[$daerah][$stuntingResult]++;
        }
        
        // Prepare data for stunting result chart
        $daerahLabels = array_keys($daerahStuntingData);
        $stuntingResultLabels = [];
        $datasets = [];
        
        // Collect all unique stunting results
        foreach ($daerahStuntingData as $daerahData) {
            foreach (array_keys($daerahData) as $result) {
                if (!in_array($result, $stuntingResultLabels)) {
                    $stuntingResultLabels[] = $result;
                }
            }
        }
        
        // Prepare dataset for each stunting result
        $colors = [
            '#4e79a7', '#f28e2c', '#e15759', '#76b7b2', 
            '#59a14f', '#edc949', '#af7aa1', '#ff9da7'
        ];
        
        foreach ($stuntingResultLabels as $index => $result) {
            $data = [];
            foreach ($daerahLabels as $daerah) {
                $data[] = $daerahStuntingData[$daerah][$result] ?? 0;
            }
            
            $datasets[] = [
                'label' => $result,
                'data' => $data,
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 1
            ];
        }

        // Get data from laporan collection for stunting cases
        $laporanSnapshots = $this->firestore->collection('laporan')
            ->where('kategori', '=', 'stunting')
            ->documents();
        
        $daerahStuntingCases = [];
        
        foreach ($laporanSnapshots as $doc) {
            $data = $doc->data();
            $daerah = $data['daerah'] ?? 'Tidak diketahui';
            
            if (!isset($daerahStuntingCases[$daerah])) {
                $daerahStuntingCases[$daerah] = 0;
            }
            
            $daerahStuntingCases[$daerah]++;
        }
        
        return view('admin.chart.kategori', [
            'title' => 'Distribusi Hasil Stunting per Kota',
            'labels' => $daerahLabels,
            'datasets' => $datasets,
            'backgroundColor' => '#59a14f',
            'stuntingCasesLabels' => array_keys($daerahStuntingCases),
            'stuntingCasesData' => array_values($daerahStuntingCases)
        ]);
    }
} 