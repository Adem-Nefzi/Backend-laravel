<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAssociationAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $association = $request->route('association');

        if (!$request->user()->associations()
            ->where('association_id', $association->id)
            ->where('role', 'admin')
            ->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
