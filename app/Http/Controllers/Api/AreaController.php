<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('pages.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('pages.areas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_area' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        Area::create($validated);

        return redirect()->route('areas.index')->with('success', 'Área creada correctamente.');
    }

    public function edit(Area $area)
    {
        return view('pages.areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'nombre_area' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $area->update($validated);

        return redirect()->route('areas.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente.');
    }
}