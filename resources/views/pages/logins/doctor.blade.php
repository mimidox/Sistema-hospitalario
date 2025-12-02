@include('include.header')
@include('include.navbar')

@if(!session('logged_in') || session('rol') !== 'medico')
    <div class="container mt-5">
        <div class="alert alert-warning text-center">
            <h4>Acceso no autorizado</h4>
            <p>Debe iniciar sesión como médico para acceder a esta página.</p>
            <a href="{{ route('ControlInicio.formlogin') }}" class="btn btn-primary">Ir al Login</a>
        </div>
    </div>
@else
<main class="container mx-auto mt-8">
    <div class="container-fluid mt-4">
        <div class="row">

            {{-- PANEL IZQUIERDO --}}
            <div class="col-3">
                <div class="sidebar text-center shadow p-3">
                    <div class="profile-circle mb-3 mx-auto" style="width: 80px; height: 80px; border-radius: 50%;">
                        <img src="assets/img/usuario_login_icon.jpg" alt="" style="width: 80px; height: 80px; background: #ccc; border-radius: 50%;">
                    </div>
                    <h4 class="mb-1">{{ session('username') ?? 'Usuario' }}</h4>
                    <p class="text-muted mb-1">ID: {{ session('usuario_id') }}</p>
                    <p class="text-success fw-bold">{{ ucfirst(session('rol')) }}</p>
                    <p class="text-muted mb-1">Nombre: {{ session('nombre') }}</p>
                    <p class="text-muted mb-1">Apellido: {{ session('paterno') }} {{ session('materno') }}</p>
                    <p class="text-muted mb-1">Correo: {{ session('correo') }}</p>
                    <p class="text-muted mb-1">Teléfono: {{ session('telefono') }}</p>
                    
                    {{-- INFORMACIÓN MÉDICA ESPECÍFICA --}}
                    @php
                        $medicoInfo = \App\Models\Medico::where('usuario_id', session('usuario_id'))->first();
                    @endphp
                    @if($medicoInfo)
                        <div class="mt-3 p-2 bg-light rounded">
                            <p class="text-muted mb-1"><strong>Especialidad:</strong> {{ $medicoInfo->especialidad }}</p>
                            <p class="text-muted mb-1"><strong>Licencia:</strong> {{ $medicoInfo->nro_licencia }}</p>
                            <p class="text-muted mb-1"><strong>Experiencia:</strong> {{ $medicoInfo->años_experiencia }} años</p>
                        </div>
                    @endif
                    
                    {{-- BOTÓN CERRAR SESIÓN --}}
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar Sesión</button>
                    </form>
                </div>
            </div>

            {{-- PANEL PRINCIPAL --}}
            <div class="col-9">
                {{-- ALERTAS --}}
                <div id="alertContainer"></div>

                {{-- BOTONES SUPERIORES --}}
                <div class="top-buttons mb-3 d-flex gap-2 justify-content-center flex-wrap">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaConsulta">
                        NUEVA CONSULTA
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRecetaMedica">
                        RECETA MÉDICA
                    </button>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalOrdenExamen">
                        ORDEN EXÁMENES
                    </button>
                    <a href="#" class="btn btn-warning">MI AGENDA</a>
                    <a href="#" class="btn btn-danger">PACIENTES HOSPITALIZADOS</a>
                </div>

                {{-- TABS --}}
                <ul class="nav nav-tabs mb-2 tab-custom">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-consultas">Mis Consultas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-pacientes">Mis Pacientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-hospitalizaciones">Hospitalizaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-historiales">Historiales Médicos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-recetas">Recetas Emitidas</a>
                    </li>
                </ul>

                {{-- CONTENIDO DE TABS --}}
                <div class="tab-content">
                    {{-- TAB CONSULTAS --}}
                    <div class="tab-pane fade show active" id="tab-consultas">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Mis Consultas del Día</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Consulta</th>
                                            <th>Paciente</th>
                                            <th>Fecha</th>
                                            <th>Motivo</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $misConsultas = \App\Models\Consulta::with(['ficha.paciente.usuario'])
                                                ->where('medico_id', $medicoInfo->medico_id)
                                                ->where('fecha', now()->format('Y-m-d'))
                                                ->get();
                                        @endphp
                                        @foreach($misConsultas as $consulta)
                                        <tr>
                                            <td>{{ $consulta->consulta_id }}</td>
                                            <td>{{ $consulta->ficha->paciente->usuario->nombre }} {{ $consulta->ficha->paciente->usuario->paterno }}</td>
                                            <td>{{ $consulta->fecha }}</td>
                                            <td>{{ $consulta->motivo_consulta }}</td>
                                            <td>{{ $consulta->tipo }}</td>
                                            <td><span class="badge bg-success">Activa</span></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-atender-consulta" data-id="{{ $consulta->consulta_id }}">
                                                    ATENDER
                                                </button>
                                                <button class="btn btn-info btn-sm btn-ver-historial" data-paciente-id="{{ $consulta->ficha->paciente->paciente_id }}">
                                                    HISTORIAL
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($misConsultas->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay consultas programadas para hoy</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB PACIENTES --}}
                    <div class="tab-pane fade" id="tab-pacientes">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Mis Pacientes</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Paciente</th>
                                            <th>Nombre Completo</th>
                                            <th>Edad</th>
                                            <th>Tipo Sangre</th>
                                            <th>Última Consulta</th>
                                            <th>Diagnóstico</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $misPacientes = \App\Models\Consulta::with(['ficha.paciente.usuario', 'ficha.paciente.historial'])
                                                ->where('medico_id', $medicoInfo->medico_id)
                                                ->get()
                                                ->unique('ficha.paciente_id');
                                        @endphp
                                        @foreach($misPacientes as $consulta)
                                        @php
                                            $paciente = $consulta->ficha->paciente;
                                            $edad = $paciente->usuario->fec_nac ? \Carbon\Carbon::parse($paciente->usuario->fec_nac)->age : 'N/A';
                                        @endphp
                                        <tr>
                                            <td>{{ $paciente->paciente_id }}</td>
                                            <td>{{ $paciente->usuario->nombre }} {{ $paciente->usuario->paterno }} {{ $paciente->usuario->materno }}</td>
                                            <td>{{ $edad }} años</td>
                                            <td>{{ $paciente->tipo_de_sangre ?? 'No especificado' }}</td>
                                            <td>{{ $consulta->fecha }}</td>
                                            <td>{{ $paciente->historial->diagnosticos ?? 'Sin diagnóstico' }}</td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver-paciente" data-id="{{ $paciente->paciente_id }}">
                                                    VER
                                                </button>
                                                <button class="btn btn-primary btn-sm btn-nueva-consulta" data-paciente-id="{{ $paciente->paciente_id }}">
                                                    NUEVA CONSULTA
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($misPacientes->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay pacientes asignados</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB HOSPITALIZACIONES --}}
                    <div class="tab-pane fade" id="tab-hospitalizaciones">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Pacientes Hospitalizados</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Hospitalización</th>
                                            <th>Paciente</th>
                                            <th>Habitación</th>
                                            <th>Fecha Ingreso</th>
                                            <th>Fecha Alta</th>
                                            <th>Diagnóstico</th>
                                            <th>Estado</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $misHospitalizaciones = \App\Models\Hospitalizacion::with(['paciente.usuario', 'habitacion'])
                                                ->where('medico_id', $medicoInfo->medico_id)
                                                ->whereNull('fecha_alta')
                                                ->get();
                                        @endphp
                                        @foreach($misHospitalizaciones as $hospitalizacion)
                                        <tr>
                                            <td>{{ $hospitalizacion->hospitalizacion_id }}</td>
                                            <td>{{ $hospitalizacion->paciente->usuario->nombre }} {{ $hospitalizacion->paciente->usuario->paterno }}</td>
                                            <td>{{ $hospitalizacion->habitacion->nro_habitacion }}</td>
                                            <td>{{ $hospitalizacion->fecha_ingreso }}</td>
                                            <td>{{ $hospitalizacion->fecha_alta ?? 'Pendiente' }}</td>
                                            <td>{{ $hospitalizacion->diagnostico ?? 'Sin diagnóstico' }}</td>
                                            <td><span class="badge bg-warning">Hospitalizado</span></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-visitar-paciente" data-id="{{ $hospitalizacion->hospitalizacion_id }}">
                                                    VISITAR
                                                </button>
                                                <button class="btn btn-success btn-sm btn-dar-alta" data-id="{{ $hospitalizacion->hospitalizacion_id }}">
                                                    DAR ALTA
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($misHospitalizaciones->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No hay pacientes hospitalizados actualmente</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB HISTORIALES MÉDICOS --}}
                    <div class="tab-pane fade" id="tab-historiales">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Historiales Médicos</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Historial</th>
                                            <th>Paciente</th>
                                            <th>Antecedentes</th>
                                            <th>Alergias</th>
                                            <th>Diagnósticos</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $historiales = \App\Models\HistorialMedico::with('paciente.usuario')->get();
                                        @endphp
                                        @foreach($historiales as $historial)
                                        <tr>
                                            <td>{{ $historial->historial_id }}</td>
                                            <td>{{ $historial->paciente->usuario->nombre }} {{ $historial->paciente->usuario->paterno }}</td>
                                            <td>{{ Str::limit($historial->antecedentes, 50) }}</td>
                                            <td>{{ Str::limit($historial->alergias, 50) }}</td>
                                            <td>{{ Str::limit($historial->diagnosticos, 50) }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-ver-historial-completo" data-id="{{ $historial->historial_id }}">
                                                    VER COMPLETO
                                                </button>
                                                <button class="btn btn-warning btn-sm btn-actualizar-historial" data-id="{{ $historial->historial_id }}">
                                                    ACTUALIZAR
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($historiales->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No hay historiales médicos registrados</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- TAB RECETAS --}}
                    <div class="tab-pane fade" id="tab-recetas">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Recetas Médicas Emitidas</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Receta</th>
                                            <th>Paciente</th>
                                            <th>Medicamento</th>
                                            <th>Dosis</th>
                                            <th>Duración</th>
                                            <th>Frecuencia</th>
                                            <th>Fecha</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recetas as $receta)
                                        <tr>
                                            <td>{{ $receta->receta_id }}</td>
                                            <td>
                                                {{ $receta->paciente_nombre ?? 'N/A' }} {{ $receta->paciente_paterno ?? '' }}
                                            </td>
                                            <td>{{ $receta->medicamento_nombre ?? 'N/A' }}</td>
                                            <td>{{ $receta->dosis }}</td>
                                            <td>{{ $receta->duracion }}</td>
                                            <td>{{ $receta->frecuencia }}</td>
                                            <td>
                                                @if($receta->fecha_ini)
                                                    {{ \Carbon\Carbon::parse($receta->fecha_ini)->format('d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-imprimir-receta" data-id="{{ $receta->receta_id }}">
                                                    IMPRIMIR
                                                </button>
                                                <button class="btn btn-info btn-sm btn-ver-receta" data-id="{{ $receta->receta_id }}">
                                                    VER
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($recetas->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No hay recetas médicas emitidas</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</main>

<!-- MODALES ADICIONALES PARA MÉDICO -->
<!-- ========== MODALES PARA MÉDICO ========== -->

<!-- Modal Nueva Consulta -->
<div class="modal fade" id="modalNuevaConsulta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Consulta Médica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevaConsulta">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Buscar Paciente *</label>
                                <input type="text" id="buscarPaciente" class="form-control" placeholder="Nombre, apellido o username">
                                <div id="resultadosPacientes" class="mt-2" style="display: none;"></div>
                                <input type="hidden" name="paciente_id" id="paciente_id">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paciente Seleccionado</label>
                                <div id="pacienteSeleccionado" class="form-control bg-light" style="min-height: 38px; display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Consulta *</label>
                                <input type="date" name="fecha_consulta" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hora Consulta *</label>
                                <input type="time" name="hora_consulta" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Consulta *</label>
                                <select name="tipo_consulta" class="form-select" required>
                                    <option value="General">General</option>
                                    <option value="Especialidad">Especialidad</option>
                                    <option value="Control">Control</option>
                                    <option value="Emergencia">Emergencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Motivo de Consulta *</label>
                                <textarea name="motivo_consulta" class="form-control" rows="3" placeholder="Describa el motivo de la consulta..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Consulta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Receta Médica -->
<div class="modal fade" id="modalRecetaMedica" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receta Médica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRecetaMedica">
                @csrf
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Paciente *</label>
                                <select name="paciente_id" class="form-select" required>
                                    <option value="">Seleccionar paciente...</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->paciente_id }}">
                                            {{ $paciente->nombre }} {{ $paciente->paterno }} {{ $paciente->materno }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Diagnóstico *</label>
                                <textarea name="diagnostico" class="form-control" rows="2" placeholder="Diagnóstico del paciente..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2">Medicamentos</h6>
                    <div id="medicamentos-container">
                        <div class="row medicamento-item mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Medicamento *</label>
                                <select name="medicamentos[0][medicamento_id]" class="form-select medicamento-select" required>
                                    <option value="">Seleccionar...</option>
                                    @php
                                        $medicamentos = DB::table('medicamentos')->get();
                                    @endphp
                                    @foreach($medicamentos as $med)
                                        <option value="{{ $med->medicamento_id }}">{{ $med->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dosis *</label>
                                <input type="text" name="medicamentos[0][dosis]" class="form-control" placeholder="Ej: 1 tableta" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Duración *</label>
                                <input type="text" name="medicamentos[0][duracion]" class="form-control" placeholder="Ej: 5 días" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Frecuencia *</label>
                                <input type="text" name="medicamentos[0][frecuencia]" class="form-control" placeholder="Ej: Cada 8 horas" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm btn-remove-medicamento" style="display: none;">✕</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="btnAgregarMedicamento" class="btn btn-outline-primary btn-sm">
                        + Agregar otro medicamento
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Emitir Receta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Orden de Exámenes -->
<div class="modal fade" id="modalOrdenExamen" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Orden de Exámenes Médicos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formOrdenExamen">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Paciente *</label>
                                <select name="paciente_id" class="form-select" required>
                                    <option value="">Seleccionar paciente...</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->paciente_id }}">
                                            {{ $paciente->nombre }} {{ $paciente->paterno }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Orden *</label>
                                <input type="date" name="fecha_orden" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Exámenes</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Hemograma">
                                    <label class="form-check-label">Hemograma Completo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Orina">
                                    <label class="form-check-label">Examen de Orina</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Glucosa">
                                    <label class="form-check-label">Glucosa en Sangre</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Colesterol">
                                    <label class="form-check-label">Perfil Lipídico</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Rayos X">
                                    <label class="form-check-label">Rayos X</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Ultrasonido">
                                    <label class="form-check-label">Ultrasonido</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Electrocardiograma">
                                    <label class="form-check-label">Electrocardiograma</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="examenes[]" value="Tomografia">
                                    <label class="form-check-label">Tomografía</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Exámenes Específicos (Personalizados)</label>
                        <textarea name="examenes_personalizados" class="form-control" rows="3" placeholder="Describa exámenes específicos requeridos..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instrucciones Especiales</label>
                        <textarea name="instrucciones" class="form-control" rows="2" placeholder="Instrucciones especiales para los exámenes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Atender Consulta -->
<div class="modal fade" id="modalAtenderConsulta" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atención de Consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAtenderConsulta">
                @csrf
                <input type="hidden" name="consulta_id" id="consulta_id_atender">
                
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h6>Información del Paciente</h6>
                            <div id="infoPacienteConsulta" class="bg-light p-3 rounded">
                                <!-- Información dinámica del paciente -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Datos de Consulta</h6>
                            <div id="infoConsulta" class="bg-light p-3 rounded">
                                <!-- Información dinámica de la consulta -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Síntomas Principales</label>
                                <textarea name="sintomas" class="form-control" rows="3" placeholder="Describa los síntomas presentados..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Examen Físico</label>
                                <textarea name="examen_fisico" class="form-control" rows="3" placeholder="Hallazgos del examen físico..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control" rows="3" placeholder="Diagnóstico principal..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tratamiento Indicado</label>
                                <textarea name="tratamiento" class="form-control" rows="3" placeholder="Plan de tratamiento..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones y Recomendaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales y recomendaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" id="btnGenerarReceta">Generar Receta</button>
                    <button type="submit" class="btn btn-success">Finalizar Consulta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Historial -->
<div class="modal fade" id="modalVerHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial Médico del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoHistorial">
                    <!-- Contenido dinámico del historial -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dar Alta -->
<div class="modal fade" id="modalDarAlta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dar de Alta Paciente Hospitalizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDarAlta">
                @csrf
                <input type="hidden" name="hospitalizacion_id" id="hospitalizacion_id_alta">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Está a punto de dar de alta a un paciente hospitalizado.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <div id="infoPacienteAlta" class="form-control bg-light" style="min-height: 38px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnóstico Final *</label>
                        <textarea name="diagnostico_final" class="form-control" rows="3" placeholder="Diagnóstico final y estado al alta..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Recomendaciones al Alta</label>
                        <textarea name="recomendaciones" class="form-control" rows="2" placeholder="Recomendaciones para el paciente..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Alta</label>
                        <input type="datetime-local" name="fecha_alta" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Alta</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endif

@include('include.footer')

<!-- JavaScript específico para médico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${tipo} alert-dismissible fade show`;
        alert.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // ========== BÚSQUEDA DE PACIENTES - CORREGIDA ==========
    const buscarPacienteInput = document.getElementById('buscarPaciente');
    if (buscarPacienteInput) {
        let timeoutId;
        
        buscarPacienteInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            const query = this.value.trim();
            
            if (query.length < 2) {
                document.getElementById('resultadosPacientes').style.display = 'none';
                return;
            }
            
            timeoutId = setTimeout(() => {
                fetch(`/doctor/buscar-pacientes?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const resultadosDiv = document.getElementById('resultadosPacientes');
                        if (data.success && data.pacientes && data.pacientes.length > 0) {
                            let html = '<div class="list-group">';
                            data.pacientes.forEach(paciente => {
                                html += `
                                    <button type="button" class="list-group-item list-group-item-action" 
                                            onclick="seleccionarPaciente(${paciente.paciente_id}, '${paciente.nombre} ${paciente.paterno} ${paciente.materno || ''}')">
                                        ${paciente.nombre} ${paciente.paterno} ${paciente.materno || ''}
                                        <small class="text-muted d-block">ID: ${paciente.paciente_id}</small>
                                    </button>
                                `;
                            });
                            html += '</div>';
                            resultadosDiv.innerHTML = html;
                            resultadosDiv.style.display = 'block';
                        } else {
                            resultadosDiv.innerHTML = '<div class="text-muted p-2">No se encontraron pacientes</div>';
                            resultadosDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error en búsqueda:', error);
                        document.getElementById('resultadosPacientes').innerHTML = 
                            '<div class="text-danger p-2">Error en la búsqueda</div>';
                        document.getElementById('resultadosPacientes').style.display = 'block';
                    });
            }, 500);
        });

        // Ocultar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#buscarPaciente') && !e.target.closest('#resultadosPacientes')) {
                document.getElementById('resultadosPacientes').style.display = 'none';
            }
        });
    }

    // ========== BOTONES VER - CORREGIDOS ==========
    
    // Botón VER PACIENTE
    document.querySelectorAll('.btn-ver-paciente').forEach(btn => {
        btn.addEventListener('click', function() {
            const pacienteId = this.getAttribute('data-id');
            console.log('Cargando paciente ID:', pacienteId);
            
            fetch(`/doctor/pacientes/${pacienteId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar paciente');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const paciente = data.paciente;
                        let edad = 'N/A';
                        if (paciente.fec_nac) {
                            const nacimiento = new Date(paciente.fec_nac);
                            const hoy = new Date();
                            edad = hoy.getFullYear() - nacimiento.getFullYear();
                            const mes = hoy.getMonth() - nacimiento.getMonth();
                            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                                edad--;
                            }
                            edad += ' años';
                        }
                        
                        const contenido = `
                            <div class="alert alert-info">
                                <h6>Detalles Completos del Paciente</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID Paciente:</strong> ${paciente.paciente_id}</p>
                                    <p><strong>Nombre Completo:</strong> ${paciente.nombre} ${paciente.paterno} ${paciente.materno || ''}</p>
                                    <p><strong>Username:</strong> ${paciente.username}</p>
                                    <p><strong>Fecha Nacimiento:</strong> ${paciente.fec_nac || 'No especificada'}</p>
                                    <p><strong>Edad:</strong> ${edad}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Género:</strong> ${paciente.genero === 'M' ? 'Masculino' : 'Femenino'}</p>
                                    <p><strong>Correo:</strong> ${paciente.correo}</p>
                                    <p><strong>Teléfono:</strong> ${paciente.telefono || 'No especificado'}</p>
                                    <p><strong>Tipo Sangre:</strong> ${paciente.tipo_de_sangre || 'No especificado'}</p>
                                </div>
                            </div>
                            ${paciente.antecedentes ? `<div class="mt-3"><strong>Antecedentes:</strong><div class="bg-light p-2 rounded">${paciente.antecedentes}</div></div>` : ''}
                            ${paciente.alergias ? `<div class="mt-2"><strong>Alergias:</strong><div class="bg-light p-2 rounded">${paciente.alergias}</div></div>` : ''}
                            ${paciente.diagnosticos ? `<div class="mt-2"><strong>Diagnósticos Previos:</strong><div class="bg-light p-2 rounded">${paciente.diagnosticos}</div></div>` : ''}
                        `;
                        
                        // Mostrar en modal existente
                        document.getElementById('contenidoHistorial').innerHTML = contenido;
                        const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
                        modal.show();
                    } else {
                        mostrarAlerta(data.message || 'Error al cargar paciente', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar información del paciente', 'danger');
                });
        });
    });

    // Botón VER HISTORIAL (desde consultas)
    document.querySelectorAll('.btn-ver-historial').forEach(btn => {
        btn.addEventListener('click', function() {
            const pacienteId = this.getAttribute('data-paciente-id');
            console.log('Cargando historial del paciente ID:', pacienteId);
            
            // Primero obtenemos la información básica del paciente
            fetch(`/doctor/pacientes/${pacienteId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar paciente');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const paciente = data.paciente;
                        let edad = 'N/A';
                        if (paciente.fec_nac) {
                            const nacimiento = new Date(paciente.fec_nac);
                            const hoy = new Date();
                            edad = hoy.getFullYear() - nacimiento.getFullYear();
                            const mes = hoy.getMonth() - nacimiento.getMonth();
                            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                                edad--;
                            }
                            edad += ' años';
                        }
                        
                        const contenido = `
                            <h6>Información del Paciente</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre:</strong> ${paciente.nombre} ${paciente.paterno} ${paciente.materno || ''}</p>
                                    <p><strong>Edad:</strong> ${edad}</p>
                                    <p><strong>Tipo Sangre:</strong> ${paciente.tipo_de_sangre || 'No especificado'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Correo:</strong> ${paciente.correo}</p>
                                    <p><strong>Teléfono:</strong> ${paciente.telefono || 'No especificado'}</p>
                                    <p><strong>Género:</strong> ${paciente.genero === 'M' ? 'Masculino' : 'Femenino'}</p>
                                </div>
                            </div>
                            ${paciente.antecedentes ? `<div class="mt-3"><strong>Antecedentes:</strong><div class="bg-light p-2 rounded">${paciente.antecedentes}</div></div>` : ''}
                            ${paciente.alergias ? `<div class="mt-2"><strong>Alergias:</strong><div class="bg-light p-2 rounded">${paciente.alergias}</div></div>` : ''}
                            ${paciente.diagnosticos ? `<div class="mt-2"><strong>Diagnósticos:</strong><div class="bg-light p-2 rounded">${paciente.diagnosticos}</div></div>` : ''}
                            
                            ${data.consultas && data.consultas.length > 0 ? `
                                <div class="mt-3">
                                    <h6>Últimas Consultas</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Motivo</th>
                                                    <th>Médico</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.consultas.map(consulta => `
                                                    <tr>
                                                        <td>${consulta.fecha}</td>
                                                        <td>${consulta.motivo_consulta}</td>
                                                        <td>${consulta.medico_nombre} ${consulta.medico_paterno}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            ` : ''}
                        `;
                        
                        document.getElementById('contenidoHistorial').innerHTML = contenido;
                        const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
                        modal.show();
                    } else {
                        mostrarAlerta(data.message || 'Error al cargar historial', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar historial del paciente', 'danger');
                });
        });
    });

    // Botón VER RECETA
    document.querySelectorAll('.btn-ver-receta').forEach(btn => {
        btn.addEventListener('click', function() {
            const recetaId = this.getAttribute('data-id');
            console.log('Cargando receta ID:', recetaId);
            
            fetch(`/doctor/recetas/${recetaId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar receta');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const receta = data.receta;
                        const contenido = `
                            <h6>Detalles de Receta Médica</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID Receta:</strong> ${receta.receta_id}</p>
                                    <p><strong>Medicamento:</strong> ${receta.medicamento_nombre}</p>
                                    <p><strong>Dosis:</strong> ${receta.dosis}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Duración:</strong> ${receta.duracion}</p>
                                    <p><strong>Frecuencia:</strong> ${receta.frecuencia}</p>
                                    <p><strong>Paciente:</strong> ${receta.paciente_nombre} ${receta.paciente_paterno}</p>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('contenidoHistorial').innerHTML = contenido;
                        const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
                        modal.show();
                    } else {
                        mostrarAlerta(data.message || 'Error al cargar receta', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar receta', 'danger');
                });
        });
    });

    // ========== FUNCIÓN GLOBAL PARA SELECCIONAR PACIENTE ==========
    window.seleccionarPaciente = function(pacienteId, nombreCompleto) {
        document.getElementById('paciente_id').value = pacienteId;
        document.getElementById('pacienteSeleccionado').innerHTML = nombreCompleto;
        document.getElementById('pacienteSeleccionado').style.display = 'block';
        document.getElementById('resultadosPacientes').style.display = 'none';
        document.getElementById('buscarPaciente').value = '';
    };

    // Resto del código JavaScript permanece igual...
    // [Mantén aquí el resto de tu código JavaScript existente]
});

</script>