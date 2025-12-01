@extends('include.main')

@section('title', 'Áreas')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Áreas</h2>
        <!-- Botón para abrir modal de creación -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAreaModal">
            Nueva Área
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
                    <!-- Botón para abrir modal de edición -->
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editAreaModal{{ $area->area_id }}">
                        Editar
                    </button>

                    <form action="{{ route('areas.destroy', $area) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta área?')">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal de edición -->
            <div class="modal fade" id="editAreaModal{{ $area->area_id }}" tabindex="-1"
                aria-labelledby="editAreaModalLabel{{ $area->area_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="editAreaModalLabel{{ $area->area_id }}">Editar Área</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form action="{{ route('areas.update', $area) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Nombre del Área</label>
                                    <input type="text" name="nombre_area" class="form-control"
                                           value="{{ old('nombre_area', $area->nombre_area) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $area->descripcion) }}</textarea>
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
            <tr><td colspan="4" class="text-center">No hay áreas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de creación -->
<div class="modal fade" id="createAreaModal" tabindex="-1" aria-labelledby="createAreaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createAreaModalLabel">Crear nueva Área</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('areas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre del Área</label>
                        <input type="text" name="nombre_area" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar Área</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection