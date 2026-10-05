<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('admin*') && !$request->ajax()) {
            $ip = $request->ip();
            $today = Carbon::today()->toDateString();
            $sessionKey = 'visited_' . $today;

            if (!session()->has($sessionKey)) {
                DB::table('visitor_logs')->insert([
                    'ip_address'   => $ip,
                    'user_agent'   => $request->userAgent(),
                    'visited_page' => '/' . ltrim($request->path(), '/'),
                    'visited_at'   => now(),
                ]);

                session()->put($sessionKey, true);
            }
        }

        return $next($request);
    }
}