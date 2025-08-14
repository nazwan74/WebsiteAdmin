<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->auth = $factory->createAuth();
        $this->firestore = $factory->createFirestore()->database();
    }

    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($request->email, $request->password);
            $firebaseUser = $this->auth->getUserByEmail($request->email);
            $uid = $firebaseUser->uid;

            // Cek di Firestore collection 'admins'
            $adminDoc = $this->firestore->collection('admins')->document($uid)->snapshot();

            if (!$adminDoc->exists()) {
                return back()->withErrors(['error' => 'Akun ini tidak memiliki izin sebagai admin.']);
            }

            $adminData = $adminDoc->data();

            Session::put('admin', [
                'uid' => $uid,
                'email' => $request->email,
                'role' => $adminData['role'] ?? 'admin',
            ]);

            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Login gagal. Periksa email dan password.']);
        }
    }

    public function logout()
    {
        Session::forget('admin');
        return redirect()->route('admin.login');
    }

    // Menampilkan halaman lupa password
    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    // Mengirim email reset password
    public function sendPasswordResetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Cek apakah email ada di collection admins
            $admins = $this->firestore->collection('admins')->documents();
            $emailExists = false;
            
            foreach ($admins as $admin) {
                $adminData = $admin->data();
                if ($adminData['email'] === $request->email) {
                    $emailExists = true;
                    break;
                }
            }

            if (!$emailExists) {
                return back()->withErrors(['email' => 'Email tidak terdaftar sebagai admin.']);
            }

            // Kirim email reset password menggunakan Firebase
            $this->auth->sendPasswordResetLink($request->email);

            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email reset password. Silakan coba lagi.']);
        }
    }

    // Menampilkan halaman reset password
    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        $oobCode = $request->query('oobCode');
        
        return view('admin.reset-password', compact('email', 'oobCode'));
    }

    // Memproses reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'oobCode' => 'required',
        ]);

        try {
            // Reset password menggunakan Firebase
            $this->auth->confirmPasswordReset($request->oobCode, $request->password);

            return redirect()->route('admin.login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Gagal mengubah password. Link mungkin sudah kadaluarsa atau tidak valid.']);
        }
    }
}
