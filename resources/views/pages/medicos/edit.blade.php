@extends('include.main')

@section('title', 'Editar Médico')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Editar Médico</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('medicos.update', $medico) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Usuario ID</label>
            <input type="number" name="usuario_id" class="form-control" value="{{ old('usuario_id', $medico->usuario_id) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad', $medico->especialidad) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nro Licencia</label>
            <input type="text" name="nro_licencia" class="form-control" value="{{ old('nro_licencia', $medico->nro_licencia) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Años de Experiencia</label>
            <input type="number" name="años_experiencia" class="form-control" value="{{ old('años_experiencia', $medico->años_experiencia) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('medicos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection