@extends('include.main')

@section('title', 'Crear Paciente')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Registrar nuevo Paciente</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('pacientes.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Usuario ID</label>
            <input type="number" name="usuario_id" class="form-control" value="{{ old('usuario_id') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Sangre</label>
            <input type="text" name="tipo_de_sangre" class="form-control" value="{{ old('tipo_de_sangre') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar Paciente</button>
        <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection