<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;


class laporancontroller extends Controller
{
    /**
     * Konversi created_date (number milliseconds / string / Timestamp) ke string waktu lokal (Y-m-d H:i:s).
     */
    private function createdDateToLocal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        $tz = config('app.timezone', 'Asia/Jakarta');
        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestampMs((int) $value)->setTimezone($tz)->format('Y-m-d H:i:s');
            }
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->setTimezone($tz)->format('Y-m-d H:i:s');
            }
            if (is_array($value) && isset($value['seconds'])) {
                return Carbon::createFromTimestamp($value['seconds'])->setTimezone($tz)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->setTimezone($tz)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    protected $firestore;
    protected $kategoriMap = [
        'kekerasan_anak'   => 'Kekerasan Anak',
        'bullying'         => 'Bullying',
        'pernikahan_anak'  => 'Pernikahan Anak',
        'stunting'         => 'Stunting',
    ];

    public function __construct()
    {
        // Jika belum login, redirect ke login
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send();
        }

        // Setup koneksi Firebase
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDefaultStorageBucket(config('firebase.storage_bucket'));

        $this->firestore = $factory->createFirestore()->database();
    }

    /**
     * Ambil daftar laporan dari Firestore (flat + nested).
     * @return array
     */
    private function getLaporanList()
    {
        $laporan = [];

        // Coba struktur flat dulu: semua dokumen langsung di koleksi report
        try {
            $snapshot = $this->firestore->collection('report')->documents();
            foreach ($snapshot as $doc) {
                if (!$doc->exists()) {
                    continue;
                }
                $data = $doc->data();
                if (isset($data['report_status']) || isset($data['case_type']) || isset($data['user_name'])) {
                    $data['id'] = $doc->id();
                    $data['kategori'] = $data['case_type'] ?? '-';
                    $data['daerah'] = $data['incident_city'] ?? ($data['incident_location'] ?? '-');
                    $data['created_date'] = $this->createdDateToLocal($data['created_date'] ?? null);
                    $data['create_at'] = $data['created_date'];
                    $data['status'] = $data['report_status'] ?? 'baru';
                    $data['judul'] = $data['report_number'] ?? ($data['case_type'] ?? 'Laporan');
                    $laporan[] = $data;
                }
            }
        } catch (\Throwable $e) {
            //
        }

        if (empty($laporan)) {
            foreach ($this->kategoriMap as $kategoriKey => $kategoriDisplay) {
                try {
                    $kategoriDocRef = $this->firestore->collection('report')->document($kategoriKey);
                    foreach ($kategoriDocRef->collections() as $userCollection) {
                        foreach ($userCollection->documents() as $doc) {
                            if (!$doc->exists()) continue;
                            $data = $doc->data();
                            $data['id'] = $doc->id();
                            $data['kategori'] = $data['case_type'] ?? $kategoriDisplay;
                            $data['daerah'] = $data['incident_city'] ?? ($data['incident_location'] ?? '-');
                            $data['created_date'] = $this->createdDateToLocal($data['created_date'] ?? null);
                            $data['create_at'] = $data['created_date'];
                            $data['status'] = $data['report_status'] ?? 'baru';
                            $data['judul'] = $data['report_number'] ?? ($data['case_type'] ?? 'Laporan');
                            $laporan[] = $data;
                        }
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        usort($laporan, function ($a, $b) {
            $ta = $a['created_date'] ?? ($a['create_at'] ?? '');
            $tb = $b['created_date'] ?? ($b['create_at'] ?? '');
            if ($ta === $tb) return 0;
            return $ta < $tb ? 1 : -1;
        });

        return $laporan;
    }

    public function index()
    {
        $laporan = $this->getLaporanList();

        $kategoriList = [];
        foreach ($laporan as $item) {
            $k = $item['case_type'] ?? ($item['kategori'] ?? null);
            if ($k !== null && $k !== '' && $k !== '-' && !in_array($k, $kategoriList, true)) {
                $kategoriList[] = $k;
            }
        }
        sort($kategoriList, SORT_STRING);

        return view('admin.laporan', compact('laporan', 'kategoriList'));
    }

    /**
     * Download daftar laporan dalam format CSV.
     * Mendukung filter: daerah, date_start, date_end, kategori, status, search (nama pelapor).
     */
    public function downloadList(Request $request)
    {
        $laporan = $this->getLaporanList();

        // Terapkan filter yang sama dengan halaman Laporan
        $daerah = $request->query('daerah');
        if ($daerah !== null && $daerah !== '') {
            $daerahList = array_map('trim', explode(',', $daerah));
            $daerahList = array_filter($daerahList);
            if (!empty($daerahList)) {
                $laporan = array_filter($laporan, function ($item) use ($daerahList) {
                    $d = $item['daerah'] ?? '-';
                    return in_array($d, $daerahList, true);
                });
                $laporan = array_values($laporan);
            }
        }

        $dateStart = $request->query('date_start');
        $dateEnd = $request->query('date_end');
        if ($dateStart !== null && $dateStart !== '' && $dateEnd !== null && $dateEnd !== '') {
            $laporan = array_filter($laporan, function ($item) use ($dateStart, $dateEnd) {
                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                if ($tanggalBuat === null || $tanggalBuat === '') {
                    return false;
                }
                try {
                    $d = Carbon::parse($tanggalBuat)->format('Y-m-d');
                    return $d >= $dateStart && $d <= $dateEnd;
                } catch (\Throwable $e) {
                    return false;
                }
            });
            $laporan = array_values($laporan);
        }

        $kategori = $request->query('kategori');
        if ($kategori !== null && $kategori !== '') {
            $kategoriList = array_map('trim', array_map('strtolower', explode(',', $kategori)));
            $kategoriList = array_filter($kategoriList);
            if (!empty($kategoriList)) {
                $laporan = array_filter($laporan, function ($item) use ($kategoriList) {
                    $k = strtolower(trim($item['case_type'] ?? ($item['kategori'] ?? '')));
                    return $k !== '' && in_array($k, $kategoriList, true);
                });
                $laporan = array_values($laporan);
            }
        }

        $status = $request->query('status');
        if ($status !== null && $status !== '') {
            $statusList = array_map('trim', array_map('strtolower', explode(',', $status)));
            $statusList = array_filter($statusList);
            if (!empty($statusList)) {
                $laporan = array_filter($laporan, function ($item) use ($statusList) {
                    $s = strtolower(trim($item['report_status'] ?? ($item['status'] ?? '')));
                    return in_array($s, $statusList, true);
                });
                $laporan = array_values($laporan);
            }
        }

        $search = $request->query('search');
        if ($search !== null && $search !== '') {
            $searchLower = mb_strtolower(trim($search));
            $laporan = array_filter($laporan, function ($item) use ($searchLower) {
                $nama = $item['user_name'] ?? ($item['nama'] ?? '');
                return $nama !== '' && mb_strpos(mb_strtolower($nama), $searchLower) !== false;
            });
            $laporan = array_values($laporan);
        }

        $filename = 'daftar-laporan-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($laporan) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            fputcsv($stream, [
                'No',
                'ID Laporan',
                'Nama Pelapor',
                'Tipe Kasus',
                'Daerah',
                'Tanggal Kejadian',
                'Status',
                'Tanggal Buat',
            ], ';');

            foreach ($laporan as $i => $item) {
                $tanggalKejadian = $item['incident_date'] ?? '-';
                if ($tanggalKejadian !== '-' && $tanggalKejadian !== null && $tanggalKejadian !== '') {
                    try {
                        $tanggalKejadian = Carbon::parse($tanggalKejadian)->format('d/m/Y');
                    } catch (\Throwable $e) {
                        //
                    }
                }
                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? '-');

                fputcsv($stream, [
                    $i + 1,
                    $item['id'] ?? '-',
                    $item['user_name'] ?? ($item['nama'] ?? '-'),
                    $item['case_type'] ?? ($item['kategori'] ?? '-'),
                    $item['daerah'] ?? '-',
                    $tanggalKejadian,
                    $item['report_status'] ?? ($item['status'] ?? 'baru'),
                    $tanggalBuat,
                ], ';');
            }
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detail($id)
    {
        // Cari dokumen berdasarkan ID laporan pada struktur baru
        $found = $this->findReportRefById($id);

        if (!$found) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        [$kategoriKey, $userCollectionId, $docRef] = $found;
        $doc = $docRef->snapshot();
        $data = $doc->data();
        $data['id'] = $doc->id();
        // Map field report untuk detail view
        $data['kategori'] = $data['case_type'] ?? ($this->kategoriMap[$kategoriKey] ?? $kategoriKey);
        $data['daerah'] = $data['incident_city'] ?? ($data['incident_location'] ?? '-');
        $data['created_date'] = $this->createdDateToLocal($data['created_date'] ?? null);
        $data['create_at'] = $data['created_date'];
        $data['status'] = $data['report_status'] ?? 'baru';
        $data['judul'] = $data['report_number'] ?? ($data['case_type'] ?? 'Detail Laporan');
        $data['nama'] = $data['user_name'] ?? '-';
        $data['no_hp'] = $data['phone_number'] ?? '-';
        $data['deskripsi_lengkap'] = $data['detail_description'] ?? ($data['deskripsi_lengkap'] ?? '-');

        // Ambil chat jika ada
        $chatMessages = [];
        foreach ($docRef->collection('chat')->documents() as $chatDoc) {
            if (!$chatDoc->exists()) {
                continue;
            }
            $chatData = $chatDoc->data();
            $message = $chatData['message'] ?? ($chatData['text'] ?? ($chatData['content'] ?? ($chatData['bubble'] ?? null)));
            $chatMessages[] = [
                'id' => $chatDoc->id(),
                'message' => $message ?? json_encode($chatData),
                'data' => $chatData,
            ];
        }

        return request()->ajax()
            ? view('admin.laporan-detail-partial', [
                'laporan' => $data,
                'chatMessages' => $chatMessages,
            ])
            : view('admin.laporan-detail', [
                'laporan' => $data,
                'chatMessages' => $chatMessages,
            ]);
    }
    
    public function setStatus(Request $request, $id)
    {
        $status = $request->input('status');

        $found = $this->findReportRefById($id);
        if (!$found) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan.'], 404);
            }
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        [, , $docRef] = $found;
        $docRef->update([
            ['path' => 'report_status', 'value' => $status]
        ]);

        // If it's an AJAX request, return updated counts
        if ($request->ajax()) {
            // Hitung ulang total dari struktur baru
            $totalLaporan = 0;
            $laporanSelesai = 0;
            $laporanDiproses = 0;
            $laporanBaru = 0;
            $laporanDitolak = 0;

            foreach ($this->kategoriMap as $kategoriKey => $kategoriDisplay) {
                $kategoriDocRef = $this->firestore->collection('report')->document($kategoriKey);
            foreach ($kategoriDocRef->collections() as $userCollection) {
                foreach ($userCollection->documents() as $doc) {
                    if (!$doc->exists()) continue;
                    $totalLaporan++;
                    $d = $doc->data();
                    $st = strtolower($d['report_status'] ?? ($d['status'] ?? 'baru'));
                        switch ($st) {
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
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status berhasil diperbarui',
                'data' => [
                    'totalLaporan' => $totalLaporan,
                    'totalSelesai' => $laporanSelesai,
                    'totalDiproses' => $laporanDiproses,
                    'totalBaru' => $laporanBaru,
                    'totalDitolak' => $laporanDitolak
                ]
            ]);
        }
    
        return redirect()->route('admin.laporan.detail', ['id' => $id])->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        [, , $docRef] = $found;

        // Opsional: hapus subkoleksi chat terlebih dahulu
        foreach ($docRef->collection('chat')->documents() as $chatDoc) {
            if ($chatDoc->exists()) {
                $chatDoc->reference()->delete();
            }
        }

        // Hapus laporan dari Firestore
        $docRef->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.laporan')->with('success', 'Laporan berhasil dihapus.');
    }


    public function downloadPDF($id)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        [, , $docRef] = $found;
        $doc = $docRef->snapshot();
        $laporan = $doc->data();
        $laporan['id'] = $doc->id();
        $laporan['kategori'] = $laporan['case_type'] ?? '';
        $laporan['daerah'] = $laporan['incident_city'] ?? ($laporan['incident_location'] ?? '-');
        $laporan['created_date'] = $this->createdDateToLocal($laporan['created_date'] ?? null);
        $laporan['create_at'] = $laporan['created_date'];
        $laporan['judul'] = $laporan['report_number'] ?? ($laporan['case_type'] ?? 'Laporan');
        $laporan['nama'] = $laporan['user_name'] ?? '-';
        $laporan['no_hp'] = $laporan['phone_number'] ?? '-';
        $laporan['status'] = $laporan['report_status'] ?? 'baru';

        $pdf = Pdf::loadView('admin.laporan-pdf', compact('laporan'));
        return $pdf->download('laporan-'.$id.'.pdf');
    }

    private function findReportRefById(string $laporanId)
    {
        // Coba struktur flat dulu: report/{reportId}
        try {
            $docRef = $this->firestore->collection('report')->document($laporanId);
            if ($docRef->snapshot()->exists()) {
                return [null, null, $docRef];
            }
        } catch (\Throwable $e) {
            // lanjut ke nested
        }

        // Struktur nested: report/{kategoriDoc}/{userId}/{reportId}
        foreach ($this->kategoriMap as $kategoriKey => $kategoriDisplay) {
            try {
                $kategoriDocRef = $this->firestore->collection('report')->document($kategoriKey);
                foreach ($kategoriDocRef->collections() as $userCollection) {
                    $candidate = $userCollection->document($laporanId)->snapshot();
                    if ($candidate->exists()) {
                        return [$kategoriKey, $userCollection->id(), $userCollection->document($laporanId)];
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return null;
    }

    public function chat($id)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        [, , $docRef] = $found;
        $doc = $docRef->snapshot();
        $laporan = $doc->data();
        $laporan['id'] = $doc->id();
        $laporan['judul'] = $laporan['report_number'] ?? ($laporan['case_type'] ?? 'Laporan');

        return view('admin.laporan-chat', compact('laporan'));
    }

    public function chatMessages($id)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        [, , $docRef] = $found;
        $since = request()->query('since');
        $collection = $docRef->collection('chat');

        // Query delta jika since ada; jika tidak, ambil SEMUA pesan untuk menampilkan seluruh histori
        if ($since) {
            $query = $collection
                ->where('created_at', '>', $since)
                ->orderBy('created_at')
                ->limit(200);
            $documents = $query->documents();
        } else {
            $documents = $collection
                ->orderBy('created_at')
                ->documents();
        }

        $messages = [];
        foreach ($documents as $chatDoc) {
            if (!$chatDoc->exists()) continue;
            $data = $chatDoc->data();
            $messages[] = array_merge($data, [
                'id' => $chatDoc->id(),
            ]);
        }

        // Pastikan urutan naik berdasarkan created_at untuk konsistensi UI
        usort($messages, function ($a, $b) {
            $ta = $a['created_at'] ?? '';
            $tb = $b['created_at'] ?? '';
            if ($ta === $tb) return 0;
            return $ta < $tb ? -1 : 1;
        });

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }

    public function sendChat(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $found = $this->findReportRefById($id);
        if (!$found) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        [, , $docRef] = $found;
        $payload = [
            'message' => $request->input('message'),
            'sender' => 'admin',
            'created_at' => now()->toDateTimeString(),
        ];
        $docRef->collection('chat')->add($payload);

        return response()->json(['status' => 'success']);
    }

    public function deleteChat($id, $messageId)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        [, , $docRef] = $found;
        $messageRef = $docRef->collection('chat')->document($messageId);
        $snap = $messageRef->snapshot();
        if (!$snap->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Pesan tidak ditemukan'], 404);
        }

        $messageRef->delete();
        return response()->json(['status' => 'success']);
    }

    public function updateChat(Request $request, $id, $messageId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $found = $this->findReportRefById($id);
        if (!$found) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        [, , $docRef] = $found;
        $messageRef = $docRef->collection('chat')->document($messageId);
        $snap = $messageRef->snapshot();
        if (!$snap->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Pesan tidak ditemukan'], 404);
        }

        $messageRef->update([
            ['path' => 'message', 'value' => $request->input('message')],
            ['path' => 'edited_at', 'value' => now()->toDateTimeString()],
        ]);

        return response()->json(['status' => 'success']);
    }
}
