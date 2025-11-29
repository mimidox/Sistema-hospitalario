@extends('include.main')

@section('title', 'Editar Área')

@section('content')
<div class="container mt-5">
    <h2>Editar Área</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('areas.update', $area) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre del Área</label>
            <input type="text" name="nombre_area" class="form-control" value="{{ old('nombre_area', $area->nombre_area) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $area->descripcion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection