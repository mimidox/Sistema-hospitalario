@extends('include.main')

@section('title', 'Consultas Médicas')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Consultas Médicas</h2>
        <a href="{{ route('consultas.create') }}" class="btn btn-primary">Nueva Consulta</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Médico</th>
                <th>Paciente</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultas as $c)
            <tr>
                <td>{{ $c->consulta_id }}</td>
                <td>{{ $c->medico ? $c->medico->especialidad : '-' }}</td>
                <td>{{ $c->paciente ? $c->paciente->paciente_id : '-' }}</td>
                <td>{{ $c->motivo_consulta }}</td>
                <td>{{ $c->fecha }}</td>
                <td>{{ $c->tipo }}</td>
                <td>
                    <a href="{{ route('consultas.edit', $c) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('consultas.destroy', $c) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta consulta?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No hay consultas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection