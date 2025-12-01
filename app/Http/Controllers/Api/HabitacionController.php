<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::all();
        $habitaciones_libres = Habitacion::where('estado', 'libre')->get();

        return view('pages.habitaciones.index', compact('habitaciones', 'habitaciones_libres'));
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
            // el procedimiento ya fija estado en 'libre', no es necesario validarlo aquí
        ]);

        // Llamada al procedimiento almacenado
        DB::statement("CALL sp_crear_habitacion(?, ?)", [
            $validated['nro_habitacion'],
            $validated['tipo']
        ]);

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
            'estado' => 'required|in:libre,ocupada,mantenimiento',
        ]);

        // Aquí puedes seguir usando update normal o crear otro procedimiento
        $habitacion->update($validated);

        return redirect()->route('habitaciones.index')->with('success', 'Habitación actualizada correctamente.');
    }

    public function destroy(Habitacion $habitacion)
    {
        try {
            $habitacion->delete();
            return redirect()->route('habitaciones.index')
                ->with('success', 'Habitación eliminada correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('habitaciones.index')
                ->with('error', 'No se puede eliminar la habitación porque está asociada a hospitalizaciones.');
        }
    }
}