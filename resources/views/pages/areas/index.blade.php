@extends('include.main')

@section('title', 'Áreas')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Áreas</h2>
        <a href="{{ route('areas.create') }}" class="btn btn-primary">Nueva Área</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($areas as $area)
            <tr>
                <td>{{ $area->area_id }}</td>
                <td>{{ $area->nombre_area }}</td>
                <td>{{ $area->descripcion }}</td>
                <td>
                    <a href="{{ route('areas.edit', $area) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('areas.destroy', $area) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta área?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No hay áreas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection