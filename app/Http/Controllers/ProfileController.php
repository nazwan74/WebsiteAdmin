<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
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
        $adminUid = Session::get('admin.uid');
        $adminData = $this->firestore->collection('admins')->document($adminUid)->snapshot();
        
        return view('admin.profile', [
            'admin' => $adminData->data()
        ]);
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

            return redirect()->route('admin.profile')->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'password-mismatch')) {
                return redirect()->back()->withErrors(['new_password' => 'Konfirmasi password baru tidak cocok']);
            }
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui password: ' . $e->getMessage()]);
        }
    }
} 