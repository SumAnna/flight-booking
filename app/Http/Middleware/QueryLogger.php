<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QueryLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        DB::enableQueryLog();

        $response = $next($request);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        Log::info('Number of queries executed: ' . $queryCount);

        return $response;
    }
}
