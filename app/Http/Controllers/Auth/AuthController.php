<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\Logger;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Display login page.
     */
    public function showLoginForm(): View
    {
        Logger::logAuth('form_viewed', [], request());

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Log login attempt
        Logger::logAuth('attempt', [
            'email' => $request->email,
            'remember' => $request->boolean('remember'),
        ], $request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log successful login
            Logger::logAuth('success', [
                'email' => $request->email,
                'remember' => $request->boolean('remember'),
            ], $request);

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => '/dashboard'
                ]);
            }

            // redirect url to /dashboard
            return redirect('/dashboard');
        }

        // Log failed login attempt
        Logger::logAuth('failed', [
            'email' => $request->email,
            'reason' => 'invalid_credentials',
        ], $request);

        // Check if request expects JSON (AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => 'The provided credentials do not match our records.'
                ]
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display registration page.
     */
    public function showRegisterForm(): View
    {
        Logger::logAuth('register_form_viewed', [], request());

        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        // Log registration attempt
        Logger::logAuth('register_attempt', [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ], $request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // Assign participant role
        $user->assignRole('participant');

        // Log user creation
        Logger::logDb('create', 'User', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => 'participant',
        ]);

        Auth::login($user);

        // Log successful registration
        Logger::logAuth('register_success', [
            'user_id' => $user->id,
            'email' => $user->email,
        ], $request);

        // redirect url to /dashboard
        return redirect('/dashboard');
    }

    /**
     * Display user dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        Logger::log('info', 'dashboard_viewed', [
            'user_id' => $user->id,
            'email' => $user->email,
        ], request());

        return view('auth.dashboard', compact('user'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        $userId = auth()->id();
        $userEmail = auth()->user()?->email;

        // Log logout attempt
        Logger::logAuth('logout_attempt', [
            'user_id' => $userId,
            'email' => $userEmail,
        ], $request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log successful logout
        Logger::logAuth('logout_success', [
            'user_id' => $userId,
            'email' => $userEmail,
        ], $request);

        // redirect url to /login
        return redirect('/login');
    }
}
