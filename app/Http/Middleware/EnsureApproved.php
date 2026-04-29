<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->isFarmer() && !$user->is_approved) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your farmer account is pending admin approval.']);
        }

        return $next($request);
    }
}