<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    // Show login page
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    // Handle login form submission
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectBasedOnRole(Auth::user());
    }

    // Handle logout
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Role-based redirect
    private function redirectBasedOnRole($user)
    {
        if ($user->hasRole('CEO')) {
            return redirect()->route('ceoDashboard');
        }

        if ($user->hasRole('Project Manager')) {
            return redirect('/manager-dashboard');
        }

        if ($user->hasAnyRole(['Designer', 'Developer'])) {
            return redirect('/tasks');
        }

        // Fallback
        return redirect('/');
    }
}