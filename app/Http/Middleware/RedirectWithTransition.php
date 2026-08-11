<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectWithTransition
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Check if this is a redirect response
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            // Store the redirect target in session to trigger smooth transition on next page
            Session::flash('selfcheq_smooth_redirect', true);
        }
        
        return $response;
    }
}