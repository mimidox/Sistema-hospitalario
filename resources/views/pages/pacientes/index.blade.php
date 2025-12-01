@extends('include.main')

@section('title', 'Pacientes')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pacientes</h2>
        <!-- Botón para abrir modal de creación -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPacienteModal">
            Nuevo Paciente
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <h5>Pacientes por tipo de sangre:</h5>
        <ul class="list-group">
            @foreach($conteo_sangre as $c)
                <li class="list-group-item">
                    Tipo {{ $c->tipo_de_sangre }}: {{ $c->total }} pacientes
                </li>
            @endforeach
        </ul>
    </div>


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
                    <!-- Botón para abrir modal de edición -->
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPacienteModal{{ $p->paciente_id }}">
                        Editar
                    </button>

                    <form action="{{ route('pacientes.destroy', $p) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este paciente?')">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal de edición -->
            <div class="modal fade" id="editPacienteModal{{ $p->paciente_id }}" tabindex="-1" aria-labelledby="editPacienteModalLabel{{ $p->paciente_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="editPacienteModalLabel{{ $p->paciente_id }}">Editar Paciente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form action="{{ route('pacientes.update', $p) }}" method="POST" id="editPacienteForm{{ $p->paciente_id }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Usuario ID</label>
                                    <input type="number" name="usuario_id" class="form-control" value="{{ old('usuario_id', $p->usuario_id) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tipo de Sangre</label>
                                    <input type="text" name="tipo_de_sangre" class="form-control" value="{{ old('tipo_de_sangre', $p->tipo_de_sangre) }}" required>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" form="editPacienteForm{{ $p->paciente_id }}" class="btn btn-primary">Guardar cambios</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>

                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="4" class="text-center">No hay pacientes registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de creación -->
<div class="modal fade" id="createPacienteModal" tabindex="-1" aria-labelledby="createPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createPacienteModalLabel">Registrar nuevo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('pacientes.store') }}" method="POST" id="createPacienteForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Usuario ID</label>
                        <input type="number" name="usuario_id" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Sangre</label>
                        <input type="text" name="tipo_de_sangre" class="form-control" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="createPacienteForm" class="btn btn-success">Guardar Paciente</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
@endsection