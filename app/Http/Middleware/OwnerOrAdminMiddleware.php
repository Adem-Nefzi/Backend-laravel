<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerOrAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $requestedUser = $request->route('user');

        // Allow if user is admin or the owner of the resource
        if ($user->user_type === 'admin' || $user->id == $requestedUser) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
