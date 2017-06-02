<?php

namespace App\Http\Middleware;

use Closure;

class TokenValidation
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/health'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->inExceptArray($request)
            && $this->hasSecretToken()
            && $request->header($this->secretTokenName()) !== $this->secretToken()
        ) {
            abort(401, 'Token missing');
        }

        return $next($request);
    }

    /**
     * do we have a secret token configured?
     *
     * @return bool
     */
    private function hasSecretToken() : bool
    {
        return config('fileproxy.api.secret_token', null) !== null;
    }

    /**
     * returns secret token.
     *
     * @return string
     */
    private function secretToken() : string
    {
        return config('fileproxy.api.secret_token', '');
    }

    /**
     * returns secret token name.
     *
     * @return string
     */
    private function secretTokenName() : string
    {
        $tokenName = config('fileproxy.api.token_name', null);

        return $tokenName ?? 'X-FILEPROXY-TOKEN';
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
