<?php

namespace App\Http\Middleware;

use Closure;

class WebUiFeatureAvailability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $options
     * @return mixed
     */
    public function handle($request, Closure $next, $options)
    {
        if (config('fileproxy.' . $options, false) === false) {
            abort(404);
        }

        return $next($request);
    }
}
