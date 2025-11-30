@extends('include.main')

@section('title', 'Editar Consulta')

@section('content')
<div class="container mt-5">
    <h2>Editar Consulta</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('consultas.update', $consulta) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Médico</label>
            <select name="medico_id" class="form-select" required>
                <option value="">-- Seleccionar Médico --</option>
                @foreach($medicos as $m)
                    <option value="{{ $m->medico_id }}"
                        {{ old('medico_id', $consulta->medico_id) == $m->medico_id ? 'selected' : '' }}>
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
                    <option value="{{ $p->paciente_id }}"
                        {{ old('ficha_id', $consulta->ficha_id) == $p->paciente_id ? 'selected' : '' }}>
                        Paciente #{{ $p->paciente_id }} (Sangre: {{ $p->tipo_de_sangre }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Motivo</label>
            <input type="text" name="motivo_consulta" class="form-control"
                   value="{{ old('motivo_consulta', $consulta->motivo_consulta) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control"
                   value="{{ old('fecha', $consulta->fecha) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control"
                   value="{{ old('tipo', $consulta->tipo) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('consultas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection