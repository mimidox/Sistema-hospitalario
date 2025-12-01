@extends('include.main')

@section('title', 'Médicos')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Médicos</h2>
        <!-- Botón para abrir modal de creación -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMedicoModal">
            Nuevo Médico
        </button>
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
                    <!-- Botón para abrir modal de edición -->
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMedicoModal{{ $m->medico_id }}">
                        Editar
                    </button>

                    <form action="{{ route('medicos.destroy', $m) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este médico?')">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal de edición -->
            <div class="modal fade" id="editMedicoModal{{ $m->medico_id }}" tabindex="-1" aria-labelledby="editMedicoModalLabel{{ $m->medico_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="editMedicoModalLabel{{ $m->medico_id }}">Editar Médico</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form action="{{ route('medicos.update', $m) }}" method="POST" id="editMedicoForm{{ $m->medico_id }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Usuario ID</label>
                                    <input type="number" name="usuario_id" class="form-control" value="{{ old('usuario_id', $m->usuario_id) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Especialidad</label>
                                    <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad', $m->especialidad) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nro Licencia</label>
                                    <input type="text" name="nro_licencia" class="form-control" value="{{ old('nro_licencia', $m->nro_licencia) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Años de Experiencia</label>
                                    <input type="number" name="años_experiencia" class="form-control" value="{{ old('años_experiencia', $m->años_experiencia) }}" required>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" form="editMedicoForm{{ $m->medico_id }}" class="btn btn-primary">Guardar cambios</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>

                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="6" class="text-center">No hay médicos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de creación -->
<div class="modal fade" id="createMedicoModal" tabindex="-1" aria-labelledby="createMedicoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createMedicoModalLabel">Nuevo Médico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('medicos.store') }}" method="POST" id="createMedicoForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Usuario ID</label>
                        <input type="number" name="usuario_id" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nro Licencia</label>
                        <input type="text" name="nro_licencia" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Años de Experiencia</label>
                        <input type="number" name="años_experiencia" class="form-control" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="createMedicoForm" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>
@endsection