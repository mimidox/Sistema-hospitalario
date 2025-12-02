<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    public function index()
    {
        $consultas = Consulta::with(['medico', 'paciente'])->get();
        return view('pages.consultas.index', compact('consultas'));
    }

    public function create()
    {
        $medicos = Medico::all();
        $pacientes = Paciente::all();
        return view('pages.consultas.create', compact('medicos', 'pacientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medico_id' => 'required|integer|exists:medico,medico_id',
            'ficha_id' => 'required|integer|exists:paciente,paciente_id',
            'motivo_consulta' => 'required|string|max:255',
            'fecha' => 'required|date',
            'tipo' => 'required|string|max:50',
        ]);

        Consulta::create($validated);

        return redirect()->route('consultas.index')->with('success', 'Consulta creada correctamente.');
    }

    public function edit(Consulta $consulta)
    {
        $medicos = Medico::all();
        $pacientes = Paciente::all();
        return view('pages.consultas.edit', compact('consulta', 'medicos', 'pacientes'));
    }

    public function update(Request $request, Consulta $consulta)
    {
        $validated = $request->validate([
            'medico_id' => 'required|integer|exists:medico,medico_id',
            'ficha_id' => 'required|integer|exists:paciente,paciente_id',
            'motivo_consulta' => 'required|string|max:255',
            'fecha' => 'required|date',
            'tipo' => 'required|string|max:50',
        ]);

        $consulta->update($validated);

        return redirect()->route('consultas.index')->with('success', 'Consulta actualizada correctamente.');
    }

    public function destroy(Consulta $consulta)
    {
        $consulta->delete();
        return redirect()->route('consultas.index')->with('success', 'Consulta eliminada correctamente.');
    }
}