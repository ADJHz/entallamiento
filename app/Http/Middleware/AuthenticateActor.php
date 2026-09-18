<?php

namespace App\Http\Middleware;

use App\Models\Elemento;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateActor
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasElemento = false;

        if (! Auth::guard('web')->check()) {
            $elementoId = $request->session()->get('elemento_auth_id');
            $elemento = is_numeric($elementoId)
                ? Elemento::query()->find((int) $elementoId)
                : null;

            if ($elemento) {
                $hasElemento = true;
                $request->setUserResolver(fn (): Elemento => $elemento);
            }
        }

        return Auth::guard('web')->check() || $hasElemento
            ? $next($request)
            : redirect()->route('login');
    }
}
