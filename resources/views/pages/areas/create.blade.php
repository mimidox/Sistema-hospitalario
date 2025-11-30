@extends('include.main')


@section('title', 'Crear Área')

@section('content')
<div class="container mt-5">
    <h2>Crear nueva Área</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

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

        <button type="submit" class="btn btn-success">Guardar Área</button>
        <a href="{{ route('areas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection