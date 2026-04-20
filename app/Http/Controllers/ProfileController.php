<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        if (!Session::has('admin')) {
            redirect()->route('admin.login')->send();
        }
        
        // Menggunakan singleton dari FirebaseServiceProvider
        $this->auth = app('firebase.auth');
        $this->firestore = app('firebase.firestore');
    }

    public function index()
    {
        $adminUid = Session::get('admin.uid');
        
        // Cache data profil admin ini selama 30 menit
        $cacheKey = 'admin_profile_' . $adminUid;
        $admin = Cache::remember($cacheKey, 1800, function () use ($adminUid) {
            $snapshot = $this->firestore->collection('admins')->document($adminUid)->snapshot();
            return $snapshot->exists() ? $snapshot->data() : null;
        });
        
        if (!$admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('admin.profile', compact('admin'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required'
        ], [
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password_confirmation.required' => 'Konfirmasi password baru harus diisi'
        ]);

        try {
            $adminUid = Session::get('admin.uid');
            
            // Verifikasi password lama
            try {
                $signInResult = $this->auth->signInWithEmailAndPassword(
                    Session::get('admin.email'),
                    $request->current_password
                );
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah']);
            }

            // Update password
            $this->auth->updateUser($adminUid, [
                'password' => $request->new_password
            ]);

            // Invalidate caches
            Cache::forget('admin_profile_' . $adminUid);
            Cache::forget('admin_list_data');

            return redirect()->route('admin.profile')->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'password-mismatch')) {
                return redirect()->back()->withErrors(['new_password' => 'Konfirmasi password baru tidak cocok']);
            }
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui password: ' . $e->getMessage()]);
        }
    }
} 