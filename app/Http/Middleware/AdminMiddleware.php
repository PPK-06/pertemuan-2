<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya user dengan role 'admin' yang boleh lewat. Middleware ini
        // dipasang bareng 'auth' di route group, jadi $request->user()
        // seharusnya selalu ada, tapi tetap dicek untuk jaga-jaga.
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
