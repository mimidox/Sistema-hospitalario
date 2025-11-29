<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::all();
        return view('pages.habitaciones.index', compact('habitaciones'));
    }

    public function create()
    {
        return view('pages.habitaciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nro_habitacion' => 'required|string|max:50',
            'tipo' => 'nullable|string|max:100',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        Habitacion::create($validated);

        return redirect()->route('habitaciones.index')->with('success', 'Habitación creada correctamente.');
    }

    public function edit(Habitacion $habitacion)
    {
        return view('pages.habitaciones.edit', compact('habitacion'));
    }

    public function update(Request $request, Habitacion $habitacion)
    {
        $validated = $request->validate([
            'nro_habitacion' => 'required|string|max:50',
            'tipo' => 'nullable|string|max:100',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        $habitacion->update($validated);

        return redirect()->route('habitaciones.index')->with('success', 'Habitación actualizada correctamente.');
    }

    public function destroy(Habitacion $habitacion)
    {
        $habitacion->delete();
        return redirect()->route('habitaciones.index')->with('success', 'Habitación eliminada correctamente.');
    }
}