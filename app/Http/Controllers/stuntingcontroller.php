<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;

class stuntingcontroller extends Controller
{
    protected $firestore;

    public function __construct()
    {
        // Cek apakah admin sudah login
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send();
        }

        // Koneksi ke Firebase
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));

        $this->firestore = $factory->createFirestore()->database();
    }
    public function chart()
    {
        $documents = $this->firestore->collection('stunting')->documents();

        $cityStats = [];

        foreach ($documents as $doc) {
            if (!$doc->exists()) continue;

            $data = $doc->data();
            $city = ucwords(strtolower(trim($data['userCity'] ?? 'Unknown')));
            $result = strtolower($data['stuntingResult'] ?? '');

            if (!isset($cityStats[$city])) {
                $cityStats[$city] = [
                    'normal' => 0,
                    'stunted' => 0,
                    'severely_stunted' => 0
                ];
            }

            if (in_array($result, ['normal', 'stunted', 'severely_stunted'])) {
                $cityStats[$city][$result]++;
            }
        }

        foreach ($cityStats as $city => &$counts) {
            $total = array_sum($counts);
            if ($total > 0) {
                foreach ($counts as $key => &$value) {
                    $value = round(($value / $total) * 100, 1); // Persen
                }
            }
        }

        // Urutkan berdasarkan jumlah stunted terbanyak
        uasort($cityStats, function ($a, $b) {
            return $b['stunted'] <=> $a['stunted'];
        });

        // Ambil 4 kota teratas
        $top4Cities = array_slice($cityStats, 0, 4, true);

        return view('admin.stunting', [
            'topCities' => $top4Cities,
            'allCities' => $cityStats
        ]);
    }


}
