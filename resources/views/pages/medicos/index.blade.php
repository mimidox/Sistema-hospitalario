@extends('include.main')

@section('title', 'Médicos')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Médicos</h2>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">Nuevo Médico</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-hover table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Especialidad</th>
                <th>Nro Licencia</th>
                <th>Años Experiencia</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicos as $m)
            <tr>
                <td>{{ $m->medico_id }}</td>
                <td>{{ $m->usuario_id }}</td>
                <td>{{ $m->especialidad }}</td>
                <td>{{ $m->nro_licencia }}</td>
                <td>{{ $m->años_experiencia }}</td>
                <td>
                    <a href="{{ route('medicos.edit', $m) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('medicos.destroy', $m) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este médico?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No hay médicos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection