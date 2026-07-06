<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

 public function store(Request $request)
{
    Log::info('Login attempt', $request->only('email'));
    
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (!Auth::guard()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        Log::warning('Failed login attempt for email: ' . $request->email);
        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }
    
    $request->session()->regenerate();

    $user = Auth::user();
    
    // Load the role relationship if not already loaded
    if (!$user->relationLoaded('assignedRole')) {
        $user->load('assignedRole');
    }

    // Debug logging
    Log::info('User authenticated', [
        'user_id' => $user->id,
        'email' => $user->email,
        'role' => $user->assignedRole ? $user->assignedRole->name : 'No role'
    ]);
    
    if ($user->assignedRole) {
        Log::info($user->assignedRole->name);
    }

    // Redirect based on role - DON'T use intended() for role-based redirects
    if ($user->assignedRole && $user->assignedRole->name === 'auditor') {
        Log::info('Redirecting auditor to auditor dashboard');
        return redirect()->route('auditor.dashboard'); // Remove intended()
    }

    Log::info('Redirecting user to regular dashboard');
    return redirect()->route('dashboard'); // Remove intended()
}
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}