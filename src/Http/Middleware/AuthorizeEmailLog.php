<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class AuthorizeEmailLog
{
    public function handle($request, Closure $next)
    {
        abort_unless(config('laravel-email-database-log-ui.enabled'), 404);
        abort_unless($request->user() && Gate::allows(config('laravel-email-database-log.gate', 'viewEmailLog')), 403);

        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }
}
