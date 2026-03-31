<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),

            'auth' => [
                // Load the 'role' relationship so every Vue page
                // receives auth.user.role.name automatically.
                // The ?-> means: if there's no logged-in user, return null safely.
                'user' => $request->user()?->load('role'),
            ],

            // Flash messages — we'll use these for success/error toasts later
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],

            'sidebarOpen' => ! $request->hasCookie('sidebar_state')
                || $request->cookie('sidebar_state') === 'true',
        ];
    }
}