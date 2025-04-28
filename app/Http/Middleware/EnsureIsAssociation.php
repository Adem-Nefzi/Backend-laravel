<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsAssociation
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() instanceof \App\Models\Association) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
