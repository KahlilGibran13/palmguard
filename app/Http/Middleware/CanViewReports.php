<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanViewReports
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->canAccessReports()) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses untuk melihat laporan.');
    }
}