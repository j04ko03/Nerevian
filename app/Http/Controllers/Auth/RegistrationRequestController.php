<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\peticions_registre;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

// PARA LA PETICIÓN DE CUENTA
class RegistrationRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_empresa' => 'required|string|max:100',
            'contacte' => 'required|string|max:100',
            'correu' => 'required|email|max:200',
            'telefon' => 'nullable|string|max:20',
            'missatge' => 'nullable|string|max:500',
            'contrasenya' => ['required', Rules\Password::defaults()],
            'rol_id' => 'required|integer|exists:rols,id',
        ]);

        $validated['contrasenya'] = Hash::make($validated['contrasenya']);

        peticions_registre::create([
            ...$validated,
            'estat' => '0',
            'data_creacio' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Solicitud enviada correctamente.'
        ], 201);
    }
}