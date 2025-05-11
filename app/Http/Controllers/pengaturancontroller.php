<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;

class PengaturanController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send();
        }
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->auth = $factory->createAuth();
        $this->firestore = $factory->createFirestore()->database();
    }

    public function index()
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses.'
                ]);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        // Ambil semua dokumen dari koleksi 'admins'
        $adminSnapshots = $this->firestore->collection('admins')->documents();
        $admins = [];

        foreach ($adminSnapshots as $doc) {
            if ($doc->exists()) {
                // Tambahkan UID (ID dokumen) ke dalam array admin
                $admins[] = array_merge($doc->data(), ['uid' => $doc->id()]);
            }
        }

        return view('admin.pengaturan', compact('admins'));
    }


    public function formTambahAdmin()
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        return view('admin.formTambahAdmin');
    }

    public function hapusAdmin($uid)
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            // Ambil data admin dari Firestore berdasarkan UID
            $adminDoc = $this->firestore->collection('admins')->document($uid)->snapshot();

            if (!$adminDoc->exists()) {
                return redirect()->route('admin.pengaturan')->withErrors(['error' => 'Admin tidak ditemukan.']);
            }

            // Cek apakah role-nya super_admin
            $adminData = $adminDoc->data();
            if (isset($adminData['role']) && $adminData['role'] === 'super_admin') {
                return redirect()->route('admin.pengaturan')->withErrors(['error' => 'Akun super_admin tidak bisa dihapus.']);
            }

            // Hapus user dari Firebase Authentication
            $this->auth->deleteUser($uid);

            // Hapus data user dari Firestore
            $this->firestore->collection('admins')->document($uid)->delete();

            return redirect()->route('admin.pengaturan')->with('success', 'Admin berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pengaturan')->withErrors(['error' => 'Gagal menghapus admin: ' . $e->getMessage()]);
        }
    }



    public function tambahAdmin(Request $request)
    {
        if (!Session::has('admin') || Session::get('admin.role') !== 'super_admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,super_admin',
        ]);

        try {
            $user = $this->auth->createUser([
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $this->firestore->collection('admins')->document($user->uid)->set([
                'email' => $request->email,
                'role' => $request->role,
                'created_at' => now()->toDateTimeString(),
            ]);

            return redirect()->route('admin.pengaturan')->with('success', 'Admin berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan admin: ' . $e->getMessage()]);
        }
    }
}
