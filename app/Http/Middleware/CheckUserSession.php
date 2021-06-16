<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserSession
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
        if (
            !$request->session()->has('user_initial_name') 
            || !$request->session()->has('user_id') 
            || !$request->session()->has('user_code') 
            || !$request->session()->has('user_name') 
            || !$request->session()->has('user_role') 
            || !$request->session()->has('user_full_name') 
            // || !$request->session()->has('company_code') 
            // || !$request->session()->has('company_name') 
            // || !$request->session()->has('brand_code') 
            // || !$request->session()->has('brand_name') 
            // || !$request->session()->has('brand_type')
            ) {
            return redirect("/");
        }
        return $next($request);
    }
}
