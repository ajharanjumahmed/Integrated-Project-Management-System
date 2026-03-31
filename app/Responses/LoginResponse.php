<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user(); 

        if (! $user) {
            return redirect('/login');
        }

        $role = optional($user->role)->name; 

        return match ($role) {
            'CEO' => redirect('/ceo-dashboard'),
            'Project Manager' => redirect('/manager-dashboard'),
            'Designer', 'Developer' => redirect('/tasks'),
            default => redirect('/dashboard'),
        };
    }
}