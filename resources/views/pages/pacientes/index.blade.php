@extends('include.main')

@section('title', 'Pacientes')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pacientes</h2>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary">Nuevo Paciente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-hover table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Tipo de Sangre</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pacientes as $p)
            <tr>
                <td>{{ $p->paciente_id }}</td>
                <td>{{ $p->usuario_id }}</td>
                <td>{{ $p->tipo_de_sangre }}</td>
                <td>
                    <a href="{{ route('pacientes.edit', $p) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('pacientes.destroy', $p) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este paciente?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No hay pacientes registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection