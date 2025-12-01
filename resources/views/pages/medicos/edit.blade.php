@extends('include.main')

@section('title', 'Editar Médico')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Listado de Médicos</h2>

    {{-- Botón para abrir el modal --}}
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editMedicoModal">
        Editar Médico
    </button>

    {{-- Modal --}}
    <div class="modal fade" id="editMedicoModal" tabindex="-1" aria-labelledby="editMedicoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicoModalLabel">Editar Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('medicos.update', $medico) }}" method="POST" id="editMedicoForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Usuario ID</label>
                            <input type="number" name="usuario_id" class="form-control" 
                                   value="{{ old('usuario_id', $medico->usuario_id) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Especialidad</label>
                            <input type="text" name="especialidad" class="form-control" 
                                   value="{{ old('especialidad', $medico->especialidad) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nro Licencia</label>
                            <input type="text" name="nro_licencia" class="form-control" 
                                   value="{{ old('nro_licencia', $medico->nro_licencia) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Años de Experiencia</label>
                            <input type="number" name="años_experiencia" class="form-control" 
                                   value="{{ old('años_experiencia', $medico->años_experiencia) }}" required>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" form="editMedicoForm" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection