<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali di Dashboard Admin, ' . Auth::user()->name);
            }

            return redirect()->intended(route('home'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil! Selamat datang di KickSwift.');
    }

    /**
     * Quick demo login as Admin.
     */
    public function loginAsAdmin(): RedirectResponse
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Auth::login($admin);
            return redirect()->route('admin.dashboard')->with('success', 'Masuk sebagai Demo Admin (' . $admin->name . ')');
        }
        return back()->with('error', 'Akun admin tidak ditemukan.');
    }

    /**
     * Quick demo login as Customer.
     */
    public function loginAsCustomer(): RedirectResponse
    {
        $customer = User::where('role', 'customer')->first();
        if ($customer) {
            Auth::login($customer);
            return redirect()->route('home')->with('success', 'Masuk sebagai Demo Customer (' . $customer->name . ')');
        }
        return back()->with('error', 'Akun customer tidak ditemukan.');
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah keluar.');
    }
}
