<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomValidatePostSize
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $max = 256 * 1024 * 1024; // 256MB
        if ($request->server('CONTENT_LENGTH') > $max) {
            throw new PostTooLargeException();
        }

        return $next($request);
    }
}
