@extends('include.main')

@section('title', 'Habitaciones')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Habitaciones</h2>
            <!-- Botón para abrir modal de creación -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHabitacionModal">
                Nueva Habitación
            </button>
        </div>
        <div class="mb-4">
            <h5>Habitaciones disponibles:</h5>
            @if($habitaciones_libres->isEmpty())
                <p class="text-muted">No hay habitaciones disponibles.</p>
            @else
                <ul class="list-group">
                    @foreach($habitaciones_libres as $h)
                        <li class="list-group-item">
                            Habitación #{{ $h->nro_habitacion }} - Tipo: {{ $h->tipo }}
                        </li>
                    @endforeach
                </ul>
            @endif
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
                            <!-- Botón para abrir modal de edición -->
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editHabitacionModal{{ $h->habitacion_id }}">
                                Editar
                            </button>

                            <form action="{{ route('habitaciones.destroy', $h) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar esta habitación?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal de edición -->
                    <div class="modal fade" id="editHabitacionModal{{ $h->habitacion_id }}" tabindex="-1"
                        aria-labelledby="editHabitacionModalLabel{{ $h->habitacion_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="editHabitacionModalLabel{{ $h->habitacion_id }}">Editar
                                        Habitación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>

                                <div class="modal-body">
                                    <form action="{{ route('habitaciones.update', $h) }}" method="POST"
                                        id="editHabitacionForm{{ $h->habitacion_id }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label class="form-label">Número de Habitación</label>
                                            <input type="text" name="nro_habitacion" class="form-control"
                                                value="{{ old('nro_habitacion', $h->nro_habitacion) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tipo</label>
                                            <input type="text" name="tipo" class="form-control"
                                                value="{{ old('tipo', $h->tipo) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select name="estado" class="form-select" required>
                                                <option value="libre" {{ old('estado', $h->estado) == 'libre' ? 'selected' : '' }}>Libre</option>
                                                <option value="ocupada" {{ old('estado', $h->estado) == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                                                <option value="mantenimiento" {{ old('estado', $h->estado) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" form="editHabitacionForm{{ $h->habitacion_id }}"
                                        class="btn btn-primary">Guardar cambios</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay habitaciones registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal de creación -->
    <div class="modal fade" id="createHabitacionModal" tabindex="-1" aria-labelledby="createHabitacionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="createHabitacionModalLabel">Crear nueva Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('habitaciones.store') }}" method="POST" id="createHabitacionForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Número de Habitación</label>
                            <input type="text" name="nro_habitacion" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <input type="text" name="tipo" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="libre">Libre</option>
                                <option value="ocupada">Ocupada</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" form="createHabitacionForm" class="btn btn-success">Guardar Habitación</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </div>
        </div>
    </div>
@endsection