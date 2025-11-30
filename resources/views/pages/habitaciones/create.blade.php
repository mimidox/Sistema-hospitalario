@extends('include.main')

@section('title', 'Crear Habitación')

@section('content')
<div class="container mt-5">
    <h2>Crear nueva Habitación</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('habitaciones.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Número de Habitación</label>
            <input type="text" name="nro_habitacion" class="form-control" value="{{ old('nro_habitacion') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" value="{{ old('tipo') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="libre" {{ old('estado')=='libre' ? 'selected' : '' }}>Libre</option>
                <option value="ocupada" {{ old('estado')=='ocupada' ? 'selected' : '' }}>Ocupada</option>
                <option value="mantenimiento" {{ old('estado')=='mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Habitación</button>
        <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection