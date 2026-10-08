<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login screen.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Set active role based on user record
            session(['current_role' => $user->role ?? 'doctor']);

            $request->session()->regenerate();

            AuditLog::record('User Login', 'User', (string) $user->id, "User {$user->name} ({$user->email}) logged in successfully.");

            return redirect()->intended(route('dashboard'))
                ->with('success', "Welcome back, {$user->name}!");
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Invalid email or password. Please verify your credentials.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record('User Logout', 'User', (string) Auth::id(), "User " . Auth::user()->name . " logged out.");
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
