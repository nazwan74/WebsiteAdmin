<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;


class laporancontroller extends Controller
{
    protected $firestore;

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
        // Ambil data laporan dari Firestore dan urutkan berdasarkan create_at (bukan created_at)
        $documents = $this->firestore
            ->collection('laporan')
            ->orderBy('create_at', 'DESC')
            ->documents();

        $laporan = [];
        foreach ($documents as $doc) {
            if ($doc->exists()) {
                $data = $doc->data();
                $data['id'] = $doc->id(); // simpan id dokumen
                $laporan[] = $data;
            }
        }

        return view('admin.laporan', compact('laporan'));
    }

    public function detail($id)
    {
        $doc = $this->firestore->collection('laporan')->document($id)->snapshot();
    
        if (!$doc->exists()) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }
    
        $laporan = $doc->data();
        $laporan['id'] = $doc->id();
    
        return view('admin.laporan-detail', compact('laporan'));
    }
    
    public function setStatus(Request $request, $id)
    {
        $status = $request->input('status');
    
        $this->firestore->collection('laporan')->document($id)->update([
            ['path' => 'status', 'value' => $status]
        ]);
    
        return redirect()->route('admin.laporan.detail', ['id' => $id])->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Hapus laporan dari Firestore
        $this->firestore->collection('laporan')->document($id)->delete();

        return redirect()->route('admin.laporan')->with('success', 'Laporan berhasil dihapus.');
    }


    public function downloadPDF($id)
    {
        $doc = $this->firestore->collection('laporan')->document($id)->snapshot();

        if (!$doc->exists()) {
            return redirect()->route('admin.laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $laporan = $doc->data();
        $laporan['id'] = $doc->id();

        $pdf = Pdf::loadView('admin.laporan-pdf', compact('laporan'));
        return $pdf->download('laporan-'.$id.'.pdf');
    }


}
