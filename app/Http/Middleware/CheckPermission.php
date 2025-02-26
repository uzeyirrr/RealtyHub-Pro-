<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * İstek işlenmeden önce izin kontrolü yapar.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu işlem için yetkiniz bulunmuyor.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Bu işlem için yetkiniz bulunmuyor.');
        }

        return $next($request);
    }
} 