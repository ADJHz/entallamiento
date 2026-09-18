<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElementoLoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'identification_type' => ['required', 'in:cuip'],
        ]);

        $elemento = Elemento::query()
            ->where('csp', trim($credentials['email']))
            ->where('cuip', trim($credentials['password']))
            ->first();

        if (! $elemento) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Las credenciales del elemento no son válidas.']);
        }

        $request->session()->regenerate();
        $request->session()->put('elemento_auth_id', $elemento->getKey());
        $request->session()->save();

        return redirect()->route('dashboard');
    }
}
