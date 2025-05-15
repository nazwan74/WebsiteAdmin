<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class articlecontroller extends Controller
{
    protected $firestore;
    protected $storage;

    public function __construct()
    {
        // Jika belum login, redirect ke login
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send(); 
        }
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDefaultStorageBucket(config('firebase.storage_bucket')); // ambil dari config

        $this->firestore = $factory->createFirestore()->database();
        $this->storage = $factory->createStorage();
        $bucket = $this->storage->getBucket();
    }

    // fungsi untuk menampilkan semua artikel
    // dengan urutan terbaru
    public function index()
    {
        $documents = $this->firestore->collection('articles')->orderBy('releasedDate', 'DESC')->documents();
        $articles = [];
    
        foreach ($documents as $doc) {
            if ($doc->exists()) {
                $data = $doc->data();
                $data['id'] = $doc->id();
                $articles[] = $data;
            }
        }
    
        return view('admin.articel', compact('articles'));
    }

    // fungsi untuk menampilkan artikel berdasarkan id
    public function edit($id)
    {
        // Ambil data artikel berdasarkan ID
        $article = $this->firestore->collection('articles')->document($id)->snapshot();
        
        if (!$article->exists()) {
            return redirect()->route('admin.articel.index')->with('error', 'Artikel tidak ditemukan!');
        }
        
        $articleData = $article->data();
        $articleData['id'] = $id; // Tambahkan ID untuk form update
        
        // Konversi URL gs:// ke URL publik yang dapat diakses browser
        if (isset($articleData['gsUrl']) && strpos($articleData['gsUrl'], 'gs://') === 0) {
            $bucket = $this->storage->getBucket();
            $gsPath = parse_url($articleData['gsUrl'], PHP_URL_PATH);
            $gsPath = ltrim($gsPath, '/');
            
            try {
                $object = $bucket->object($gsPath);
                if ($object->exists()) {
                    // Buat URL dengan waktu kadaluarsa (1 bulan)
                    $expiresAt = new \DateTime('now + 1 month');
                    $articleData['gambar_url'] = $object->signedUrl($expiresAt);
                }
            } catch (\Exception $e) {
                \Log::error('Error getting image URL: ' . $e->getMessage());
            }
        }
        
        return view('admin.edit', compact('articleData'));
    }

    // fungsi untuk mengupdate artikel
    // dengan validasi input
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'articleType' => 'required|in:stunting,bullying,pernikahan dini,kekerasan anak',
            'description' => 'required|string',
            'photoUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'hashtags' => 'required|string',
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
            'hashtags' => $request->hashtags,
            'updateDate' => now()->toDateTimeString(),
        ];

        $bucket = $this->storage->getBucket();

        // Jika ada gambar baru diupload
        if ($request->hasFile('photoUrl')) {
            try {
                // Hapus gambar lama kalau ada
                if (isset($oldData['gsUrl']) && strpos($oldData['gsUrl'], 'gs://') === 0) {
                    $oldPath = parse_url($oldData['gsUrl'], PHP_URL_PATH);
                    $oldPath = ltrim($oldPath, '/');
                    $oldObject = $bucket->object($oldPath);
                    if ($oldObject->exists()) {
                        $oldObject->delete();
                    }
                }

                // Upload gambar baru
                $image = $request->file('photoUrl');
                $folder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
                $filename = $folder . '/' . Str::random(20) . '.' . $image->getClientOriginalExtension();

                $bucket->upload(
                    fopen($image->getRealPath(), 'r'),
                    ['name' => $filename]
                );

                // Buat URL publik langsung (tanpa tanda tangan)
                $publicUrl = 'https://firebasestorage.googleapis.com/' . $bucket->name() . '/o' . $filename;$publicUrl = 'https://firebasestorage.googleapis.com/v0/b/' . $bucket->name() . '/o/' . urlencode($filename) . '?alt=media';
                $gsUrl = 'gs://' . $bucket->name() . '/' . $filename;
                
                $updateData['photoUrl'] = $publicUrl; // URL publik untuk aplikasi
                $updateData['gsUrl'] = $gsUrl; // URL gs:// untuk keperluan internal

            } catch (\Exception $e) {
                \Log::error('Upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
            }
        } 
        else if ($request->articleType !== $oldData['articleType']) {
            // Kalau kategori berubah tapi gambar tidak diupload
            if (isset($oldData['gsUrl']) && strpos($oldData['gsUrl'], 'gs://') === 0) {
                $oldPath = ltrim(parse_url($oldData['gsUrl'], PHP_URL_PATH), '/');
                $newFolder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
                $filename = basename($oldPath);
                $newPath = $newFolder . '/' . $filename;

                $object = $bucket->object($oldPath);
                if ($object->exists()) {
                    $object->copy($bucket, ['name' => $newPath]);
                    $object->delete();

                    // Buat URL publik baru sesuai path baru
                    $publicUrl = 'https://firebasestorage.googleapis.com/v0/b/' . $bucket->name() . '/o/' . urlencode($filename) . '?alt=media';
                    $updateData['gsUrl'] = 'gs://' . $bucket->name() . '/' . $newPath;
                    $updateData['photoUrl'] = $publicUrl;
                }
            }
        } 
        else {
            // Kalau tidak upload gambar baru DAN kategori tidak berubah
            // tetap gunakan URL lama
            if (isset($oldData['photoUrl'])) {
                $updateData['photoUrl'] = $oldData['photoUrl'];
            }
            if (isset($oldData['gsUrl'])) {
                $updateData['gsUrl'] = $oldData['gsUrl'];
            }
        }

        $articleRef->set($updateData, ['merge' => true]);

        return redirect()->route('admin.articel.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    // fungsi untuk menghapus artikel
    // dengan validasi input
    public function destroy($id)
    {
        try {
            $articleRef = $this->firestore->collection('articles')->document($id);
            $articleSnapshot = $articleRef->snapshot();
    
            if ($articleSnapshot->exists()) {
                $articleData = $articleSnapshot->data();
    
                // Hapus gambar dari Firebase Storage jika ada
                if (isset($articleData['gsUrl']) && strpos($articleData['gsUrl'], 'gs://') === 0) {
                    $gsUrl = $articleData['gsUrl'];
                    $path = parse_url($gsUrl, PHP_URL_PATH);
                    $path = ltrim($path, '/');
    
                    $bucket = $this->storage->getBucket();
                    $object = $bucket->object($path);
    
                    if ($object->exists()) {
                        $object->delete();
                    }
                }
    
                // Hapus dokumen Firestore
                $articleRef->delete();
            }
    
            return redirect()->route('admin.articel.index')->with('success', 'Artikel dan gambar berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus artikel: ' . $e->getMessage());
        }
    }
    
    // fungsi untuk menampilkan form tambah artikel
    public function create()
    {
        return view('admin.create_article');
    }

    // fungsi untuk menyimpan artikel
    // dengan validasi input
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'articleType' => 'required|in:stunting,bullying,pernikahan dini,kekerasan anak',
            'description' => 'required|string',
            'photoUrl' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'hashtags' => 'required|string',
        ]);

        $image = $request->file('photoUrl');
        $folder = 'images/articles/' . $request->articleType . '/' . now()->format('Ymd');
        $filename = $folder . '/' . Str::random(20) . '.' . $image->getClientOriginalExtension();

        try {
            // Upload ke Firebase Storage
            $bucket = $this->storage->getBucket();
            $bucket->upload(
                fopen($image->getRealPath(), 'r'),
                ['name' => $filename]
            );

            // Buat URL publik langsung (tanpa tanda tangan)
            $publicUrl = 'https://firebasestorage.googleapis.com/v0/b/' . $bucket->name() . '/o/' . urlencode($filename) . '?alt=media';
            // Simpan format gs:// untuk operasi internal
            $gsUrl = 'gs://' . $bucket->name() . '/' . $filename;

            // Simpan ke Firestore
            $this->firestore->collection('articles')->add([
                'title' => $request->title,
                'articleType' => $request->articleType,
                'description' => $request->description,
                'hashtags' => $request->hashtags,
                'photoUrl' => $publicUrl, // URL publik langsung
                'gsUrl' => $gsUrl, // URL gs:// untuk operasi internal
                'releasedDate' => now()->toDateTimeString(),
            ]);

            return redirect()->route('admin.articel.index')->with('success', 'Artikel berhasil disimpan!');
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
        }
    }
}