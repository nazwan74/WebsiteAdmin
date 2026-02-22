<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;


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
    protected $storage;
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
        $this->storage = $factory->createStorage();
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
                    $data['adminLastReadAt'] = $data['adminLastReadAt'] ?? 0;
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
                            $data['adminLastReadAt'] = $data['adminLastReadAt'] ?? 0;
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

        $source = request()->query('source', 'laporan');

        return request()->ajax()
            ? view('admin.laporan-detail-partial', [
                'laporan' => $data,
                'chatMessages' => $chatMessages,
                'source' => $source,
            ])
            : view('admin.laporan-detail', [
                'laporan' => $data,
                'chatMessages' => $chatMessages,
                'source' => $source,
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
    
        $source = $request->input('source', 'laporan');

        if ($source === 'dashboard') {
            return redirect()->route('admin.dashboard')->with('success', 'Status berhasil diperbarui.');
        }
    
        return redirect()->route('admin.laporan')->with('success', 'Status berhasil diperbarui.');
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
        // Langsung buat DocumentReference tanpa findReportRefById (hemat 1-2 query)
        $docRef = $this->firestore->collection('report')->document($id);
        $since = request()->query('since');
        $collection = $docRef->collection('chat');

        // Konversi since ke integer jika ada
        if ($since) {
            $since = intval($since); // timestamp millis
            if ($since > 0) {
                // Pesan dari USER (mobile) mungkin tidak punya lastActionAt,
                // hanya createdAt. Kita perlu 2 query dan gabungkan hasilnya.

                // Query 1: Pesan yang diubah/dihapus/dibuat oleh admin (punya lastActionAt)
                $q1 = $collection
                    ->where('lastActionAt', '>', $since)
                    ->orderBy('lastActionAt')
                    ->limit(200);
                $docs1 = $q1->documents();

                // Query 2: Pesan baru (termasuk dari user) berdasarkan createdAt
                $q2 = $collection
                    ->where('createdAt', '>', $since)
                    ->orderBy('createdAt')
                    ->limit(200);
                $docs2 = $q2->documents();

                // Gabungkan dan deduplicate berdasarkan document ID
                $merged = [];
                foreach ($docs1 as $doc) {
                    if ($doc->exists()) $merged[$doc->id()] = $doc;
                }
                foreach ($docs2 as $doc) {
                    if ($doc->exists() && !isset($merged[$doc->id()])) {
                        $merged[$doc->id()] = $doc;
                    }
                }
                $documents = array_values($merged);
            } else {
                $documents = $collection
                    ->orderBy('createdAt')
                    ->documents();
            }
        } else {
            // Ambil SEMUA pesan untuk menampilkan seluruh histori
            $documents = $collection
                ->orderBy('createdAt')
                ->documents();
        }

        $messages = [];
        foreach ($documents as $chatDoc) {
            // Handle both DocumentSnapshot (from Firestore query) and raw objects
            if (is_object($chatDoc) && method_exists($chatDoc, 'exists')) {
                if (!$chatDoc->exists()) continue;
                $data = $chatDoc->data();
                $messages[] = array_merge($data, [
                    'chatId' => $chatDoc->id(),
                ]);
            }
        }

        // Sort via PHP untuk konsistensi (terutama jika query campuran)
        usort($messages, function ($a, $b) {
            $ta = $a['createdAt'] ?? 0;
            $tb = $b['createdAt'] ?? 0;
            return $ta <=> $tb;
        });

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }

    public function sendChat(Request $request, $id)
    {
        $request->validate([
            'textMessage' => 'nullable|string',
            'imageFile'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120', // max 5MB
        ]);

        // Harus ada minimal teks atau gambar
        if (!$request->input('textMessage') && !$request->hasFile('imageFile')) {
            return response()->json(['status' => 'error', 'message' => 'Pesan atau gambar wajib diisi'], 422);
        }

        // Langsung buat DocumentReference tanpa findReportRefById (hemat 1-2 query)
        $docRef = $this->firestore->collection('report')->document($id);
        $nowMillis = round(microtime(true) * 1000);

        // Gunakan admin uid dari session, tidak perlu snapshot report hanya untuk user_id
        $userId = Session::get('admin.uid') ?? 'admin';

        // Tetap pakai nilai 'ADMIN' untuk chatType seperti diminta
        $chatType = 'ADMIN';

        // Upload gambar jika ada
        $imageUrl = null;
        if ($request->hasFile('imageFile')) {
            try {
                $image = $request->file('imageFile');
                $folder = 'images/chats/' . $id . '/' . now()->format('Ymd');
                $filename = $folder . '/' . Str::random(20) . '.' . $image->getClientOriginalExtension();

                $bucket = $this->storage->getBucket();
                $object = $bucket->upload(
                    fopen($image->getRealPath(), 'r'),
                    ['name' => $filename]
                );

                // Generate signed URL (1 tahun)
                $expiresAt = new \DateTime('now + 1 year');
                $imageUrl = $object->signedUrl($expiresAt);
            } catch (\Exception $e) {
                \Log::error('Chat image upload error: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Gagal mengupload gambar'], 500);
            }
        }

        // Buat document reference dulu agar chatId langsung tersedia (hemat 1 write)
        $newDocRef = $docRef->collection('chat')->newDocument();
        $chatId = $newDocRef->id();

        $payload = [
            'chatId' => $chatId,
            'textMessage' => $request->input('textMessage') ?? '',
            'userId' => $userId,
            'createdAt' => $nowMillis,
            'lastActionAt' => $nowMillis,
            'dayMessage' => Carbon::now()->format('Y-m-d'),
            'imageMessage' => $imageUrl,
            'messageStatus' => 'terkirim',
            'reportId' => $id,
            'chatType' => $chatType,
        ];

        // Satu kali write saja (sebelumnya: add + update chatId = 2 writes)
        $newDocRef->set($payload);

        // Invalidate unread chats cache karena admin mengirim pesan
        Cache::forget('unread_chats');

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

        // Soft delete
        $nowMillis = round(microtime(true) * 1000);
        $messageRef->update([
            ['path' => 'isDeleted', 'value' => true],
            ['path' => 'lastActionAt', 'value' => $nowMillis]
        ]);
        return response()->json(['status' => 'success']);
    }

    public function updateChat(Request $request, $id, $messageId)
    {
        $request->validate([
            'textMessage' => 'required|string'
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

        $nowMillis = round(microtime(true) * 1000);
        $messageRef->update([
            ['path' => 'textMessage', 'value' => $request->input('textMessage')],
            ['path' => 'messageStatus', 'value' => 'teredit'],
            ['path' => 'lastActionAt', 'value' => $nowMillis]
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Get unread chat notifications across all reports.
     * Returns reports that have user messages newer than admin's lastReadAt.
     *
     * OPTIMIZED: Uses Cache::remember (30s) and reuses getLaporanList data
     * to avoid redundant findReportRefById + snapshot queries per report.
     */
    public function unreadChats()
    {
        try {
            $result = Cache::remember('unread_chats', 30, function () {
                $laporan = $this->getLaporanList();
                $unread = [];

                foreach ($laporan as $item) {
                    $reportId = $item['id'] ?? null;
                    if (!$reportId) continue;

                    // adminLastReadAt sudah tersedia dari getLaporanList — tidak perlu query lagi
                    $lastReadAt = $item['adminLastReadAt'] ?? 0;

                    // Buat DocumentReference langsung tanpa findReportRefById
                    $docRef = $this->firestore->collection('report')->document($reportId);

                    // Query chat subcollection: pesan setelah lastReadAt
                    $chatCollection = $docRef->collection('chat');
                    $query = $chatCollection
                        ->where('createdAt', '>', $lastReadAt)
                        ->orderBy('createdAt', 'DESC')
                        ->limit(50);

                    $docs = $query->documents();
                    $unreadCount = 0;
                    $lastUserMessage = null;

                    foreach ($docs as $chatDoc) {
                        if (!$chatDoc->exists()) continue;
                        $data = $chatDoc->data();

                        // Skip deleted messages
                        if (!empty($data['isDeleted'])) continue;

                        // Only count non-admin messages
                        $chatType = $data['chatType'] ?? '';
                        if (strtoupper($chatType) === 'ADMIN') continue;

                        $unreadCount++;
                        if (!$lastUserMessage) {
                            $lastUserMessage = $data['textMessage'] ?? '';
                            if ($data['imageMessage'] ?? null) {
                                $lastUserMessage = $lastUserMessage ?: '📷 Gambar';
                            }
                        }
                    }

                    if ($unreadCount > 0) {
                        $unread[] = [
                            'reportId' => $reportId,
                            'reportTitle' => $item['judul'] ?? 'Laporan',
                            'userName' => $item['user_name'] ?? ($item['nama'] ?? 'User'),
                            'lastMessage' => $lastUserMessage,
                            'unreadCount' => $unreadCount,
                        ];
                    }
                }

                return [
                    'status' => 'success',
                    'unread' => $unread,
                    'totalUnread' => array_sum(array_column($unread, 'unreadCount')),
                ];
            });

            return response()->json($result);
        } catch (\Throwable $e) {
            \Log::error('unreadChats error: ' . $e->getMessage());
            return response()->json([
                'status' => 'success',
                'unread' => [],
                'totalUnread' => 0,
            ]);
        }
    }

    /**
     * Mark chat as read by updating adminLastReadAt timestamp on the report.
     */
    public function markChatRead($id)
    {
        $found = $this->findReportRefById($id);
        if (!$found) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        [, , $docRef] = $found;
        $nowMillis = round(microtime(true) * 1000);

        try {
            $docRef->update([
                ['path' => 'adminLastReadAt', 'value' => $nowMillis],
            ]);

            // Invalidate unread chats cache karena admin sudah membaca
            Cache::forget('unread_chats');
        } catch (\Throwable $e) {
            \Log::error('markChatRead error: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success']);
    }
}
