<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateFlightId
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string  $parameter
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $parameter = 'id'): mixed
    {
        $validator = Validator::make([$parameter => $request->route($parameter)], [
            $parameter => 'required|integer|exists:flights,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        return $next($request);
    }
}



