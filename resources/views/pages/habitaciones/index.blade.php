@extends('include.main')

@section('title', 'Habitaciones')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Habitaciones</h2>
        <a href="{{ route('habitaciones.create') }}" class="btn btn-primary">Nueva Habitación</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nro</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($habitaciones as $h)
            <tr>
                <td>{{ $h->habitacion_id }}</td>
                <td>{{ $h->nro_habitacion }}</td>
                <td>{{ $h->tipo }}</td>
                <td>{{ ucfirst($h->estado) }}</td>
                <td>
                    <a href="{{ route('habitaciones.edit', $h) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('habitaciones.destroy', $h) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta habitación?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No hay habitaciones registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection