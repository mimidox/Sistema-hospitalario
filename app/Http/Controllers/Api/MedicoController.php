<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::all();
        return view('pages.medicos.index', compact('medicos'));
    }

    public function create()
    {
        return view('pages.medicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|integer',
            'especialidad' => 'required|string|max:100',
            'nro_licencia' => 'required|string|max:50',
            'años_experiencia' => 'required|integer|min:0',
        ]);

        Medico::create($validated);

        return redirect()->route('medicos.index')->with('success', 'Médico creado correctamente.');
    }

    public function edit(Medico $medico)
    {
        return view('pages.medicos.edit', compact('medico'));
    }

    public function update(Request $request, Medico $medico)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|integer',
            'especialidad' => 'required|string|max:100',
            'nro_licencia' => 'required|string|max:50',
            'años_experiencia' => 'required|integer|min:0',
        ]);

        $medico->update($validated);

        return redirect()->route('medicos.index')->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();
        return redirect()->route('medicos.index')->with('success', 'Médico eliminado correctamente.');
    }
}