<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class articlecontroller extends Controller
{
    protected $firestore;
    protected $storage;
    protected $allowedTypes = ['stunting', 'bullying', 'pernikahan dini', 'kekerasan anak'];

    public function __construct()
    {
        // Jika belum login, redirect ke login
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send(); 
        }

        // Cek apakah admin_pengaduan mencoba akses modul artikel
        if (Session::get('admin.role') === 'admin_pengaduan') {
            redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke fitur artikel.')->send();
        }
        
        // Menggunakan singleton dari FirebaseServiceProvider
        $this->firestore = app('firebase.firestore');
        $this->storage = app('firebase.storage');
    }

    /**
     * Helper to parse Firestore dates consistently.
     */
    private function parseArticleDate($value)
    {
        if (!$value) return '-';

        try {
            // Handle primitive integer (milliseconds)
            if (is_numeric($value) && !($value instanceof Timestamp)) {
                return \Carbon\Carbon::createFromTimestampMs($value)
                    ->setTimezone(config('app.timezone', 'Asia/Jakarta'))
                    ->format('Y-m-d H:i');
            }

            // Handle Firestore Timestamp
            if ($value instanceof Timestamp) {
                $value = $value->get();
            }

            return \Carbon\Carbon::instance($value)
                ->setTimezone(config('app.timezone', 'Asia/Jakarta'))
                ->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    private function getArticlesList()
    {
        // Durasi cache diperpendek ke 60 detik agar UX Admin lebih real-time
        return Cache::remember('articles_list_data', 3600, function () {
            $documents = $this->firestore->collection('articles')
                ->orderBy('releasedDate', 'DESC')
                ->limit(200)
                ->documents();
                
            $articles = [];
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $data['id'] = $doc->id();
                    $articles[] = $data;
                }
            }
            return $articles;
        });
    }

    public function index()
    {
        $articles = $this->getArticlesList();
        return view('admin.articel', compact('articles'));
    }

    public function refresh()
    {
        Cache::forget('articles_list_data');
        return redirect()->route('admin.articel.index')->with('success', 'Data artikel berhasil diperbarui.');
    }

    /**
     * Download daftar artikel dalam format CSV.
     * Query: kategori (articleType), search (judul/deskripsi/hashtag).
     */
    public function downloadList(Request $request)
    {
        $articles = $this->getArticlesList();

        $kategori = $request->query('kategori');
        if ($kategori !== null && $kategori !== '' && $kategori !== 'all') {
            $articles = array_values(array_filter($articles, function ($item) use ($kategori) {
                return ($item['articleType'] ?? '') === $kategori;
            }));
        }

        $search = $request->query('search');
        if ($search !== null && $search !== '') {
            $searchLower = mb_strtolower(trim($search));
            $articles = array_values(array_filter($articles, function ($item) use ($searchLower) {
                $title = mb_strtolower($item['title'] ?? '');
                $desc = mb_strtolower($item['description'] ?? '');
                return mb_strpos($title, $searchLower) !== false
                    || mb_strpos($desc, $searchLower) !== false;
            }));
        }

        $filename = 'daftar-artikel-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($articles) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($stream, [
                'No',
                'ID',
                'Judul Artikel',
                'Kategori',
                'Tanggal Rilis',
                'Tanggal Edit',
            ], ';');

            foreach ($articles as $i => $item) {
                $released = $this->parseArticleDate($item['releasedDate'] ?? null);
                $updated = $this->parseArticleDate($item['updateDate'] ?? null);
                
                fputcsv($stream, [
                    $i + 1,
                    $item['id'] ?? '-',
                    $item['title'] ?? '-',
                    $item['articleType'] ?? '-',
                    $released,
                    $updated,
                ], ';');
            }
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit($id)
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        $snapshot = $this->firestore->collection('articles')->document($id)->snapshot();
        
        if (!$snapshot->exists()) {
            return redirect()->route('admin.articel.index')->with('error', 'Artikel tidak ditemukan!');
        }
        
        $articleData = $snapshot->data();

        // Regenerate Signed URL (7 hari) jika ada gsUrl
        if (isset($articleData['gsUrl']) && strpos($articleData['gsUrl'], 'gs://') === 0) {
            try {
                $path = ltrim(parse_url($articleData['gsUrl'], PHP_URL_PATH), '/');
                $bucket = $this->storage->getBucket();
                $object = $bucket->object($path);
                
                if ($object->exists()) {
                    $expiresAt = new \DateTime('now + 7 days');
                    $articleData['photoUrl'] = $object->signedUrl($expiresAt);
                }
            } catch (\Throwable $e) {
                \Log::error('Signed URL Regen Error: ' . $e->getMessage());
            }
        }
        
        $articleData['gambar_url'] = $articleData['photoUrl'] ?? null;
        
        return view('admin.edit', [
            'articleData' => array_merge($articleData, ['id' => $id])
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        $request->validate([
            'title' => 'required|string',
            'articleType' => 'required|in:' . implode(',', $this->allowedTypes),
            'description' => 'required|string',
            'photoUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $articleRef = $this->firestore->collection('articles')->document($id);
        $snapshot = $articleRef->snapshot();

        if (!$snapshot->exists()) {
            return redirect()->route('admin.articel.index')->with('error', 'Artikel tidak ditemukan!');
        }

        $oldData = $snapshot->data();
        $updateData = [
            'title' => $request->title,
            'articleType' => $request->articleType,
            'description' => $request->description,
            'updateDate' => round(microtime(true) * 1000),
        ];

        $bucket = $this->storage->getBucket();

        if ($request->hasFile('photoUrl')) {
            try {
                // Hapus gambar lama
                if (isset($oldData['gsUrl']) && strpos($oldData['gsUrl'], 'gs://') === 0) {
                    $oldPath = ltrim(parse_url($oldData['gsUrl'], PHP_URL_PATH), '/');
                    $oldObject = $bucket->object($oldPath);
                    if ($oldObject->exists()) $oldObject->delete();
                }

                // Upload baru
                $image = $request->file('photoUrl');
                $folder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
                $filename = $folder . '/' . Str::random(20) . '.' . $image->getClientOriginalExtension();

                $object = $bucket->upload(fopen($image->getRealPath(), 'r'), ['name' => $filename]);
                $updateData['photoUrl'] = $object->signedUrl(new \DateTime('now + 7 days'));
                $updateData['gsUrl'] = 'gs://' . $bucket->name() . '/' . $filename;

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
            }
        } 
        else if ($request->articleType !== $oldData['articleType']) {
            // Jika kategori berubah, pindahkan gambar di Storage
            if (isset($oldData['gsUrl']) && strpos($oldData['gsUrl'], 'gs://') === 0) {
                try {
                    $oldPath = ltrim(parse_url($oldData['gsUrl'], PHP_URL_PATH), '/');
                    $newFolder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
                    $newPath = $newFolder . '/' . basename($oldPath);

                    $object = $bucket->object($oldPath);
                    if ($object->exists()) {
                        $newObject = $object->copy($bucket, ['name' => $newPath]);
                        $object->delete();
                        $updateData['gsUrl'] = 'gs://' . $bucket->name() . '/' . $newPath;
                        $updateData['photoUrl'] = $newObject->signedUrl(new \DateTime('now + 7 days'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Move image error: ' . $e->getMessage());
                }
            }
        } 

        $articleRef->set($updateData, ['merge' => true]);
        Cache::forget('articles_list_data');

        return redirect()->route('admin.articel.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        try {
            $articleRef = $this->firestore->collection('articles')->document($id);
            $articleSnapshot = $articleRef->snapshot();
    
            if ($articleSnapshot->exists()) {
                $articleData = $articleSnapshot->data();
    
                if (isset($articleData['gsUrl']) && strpos($articleData['gsUrl'], 'gs://') === 0) {
                    $path = ltrim(parse_url($articleData['gsUrl'], PHP_URL_PATH), '/');
                    $bucket = $this->storage->getBucket();
                    $object = $bucket->object($path);
                    if ($object->exists()) $object->delete();
                }
    
                $articleRef->delete();
                Cache::forget('articles_list_data');
            }
    
            return redirect()->route('admin.articel.index')->with('success', 'Artikel dan gambar berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus artikel: ' . $e->getMessage());
        }
    }
    
    public function bulkDestroy(Request $request)
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada artikel yang dipilih.');
        }

        try {
            $bucket = $this->storage->getBucket();
            $deletedCount = 0;

            foreach ($ids as $id) {
                $articleRef = $this->firestore->collection('articles')->document($id);
                $snapshot = $articleRef->snapshot();

                if ($snapshot->exists()) {
                    $articleData = $snapshot->data();

                    // Hapus gambar dari Storage jika ada
                    if (isset($articleData['gsUrl']) && strpos($articleData['gsUrl'], 'gs://') === 0) {
                        try {
                            $path = ltrim(parse_url($articleData['gsUrl'], PHP_URL_PATH), '/');
                            $object = $bucket->object($path);
                            if ($object->exists()) $object->delete();
                        } catch (\Exception $e) {
                            \Log::error("Bulk Delete Storage Error ($id): " . $e->getMessage());
                        }
                    }

                    // Hapus dari Firestore
                    $articleRef->delete();
                    $deletedCount++;
                }
            }

            Cache::forget('articles_list_data');

            return redirect()->route('admin.articel.index')->with('success', "$deletedCount artikel berhasil dihapus massal.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat penghapusan massal: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        return view('admin.create_article');
    }

    public function store(Request $request)
    {
        if (Session::get('admin.role') === 'super_admin') {
            return redirect()->route('admin.articel.index')->with('error', 'Super Admin hanya memiliki akses pantau (read-only) untuk artikel.');
        }

        $request->validate([
            'title' => 'required|string',
            'articleType' => 'required|in:' . implode(',', $this->allowedTypes),
            'description' => 'required|string',
            'photoUrl' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $request->file('photoUrl');
        $folder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
        $filename = $folder . '/' . Str::random(20) . '.' . $image->getClientOriginalExtension();
        
        try {
            $bucket = $this->storage->getBucket();
            $object = $bucket->upload(
                fopen($image->getRealPath(), 'r'),
                ['name' => $filename]
            );
            $expiresAt = new \DateTime('now + 1 year');
            $signedUrl = $object->signedUrl($expiresAt);
            
            $gsUrl = 'gs://' . $bucket->name() . '/' . $filename;
            
            $this->firestore->collection('articles')->add([
                'title' => $request->title,
                'articleType' => $request->articleType,
                'description' => $request->description,
                'photoUrl' => $signedUrl,
                'gsUrl' => $gsUrl,
                'releasedDate' => round(microtime(true) * 1000),
            ]);

            Cache::forget('articles_list_data');

            return redirect()->route('admin.articel.index')->with('success', 'Artikel berhasil disimpan!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('Upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
        }
    }
}