<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AssociationOwnerOrAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $association = $request->route('association');

        // Allow if user is admin or the owner of the association
        if ($user->user_type === 'admin' || $user->id == $association->user_id) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
