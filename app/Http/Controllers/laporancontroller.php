<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;


class laporancontroller extends Controller
{
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

    public function index()
    {
        // Struktur baru: laporan/{kategoriDoc}/{userIdSubcollection}/{laporanId}
        $laporan = [];

        foreach ($this->kategoriMap as $kategoriKey => $kategoriDisplay) {
            $kategoriDocRef = $this->firestore->collection('laporan')->document($kategoriKey);

            // Iterasi semua sub-koleksi (dinamis: nama = userId)
            foreach ($kategoriDocRef->collections() as $userCollection) {
                foreach ($userCollection->documents() as $doc) {
                    if (!$doc->exists()) {
                        continue;
                    }
                    $data = $doc->data();
                    $data['id'] = $doc->id();
                    // Normalisasi field untuk kompatibilitas view lama
                    $data['kategori'] = $kategoriDisplay;
                    $data['daerah'] = $data['tempat_kejadian'] ?? ($data['daerah'] ?? '-');
                    $data['create_at'] = $data['tanggal_kejadian'] ?? ($data['create_at'] ?? null);
                    $data['judul'] = $data['jenis_kekerasan'] ?? ($data['nomor_laporan'] ?? 'Laporan');
                    $laporan[] = $data;
                }
            }
        }

        // Urutkan menurun berdasarkan create_at/tanggal_kejadian jika ada
        usort($laporan, function ($a, $b) {
            $ta = $a['create_at'] ?? '';
            $tb = $b['create_at'] ?? '';
            if ($ta === $tb) return 0;
            return $ta < $tb ? 1 : -1;
        });

        return view('admin.laporan', compact('laporan'));
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
        $data['kategori'] = $this->kategoriMap[$kategoriKey] ?? $kategoriKey;
        $data['daerah'] = $data['tempat_kejadian'] ?? ($data['daerah'] ?? '-');
        $data['create_at'] = $data['tanggal_kejadian'] ?? ($data['create_at'] ?? null);
        $data['judul'] = $data['jenis_kekerasan'] ?? ($data['nomor_laporan'] ?? 'Detail Laporan');

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

        return view('admin.laporan-detail', [
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
            ['path' => 'status', 'value' => $status]
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
                $kategoriDocRef = $this->firestore->collection('laporan')->document($kategoriKey);
                foreach ($kategoriDocRef->collections() as $userCollection) {
                    foreach ($userCollection->documents() as $doc) {
                        if (!$doc->exists()) continue;
                        $totalLaporan++;
                        $d = $doc->data();
                        $st = strtolower($d['status'] ?? 'baru');
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
        $laporan['kategori'] = $laporan['kategori'] ?? '';
        $laporan['daerah'] = $laporan['tempat_kejadian'] ?? ($laporan['daerah'] ?? '-');
        $laporan['create_at'] = $laporan['tanggal_kejadian'] ?? ($laporan['create_at'] ?? null);
        $laporan['judul'] = $laporan['jenis_kekerasan'] ?? ($laporan['nomor_laporan'] ?? 'Laporan');

        $pdf = Pdf::loadView('admin.laporan-pdf', compact('laporan'));
        return $pdf->download('laporan-'.$id.'.pdf');
    }

    private function findReportRefById(string $laporanId)
    {
        foreach ($this->kategoriMap as $kategoriKey => $kategoriDisplay) {
            $kategoriDocRef = $this->firestore->collection('laporan')->document($kategoriKey);
            foreach ($kategoriDocRef->collections() as $userCollection) {
                // Asumsikan ID dokumen = ID laporan
                $candidate = $userCollection->document($laporanId)->snapshot();
                if ($candidate->exists()) {
                    return [$kategoriKey, $userCollection->id(), $userCollection->document($laporanId)];
                }
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
        $laporan['judul'] = $laporan['jenis_kekerasan'] ?? ($laporan['nomor_laporan'] ?? 'Laporan');

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
