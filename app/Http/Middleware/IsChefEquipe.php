<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsChefEquipe
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'chef_equipe') {
            return $next($request);
        }
        
        return redirect('/login')->with('error', 'Accès réservé aux chefs d\'équipe');
    }
}