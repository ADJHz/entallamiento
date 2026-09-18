<?php

namespace App\Http\Controllers;

use App\Models\ColorFranja;
use App\Models\Direccion;
use App\Models\Elemento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\View\View;

class UniformeController extends Controller
{
    public function show(Request $request): View
    {
        $elemento = $this->elementoAutenticado($request);

        return view('uniforme.show', [
            'elemento' => $elemento,
            'direcciones' => Direccion::query()->orderBy('nombre')->get(['id', 'nombre']),
            'tipoUniforme' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $elemento = $this->elementoAutenticado($request);

        $direccionId = $this->decryptId($request->input('direccion_id'));
        $colorFranjaId = $this->decryptId($request->input('color_franja_id'));

        if ($direccionId === null || $colorFranjaId === null) {
            return back()->withInput()->withErrors([
                'color_franja_id' => 'La selección del uniforme no es válida.',
            ]);
        }

        $data = $request->validate([
            'talla_chamarra' => ['required', 'string', 'max:10'],
            'talla_camisa' => ['required', 'string', 'max:10'],
            'talla_pantalon' => ['required', 'string', 'max:10'],
            'talla_cinturon' => ['required', 'string', 'max:10'],
            'talla_botas' => ['required', 'string', 'max:10'],
        ]);
        $data['direccion_id'] = $direccionId;
        $data['color_franja_id'] = $colorFranjaId;

        $colorFranja = ColorFranja::query()
            ->with('tipoUniforme')
            ->where('id', $data['color_franja_id'])
            ->where('direccion_id', $data['direccion_id'])
            ->first();

        if (! $colorFranja) {
            return back()
                ->withInput()
                ->withErrors(['color_franja_id' => 'La combinación seleccionada no es válida para tu dirección.']);
        }

        $elemento->fill($data);
        $elemento->tipo_uniforme = $colorFranja->tipoUniforme?->nombre ?? $elemento->tipo_uniforme;
        $elemento->save();

        return redirect()->route('uniforme.show')->with('status', 'Uniforme guardado correctamente.');
    }

    private function elementoAutenticado(Request $request): Elemento
    {
        $elemento = Elemento::autenticado($request);

        if ($elemento === null) {
            throw new HttpException(403);
        }

        return $elemento;
    }

    private function decryptId(mixed $value): ?int
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            $id = Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }

        return ctype_digit($id) && (int) $id > 0 ? (int) $id : null;
    }
}
