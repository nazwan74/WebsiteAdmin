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
}
