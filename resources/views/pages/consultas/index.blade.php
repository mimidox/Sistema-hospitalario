@extends('include.main')

@section('title', 'Consultas Médicas')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Consultas Médicas</h2>
        <!-- Botón para abrir modal de creación -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createConsultaModal">
            Nueva Consulta
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
                    <!-- Botón para abrir modal de edición -->
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editConsultaModal{{ $c->consulta_id }}">
                        Editar
                    </button>

                    <form action="{{ route('consultas.destroy', $c) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta consulta?')">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal de edición -->
            <div class="modal fade" id="editConsultaModal{{ $c->consulta_id }}" tabindex="-1"
                aria-labelledby="editConsultaModalLabel{{ $c->consulta_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="editConsultaModalLabel{{ $c->consulta_id }}">Editar Consulta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form action="{{ route('consultas.update', $c) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Médico</label>
                                    <select name="medico_id" class="form-select" required>
                                        <option value="">-- Seleccionar Médico --</option>
                                        @foreach($medicos as $m)
                                            <option value="{{ $m->medico_id }}" {{ $c->medico_id == $m->medico_id ? 'selected' : '' }}>
                                                {{ $m->especialidad }} (Licencia: {{ $m->nro_licencia }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Paciente</label>
                                    <select name="ficha_id" class="form-select" required>
                                        <option value="">-- Seleccionar Paciente --</option>
                                        @foreach($pacientes as $p)
                                            <option value="{{ $p->paciente_id }}" {{ $c->ficha_id == $p->paciente_id ? 'selected' : '' }}>
                                                Paciente #{{ $p->paciente_id }} (Sangre: {{ $p->tipo_de_sangre }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Motivo</label>
                                    <input type="text" name="motivo_consulta" class="form-control"
                                           value="{{ $c->motivo_consulta }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control"
                                           value="{{ $c->fecha }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tipo</label>
                                    <input type="text" name="tipo" class="form-control"
                                           value="{{ $c->tipo }}" required>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="7" class="text-center">No hay consultas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de creación -->
<div class="modal fade" id="createConsultaModal" tabindex="-1" aria-labelledby="createConsultaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createConsultaModalLabel">Crear nueva Consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('consultas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Médico</label>
                        <select name="medico_id" class="form-select" required>
                            <option value="">-- Seleccionar Médico --</option>
                            @foreach($medicos as $m)
                                <option value="{{ $m->medico_id }}">{{ $m->especialidad }} (Licencia: {{ $m->nro_licencia }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="ficha_id" class="form-select" required>
                            <option value="">-- Seleccionar Paciente --</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->paciente_id }}">Paciente #{{ $p->paciente_id }} (Sangre: {{ $p->tipo_de_sangre }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="motivo_consulta" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="tipo" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar Consulta</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection