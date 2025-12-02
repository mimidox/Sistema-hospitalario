@extends('include.main')

@section('title', 'Crear Consulta')

@section('content')
<div class="container mt-5">
    <h2>Crear nueva Consulta</h2>

    <form action="{{ route('consultas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Médico</label>
            <select name="medico_id" class="form-select" required>
                <option value="">-- Seleccionar Médico --</option>
                @foreach($medicos as $m)
                    <option value="{{ $m->medico_id }}">{{ $m->especialidad }} (Licencia: {{ $m->nro_licencia }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Paciente</label>
            <select name="ficha_id" class="form-select" required>
                <option value="">-- Seleccionar Paciente --</option>
                @foreach($pacientes as $p)
                    <option value="{{ $p->paciente_id }}">Paciente #{{ $p->paciente_id }} (Sangre: {{ $p->tipo_de_sangre }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Motivo</label>
            <input type="text" name="motivo_consulta" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar Consulta</button>
        <a href="{{ route('consultas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection