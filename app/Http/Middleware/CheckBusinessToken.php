<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Business;

class CheckBusinessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $businessToken = session('business_token');

        if (!$businessToken) {
            abort(403, 'Business token is missing.');
        }

        if (!auth()->check()) {
            return redirect('/login');
        }

        

        return $next($request);
    
    }
}
