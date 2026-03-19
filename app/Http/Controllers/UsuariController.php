<?php

namespace App\Http\Controllers;

use App\Models\usuaris;
use Illuminate\Http\Request;
use App\Classes\Utilitat;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
class UsuariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Listar todos los usuarios (para el admin).
        try {
            $usuaris = usuaris::all(); // Obtener todos los usuarios (all()).
            session()->flash('success', 'Se pasan correctamente los datos');
            $response = view('usuaris.index', compact('usuaris'));
        }
        catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            session()->flash('error', 'No se han obtenido los datos' . ' - ' . $missatge);
            $response = $response = redirect()->back();
        }
        // Pasar usuarios a la vista.
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Vista del formulario de petición de registro
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    //
    }

    /**
     * Display the specified resource.
     */
    public function show(usuaris $usuaris)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(usuaris $usuaris)
    {
    //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, usuaris $usuaris)
    {
    //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(usuaris $usuaris)
    {
        try {
            // Eliminar usuario.
            $usuaris->delete();
            session()->flash('success', 'Se han eliminado correctamente los datos');
            $response = redirect()->route('dashboardAdmin.usuaris')->with('success', 'Cuenta eliminada exitosamente!');
        }
        catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            session()->flash('error', 'No se han eliminado los datos' . ' - ' . $missatge);
            $response = $response = redirect()->back();
        }
        return $response;
    }
}
