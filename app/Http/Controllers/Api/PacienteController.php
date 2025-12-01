<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    public function index()
    {
        // Todos los pacientes
        $pacientes = Paciente::all();

        // Conteo agrupado por tipo de sangre
        $conteo_sangre = DB::select("
            SELECT tipo_de_sangre, COUNT(*) AS total
            FROM paciente
            GROUP BY tipo_de_sangre
        ");

        // Enviamos ambas variables a la vista
        return view('pages.pacientes.index', compact('pacientes', 'conteo_sangre'));
    }


    public function create()
    {
        return view('pages.pacientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|integer',
            'tipo_de_sangre' => 'required|string|max:10',
        ]);

        Paciente::create($validated);

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pages.pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|integer',
            'tipo_de_sangre' => 'required|string|max:10',
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado correctamente.');
    }
}