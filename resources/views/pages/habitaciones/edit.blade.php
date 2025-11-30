@extends('include.main')

@section('title', 'Editar Habitación')

@section('content')
<div class="container mt-5">
    <h2>Editar Habitación</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('habitaciones.update', $habitacion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Número de Habitación</label>
            <input type="text" name="nro_habitacion" class="form-control" value="{{ old('nro_habitacion', $habitacion->nro_habitacion) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $habitacion->tipo) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="libre" {{ old('estado', $habitacion->estado)=='libre' ? 'selected' : '' }}>Libre</option>
                <option value="ocupada" {{ old('estado', $habitacion->estado)=='ocupada' ? 'selected' : '' }}>Ocupada</option>
                <option value="mantenimiento" {{ old('estado', $habitacion->estado)=='mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection