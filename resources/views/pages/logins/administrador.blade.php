@include('include.header')
@include('include.navbar')

@if(!session('logged_in') || session('rol') !== 'administrativo')
    <div class="container mt-5">
        <div class="alert alert-warning text-center">
            <h4>Acceso no autorizado</h4>
            <p>Debe iniciar sesión como administrativo para acceder a esta página.</p>
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
                    <div class="profile-circle mb-3 mx-auto" style="width: 80px; height: 80px; border-radius: 50%;"><img src="assets/img/usuario_login_icon.jpg" alt="" style="width: 80px; height: 80px; background: #ccc; border-radius: 50%;"></div>
                    <h4 class="mb-1">{{ session('username') ?? 'Usuario' }}</h4>
                    <p class="text-muted mb-1">ID: {{ session('usuario_id') }}</p>
                    <p class="text-success fw-bold">{{ ucfirst(session('rol')) }}</p>
                    <p class="text-muted mb-1">Nombre: {{ session('nombre') }}</p>
                    <p class="text-muted mb-1">Apellido: {{ session('paterno') }} {{ session('materno') }}</p>
                    <p class="text-muted mb-1">Correo: {{ session('correo') }}</p>
                    <p class="text-muted mb-1">Teléfono: {{ session('telefono') }}</p>
                    
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
                <div class="top-buttons mb-3 d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                        CREAR USUARIO
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#areasModal">
                        Ver Áreas y Administradores
                    </button>
                    
                    <button type="button" class="btn btn-dark" id="btnMostrarAuditoria">
                        <i class="fas fa-history"></i> AUDITORÍA
                    </button>
                </div>
                
                {{-- TABS --}}
                <ul class="nav nav-tabs mb-2 tab-custom">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-usuarios">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-medicos">Médicos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-pacientes">Pacientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-admins">Administradores</a>
                    </li>
                </ul>

                {{-- CONTENIDO DE TABS --}}
                <div class="tab-content">
                    {{-- TAB USUARIOS --}}
                    <div class="tab-pane fade show active" id="tab-usuarios">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Usuarios del Sistema</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usuarios as $usuario)
                                        <tr>
                                            <td>{{ $usuario->usuario_id }}</td>
                                            <td>{{ $usuario->username }}</td>
                                            <td>{{ $usuario->nombre }} {{ $usuario->paterno }} {{ $usuario->materno }}</td>
                                            <td>{{ $usuario->correo }}</td>
                                            <td>{{ $usuario->telefono }}</td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $usuario->usuario_id }}">
                                                    VER
                                                </button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $usuario->usuario_id }}">
                                                    EDITAR
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" 
                                                        data-id="{{ $usuario->usuario_id }}" 
                                                        data-username="{{ $usuario->username }}">
                                                    ELIMINAR
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($usuarios->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No hay usuarios registrados</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB MÉDICOS --}}
                    <div class="tab-pane fade" id="tab-medicos">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Médicos del Sistema</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Médico</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Especialidad</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($medicos as $medico)
                                        <tr>
                                            <td>{{ $medico->medico_id }}</td>
                                            <td>{{ $medico->username }}</td>
                                            <td>{{ $medico->nombre }} {{ $medico->paterno }} {{ $medico->materno }}</td>
                                            <td>{{ $medico->correo }}</td>
                                            <td>{{ $medico->telefono }}</td>
                                            <td>{{ $medico->especialidad ?? 'General' }}</td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $medico->usuario_id }}">
                                                    VER
                                                </button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $medico->usuario_id }}">
                                                    EDITAR
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" 
                                                        data-id="{{ $medico->usuario_id }}" 
                                                        data-username="{{ $medico->username }}">
                                                    ELIMINAR
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($medicos->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No hay médicos registrados</td>
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
                            <h3 class="text-center mb-4">Pacientes Registrados</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Paciente</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Género</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pacientes as $paciente)
                                        <tr>
                                            <td>{{ $paciente->paciente_id }}</td>
                                            <td>{{ $paciente->username }}</td>
                                            <td>{{ $paciente->nombre }} {{ $paciente->paterno }} {{ $paciente->materno }}</td>
                                            <td>{{ $paciente->correo }}</td>
                                            <td>{{ $paciente->telefono }}</td>
                                            <td>{{ $paciente->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $paciente->usuario_id }}">
                                                    VER
                                                </button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $paciente->usuario_id }}">
                                                    EDITAR
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" 
                                                        data-id="{{ $paciente->usuario_id }}" 
                                                        data-username="{{ $paciente->username }}">
                                                    ELIMINAR
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($pacientes->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No hay pacientes registrados</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB ADMINISTRADORES --}}
                    <div class="tab-pane fade" id="tab-admins">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Personal Administrativo</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Admin</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Cargo</th>
                                            <th>Área</th>
                                            <th>Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($administrativos as $admin)
                                        <tr>
                                            <td>{{ $admin->administrativo_id }}</td>
                                            <td>{{ $admin->username }}</td>
                                            <td>{{ $admin->nombre }} {{ $admin->paterno }} {{ $admin->materno }}</td>
                                            <td>{{ $admin->correo }}</td>
                                            <td>{{ $admin->telefono }}</td>
                                            <td>{{ $admin->cargo }}</td>
                                            <td>{{ $admin->nombre_area }}</td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $admin->usuario_id }}">
                                                    VER
                                                </button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $admin->usuario_id }}">
                                                    EDITAR
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" 
                                                        data-id="{{ $admin->usuario_id }}" 
                                                        data-username="{{ $admin->username }}">
                                                    ELIMINAR
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($administrativos->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No hay personal administrativo registrado</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Buscador Dinámico en Tiempo Real -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-search me-2"></i>Buscador
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="tabla_buscar" class="form-label">Tabla a Buscar</label>
                                    <select class="form-select" id="tabla_buscar" name="tabla_buscar">
                                        <option value="">Seleccione una tabla...</option>
                                        <option value="usuarios">Usuarios</option>
                                        <option value="pacientes">Pacientes</option>
                                        <option value="medicos">Médicos</option>
                                        <option value="administrativos">Administrativos</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="nombre_buscar" class="form-label">Buscar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="nombre_buscar" 
                                            placeholder="Escriba para buscar en tiempo real... (mínimo 3 caracteres)">
                                        <span class="input-group-text" id="contador-caracteres">0</span>
                                    </div>
                                    <div class="form-text">La búsqueda se activa automáticamente después de 3 caracteres</div>
                                </div>
                            </div>
                            
                            <!-- Resultados de la búsqueda -->
                            <div id="resultadosBusqueda" class="mt-4" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <h6 class="mb-0">Resultados de la Búsqueda</h6>
                                    <span id="contador-resultados" class="badge bg-primary">0 resultados</span>
                                </div>
                                <div id="contenidoResultados"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>



<!--||||||||||||||||||||||||||||||||||||||||||||||-->
<!-- MODALESSSSSSSSSSSSSSSSSSSSSSSSSSS -->
<!--||||||||||||||||||||||||||||||||||||||||||||||-->



<!-- ========== MODAL DE AUDITORÍA ========== -->
<div class="modal fade" id="modalAuditoria" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history"></i> Registro de Auditoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Este es un registro de solo lectura. Muestra todas las actividades del sistema.
                </div>
                
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="filtroAuditoria" class="form-control" placeholder="Buscar por usuario o acción...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroDias" class="form-select">
                            <option value="7">Últimos 7 días</option>
                            <option value="30" selected>Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                            <option value="365">Último año</option>
                            <option value="all">Todo el historial</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnRefrescarAuditoria" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt"></i> Refrescar
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="badge bg-info">
                            <span id="contadorAuditoria">0</span> registros
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de auditoría -->
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-sm" id="tablaAuditoria">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th width="15%">
                                    <i class="fas fa-calendar"></i> Fecha/Hora
                                </th>
                                <th width="20%">
                                    <i class="fas fa-user"></i> Usuario
                                </th>
                                <th width="50%">
                                    <i class="fas fa-tasks"></i> Acción
                                </th>
                                <th width="15%">
                                    <i class="fas fa-desktop"></i> IP
                                </th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoAuditoria">
                            <!-- Los datos se cargarán aquí -->
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    Cargando registros de auditoría...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <small class="text-muted" id="infoPaginacion"></small>
                    </div>
                    <div class="col-md-6 text-end">
                        <nav aria-label="Paginación auditoría">
                            <ul class="pagination pagination-sm justify-content-end" id="paginacionAuditoria">
                                <!-- La paginación se generará aquí -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="exportarAuditoria()">
                    <i class="fas fa-download"></i> Exportar CSV
                </button>
            </div>
        </div>
    </div>
</div>
 <!-- Botón para abrir modal de áreas -->
<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#areasModal">
        <i class="fas fa-building me-2"></i>Ver Áreas y Administradores
    </button>
</div>

<!-- Modal Principal de Áreas -->
<div class="modal fade" id="areasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Áreas y Administradores</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(isset($areas) && count($areas) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Área</th>
                                <th>Descripción</th>
                                <th>Administradores</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                            <tr>
                                <td>{{ $area->area_id }}</td>
                                <td><strong>{{ $area->nombre_area }}</strong></td>
                                <td><small class="text-muted">{{ $area->descripcion ?: 'Sin descripción' }}</small></td>
                                <td>
                                    <span class="badge bg-info">{{ $area->administradores_count }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-ver-administradores" 
                                            data-area-id="{{ $area->area_id }}"
                                            data-area-name="{{ $area->nombre_area }}">
                                        <i class="fas fa-users me-1"></i>Ver
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-warning">No hay áreas registradas.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle (Este es el que muestra los últimos 4 administradores) -->
<div class="modal fade" id="detalleAdministradoresModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Administradores del Área</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenido-administradores">
                    <!-- Aquí se cargarán los últimos 4 administradores -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL CREAR USUARIO -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearUsuario">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Paterno *</label>
                                <input type="text" name="paterno" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="materno" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Correo *</label>
                                <input type="email" name="correo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Género</label>
                                <select name="genero" class="form-select">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER USUARIO -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoVerUsuario">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarUsuario">
                @csrf
                @method('PUT')
                <input type="hidden" name="usuario_id" id="editar_usuario_id">
                <div class="modal-body" id="contenidoEditarUsuario">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR USUARIO -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoEliminarUsuario">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado - Inicializando botones...');
    
    document.addEventListener('click', function(event) {
        if (event.target.closest('.btn-ver-administradores')) {
            event.preventDefault();
            const button = event.target.closest('.btn-ver-administradores');
            const areaId = button.getAttribute('data-area-id');
            const areaName = button.getAttribute('data-area-name');
            
            console.log('🟡 Botón clickeado - Área:', areaId, areaName);
            
            // Cerrar el modal actual de áreas
            const areasModal = bootstrap.Modal.getInstance(document.getElementById('areasModal'));
            if (areasModal) {
                areasModal.hide();
            }
            
            // Mostrar loading
            document.getElementById('contenido-administradores').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando administradores de <strong>${areaName}</strong>...</p>
                </div>
            `;
            
            // Mostrar el modal de detalle
            const detalleModal = new bootstrap.Modal(document.getElementById('detalleAdministradoresModal'));
            detalleModal.show();
            
            // Actualizar título
            document.querySelector('#detalleAdministradoresModal .modal-title').innerHTML = 
                `<i class="fas fa-users me-2"></i>Administradores - ${areaName}`;
            
            // Hacer la petición AJAX con la nueva ruta
            cargarAdministradores(areaId, areaName);
        }
    });
    
    function cargarAdministradores(areaId, areaName) {
        console.log('🟡 Haciendo petición para área:', areaId);
        
        // Usar la nueva ruta
        fetch(`/admin/areas/${areaId}/administradores`)
            .then(response => {
                console.log('🔵 Status de respuesta:', response.status);
                if (!response.ok) {
                    // Obtener más detalles del error
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}. Respuesta: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Datos recibidos:', data);
                
                if (data.success) {
                    mostrarUltimosAdministradores(data, areaName);
                } else {
                    throw new Error(data.message || 'Error en la respuesta del servidor');
                }
            })
            .catch(error => {
                console.error('❌ Error completo:', error);
                document.getElementById('contenido-administradores').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error al cargar los administradores</strong>
                        <br>
                        <small>${error.message}</small>
                        <br>
                        <button onclick="location.reload()" class="btn btn-sm btn-outline-danger mt-2">
                            <i class="fas fa-redo me-1"></i>Reintentar
                        </button>
                    </div>
                `;
            });
    }
    
    function mostrarUltimosAdministradores(data, areaName) {
        let html = '';
        
        // Información del área
        html += `
            <div class="alert alert-primary">
                <h6 class="alert-heading">
                    <i class="fas fa-building me-2"></i>${areaName}
                </h6>
                <p class="mb-1"><strong>Descripción:</strong> ${data.area.descripcion}</p>
                <p class="mb-0"><strong>Total de administradores:</strong> ${data.total_administradores}</p>
            </div>
        `;
        
        if (data.ultimos_administradores && data.ultimos_administradores.length > 0) {
            html += `<div class="row">`;
            
            data.ultimos_administradores.forEach((admin, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i>ID: ${admin.id}
                                    </small>
                                    <span class="badge bg-success">#${index + 1}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-user me-1"></i>${admin.nombre_completo}
                                </h6>
                                <p class="card-text mb-2">
                                    <i class="fas fa-briefcase me-1 text-muted"></i>
                                    <strong>Cargo:</strong> ${admin.cargo}
                                </p>
                                <p class="card-text mb-2">
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    ${admin.email}
                                </p>
                                <p class="card-text mb-0">
                                    <i class="fas fa-phone me-1 text-muted"></i>
                                    ${admin.telefono || 'No especificado'}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
            
            // Mensaje si hay más administradores
            if (data.total_administradores > data.ultimos_administradores.length) {
                html += `
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Mostrando los últimos ${data.ultimos_administradores.length} administradores.
                        ${data.total_administradores - data.ultimos_administradores.length} administradores más en esta área.
                    </div>
                `;
            }
        } else {
            html = `
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay administradores en esta área</h5>
                    <p class="text-muted">No se encontraron administradores asignados a "${areaName}"</p>
                </div>
            `;
        }
        
        document.getElementById('contenido-administradores').innerHTML = html;
    }
});
</script>


<!--||||||||||||||||||||||||||||||||||||||||||||||-->
<!-- JavaScript para manejar los modales -->
<!--||||||||||||||||||||||||||||||||||||||||||||||-->



<script>
// ========== FUNCIONALIDAD DE AUDITORÍA ==========

// Variables globales para auditoría
let registrosAuditoria = [];
let paginaActualAuditoria = 1;
const registrosPorPagina = 20;

// Botón para abrir auditoría
document.getElementById('btnMostrarAuditoria')?.addEventListener('click', function() {
    cargarAuditoria();
    const modal = new bootstrap.Modal(document.getElementById('modalAuditoria'));
    modal.show();
});

// Cargar datos de auditoría
function cargarAuditoria() {
    const dias = document.getElementById('filtroDias').value;
    const cuerpo = document.getElementById('cuerpoAuditoria');
    const contador = document.getElementById('contadorAuditoria');
    
    cuerpo.innerHTML = `
        <tr>
            <td colspan="4" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                Cargando registros de auditoría...
            </td>
        </tr>
    `;
    
    let url = '/auditoria';
    if (dias !== 'all') {
        url += `?dias=${dias}`;
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar auditoría');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                registrosAuditoria = data.auditoria;
                contador.textContent = data.total_registros || registrosAuditoria.length;
                paginaActualAuditoria = 1;
                aplicarFiltrosYMostrar();
            } else {
                cuerpo.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar auditoría
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            cuerpo.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error de conexión
                    </td>
                </tr>
            `;
        });
}

// Aplicar filtros y mostrar resultados
function aplicarFiltrosYMostrar() {
    const filtroTexto = document.getElementById('filtroAuditoria').value.toLowerCase();
    
    let registrosFiltrados = registrosAuditoria;
    
    // Aplicar filtro de texto
    if (filtroTexto) {
        registrosFiltrados = registrosAuditoria.filter(reg => 
            (reg.username && reg.username.toLowerCase().includes(filtroTexto)) ||
            (reg.nombre && reg.nombre.toLowerCase().includes(filtroTexto)) ||
            (reg.accion && reg.accion.toLowerCase().includes(filtroTexto)) ||
            (reg.nombre_completo && reg.nombre_completo.toLowerCase().includes(filtroTexto))
        );
    }
    
    // Actualizar contador
    document.getElementById('contadorAuditoria').textContent = registrosFiltrados.length;
    
    // Calcular paginación
    const totalPaginas = Math.ceil(registrosFiltrados.length / registrosPorPagina);
    const inicio = (paginaActualAuditoria - 1) * registrosPorPagina;
    const fin = inicio + registrosPorPagina;
    const registrosPagina = registrosFiltrados.slice(inicio, fin);
    
    // Mostrar registros
    mostrarRegistrosAuditoria(registrosPagina);
    
    // Actualizar paginación
    actualizarPaginacionAuditoria(totalPaginas);
    
    // Actualizar información de paginación
    document.getElementById('infoPaginacion').textContent = 
        `Mostrando ${inicio + 1} - ${Math.min(fin, registrosFiltrados.length)} de ${registrosFiltrados.length} registros`;
}

// Mostrar registros en la tabla
function mostrarRegistrosAuditoria(registros) {
    const cuerpo = document.getElementById('cuerpoAuditoria');
    
    if (registros.length === 0) {
        cuerpo.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="fas fa-inbox"></i> No hay registros de auditoría
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    
    registros.forEach(registro => {
        // Determinar clase según el tipo de acción
        let claseAccion = 'accion-otro';
        if (registro.accion) {
            const accion = registro.accion.toLowerCase();
            if (accion.includes('crear') || accion.includes('creado') || accion.includes('insert')) {
                claseAccion = 'accion-crear';
            } else if (accion.includes('editar') || accion.includes('actualizar') || accion.includes('update')) {
                claseAccion = 'accion-editar';
            } else if (accion.includes('eliminar') || accion.includes('borrar') || accion.includes('delete')) {
                claseAccion = 'accion-eliminar';
            } else if (accion.includes('login') || accion.includes('sesión') || accion.includes('logout')) {
                claseAccion = 'accion-login';
            }
        }
        
        // Formatear fecha
        const fecha = new Date(registro.fecha);
        const fechaFormateada = fecha.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Nombre del usuario
        const nombreUsuario = registro.nombre_completo || 
                             (registro.nombre && registro.paterno ? `${registro.nombre} ${registro.paterno}` : 
                             registro.username || 'Usuario desconocido');
        
        // IP
        const ip = registro.ip_address || 'No registrada';
        
        html += `
            <tr class="auditoria-fila ${claseAccion}">
                <td class="fecha-auditoria">
                    <small>${fechaFormateada}</small>
                </td>
                <td>
                    <div class="usuario-auditoria">
                        <i class="fas fa-user-circle"></i> ${nombreUsuario}
                    </div>
                    <small class="text-muted">${registro.username || ''}</small>
                </td>
                <td>
                    <div class="accion-auditoria" title="${registro.accion || 'Sin descripción'}">
                        ${registro.accion || 'Acción no especificada'}
                    </div>
                    ${registro.auditoria_id ? `<small class="text-muted">ID: ${registro.auditoria_id}</small>` : ''}
                </td>
                <td>
                    <code class="ip-address" title="${ip}">
                        ${ip.substring(0, 15)}${ip.length > 15 ? '...' : ''}
                    </code>
                </td>
            </tr>
        `;
    });
    
    cuerpo.innerHTML = html;
}

// Actualizar paginación
function actualizarPaginacionAuditoria(totalPaginas) {
    const paginacion = document.getElementById('paginacionAuditoria');
    
    if (totalPaginas <= 1) {
        paginacion.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Botón anterior
    html += `
        <li class="page-item ${paginaActualAuditoria === 1 ? 'disabled' : ''}">
            <button class="page-link" onclick="cambiarPaginaAuditoria(${paginaActualAuditoria - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>
        </li>
    `;
    
    // Números de página
    const maxBotones = 5;
    let inicio = Math.max(1, paginaActualAuditoria - Math.floor(maxBotones / 2));
    let fin = Math.min(totalPaginas, inicio + maxBotones - 1);
    
    if (fin - inicio + 1 < maxBotones) {
        inicio = Math.max(1, fin - maxBotones + 1);
    }
    
    for (let i = inicio; i <= fin; i++) {
        html += `
            <li class="page-item ${i === paginaActualAuditoria ? 'active' : ''}">
                <button class="page-link" onclick="cambiarPaginaAuditoria(${i})">
                    ${i}
                </button>
            </li>
        `;
    }
    
    // Botón siguiente
    html += `
        <li class="page-item ${paginaActualAuditoria === totalPaginas ? 'disabled' : ''}">
            <button class="page-link" onclick="cambiarPaginaAuditoria(${paginaActualAuditoria + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>
        </li>
    `;
    
    paginacion.innerHTML = html;
}

// Cambiar página
function cambiarPaginaAuditoria(pagina) {
    if (pagina < 1 || pagina > Math.ceil(registrosAuditoria.length / registrosPorPagina)) {
        return;
    }
    
    paginaActualAuditoria = pagina;
    aplicarFiltrosYMostrar();
}

// Exportar auditoría a CSV
function exportarAuditoria() {
    if (registrosAuditoria.length === 0) {
        mostrarAlerta('No hay datos para exportar', 'warning');
        return;
    }
    
    // Crear contenido CSV
    let csv = 'Fecha,Usuario,Username,Acción,IP\n';
    
    registrosAuditoria.forEach(registro => {
        const fecha = new Date(registro.fecha).toLocaleString('es-ES');
        const nombre = (registro.nombre_completo || `${registro.nombre || ''} ${registro.paterno || ''}`).replace(/,/g, ' ');
        const username = registro.username || '';
        const accion = (registro.accion || '').replace(/,/g, ' ');
        const ip = registro.ip_address || '';
        
        csv += `"${fecha}","${nombre}","${username}","${accion}","${ip}"\n`;
    });
    
    // Crear archivo y descargar
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    
    link.setAttribute('href', url);
    link.setAttribute('download', `auditoria_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    mostrarAlerta('Auditoría exportada exitosamente', 'success');
}

// Event listeners para filtros
document.getElementById('filtroAuditoria')?.addEventListener('input', function() {
    aplicarFiltrosYMostrar();
});

document.getElementById('filtroDias')?.addEventListener('change', function() {
    cargarAuditoria();
});

document.getElementById('btnRefrescarAuditoria')?.addEventListener('click', function() {
    cargarAuditoria();
});

// Cargar auditoría automáticamente cuando se abre el modal
document.getElementById('modalAuditoria')?.addEventListener('shown.bs.modal', function() {
    cargarAuditoria();
});


document.addEventListener('DOMContentLoaded', function() {
    let usuarioIdEliminar = null;

    // Mostrar alertas
    function mostrarAlerta(mensaje, tipo = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            console.error('No se encontró alertContainer');
            return;
        }
        const alert = document.createElement('div');
        alert.className = `alert alert-${tipo} alert-dismissible fade show`;
        alert.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // Función auxiliar para obtener texto del área
    function getAreaText(areaId) {
        const areas = {
            1: 'Recepción',
            2: 'Archivo', 
            3: 'Contabilidad',
            4: 'Caja',
            5: 'Coordinación',
            6: 'RRHH'
        };
        return areas[areaId] || `Área ${areaId}`;
    }

    // ========== FUNCIONES GENÉRICAS ==========

    // Función genérica para VER cualquier usuario
    function verUsuario(usuarioId) {
        fetch(`/obtener-usuario-completo/${usuarioId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.success && data.usuario) {
                    const usuario = data.usuario;
                    const roles = data.roles;
                    
                    let contenidoHTML = `
                        <h6 class="border-bottom pb-2">Información del Usuario</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Usuario:</strong> ${usuario.usuario_id}</p>
                                <p><strong>Username:</strong> ${usuario.username}</p>
                                <p><strong>Nombre completo:</strong> ${usuario.nombre} ${usuario.paterno} ${usuario.materno || ''}</p>
                                <p><strong>Género:</strong> ${usuario.genero === 'M' ? 'Masculino' : 'Femenino'}</p>
                                <p><strong>Fecha nacimiento:</strong> ${usuario.fec_nac || 'No especificada'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Correo electrónico:</strong> ${usuario.correo}</p>
                                <p><strong>Teléfono:</strong> ${usuario.telefono || 'No especificado'}</p>
                                <p><strong>Dirección:</strong> ${usuario.calle || ''} ${usuario.zona || ''}</p>
                                <p><strong>Municipio:</strong> ${usuario.municipio || 'No especificado'}</p>
                            </div>
                        </div>
                    `;

                    // Añadir información de roles si existen
                    if (Object.keys(roles).length > 0) {
                        contenidoHTML += `<div class="row mt-3"><div class="col-12"><h6 class="border-bottom pb-2">Roles del Usuario</h6>`;
                        
                        if (roles.medico) {
                            contenidoHTML += `
                                <div class="alert alert-info mb-2">
                                    <strong>MÉDICO</strong><br>
                                    ID Médico: ${roles.medico.medico_id}<br>
                                    Especialidad: ${roles.medico.especialidad || 'General'}<br>
                                    ${roles.medico.num_colegiatura ? 'Colegiatura: ' + roles.medico.num_colegiatura : ''}
                                </div>
                            `;
                        }
                        
                        if (roles.paciente) {
                            contenidoHTML += `
                                <div class="alert alert-success mb-2">
                                    <strong>PACIENTE</strong><br>
                                    ID Paciente: ${roles.paciente.paciente_id}<br>
                                    Tipo de Sangre: ${roles.paciente.tipo_de_sangre || 'No especificado'}<br>
                                    ${roles.paciente.num_historial ? 'Historial: ' + roles.paciente.num_historial : ''}
                                </div>
                            `;
                        }
                        
                        if (roles.administrativo) {
                            const areaText = getAreaText(roles.administrativo.area_id);
                            contenidoHTML += `
                                <div class="alert alert-warning mb-2">
                                    <strong>ADMINISTRATIVO</strong><br>
                                    ID Admin: ${roles.administrativo.administrativo_id}<br>
                                    Cargo: ${roles.administrativo.cargo}<br>
                                    Área: ${areaText}
                                </div>
                            `;
                        }
                        
                        contenidoHTML += `</div></div>`;
                    }

                    document.getElementById('contenidoVerUsuario').innerHTML = contenidoHTML;
                    
                    // Mostrar modal con Bootstrap 5
                    const modal = new bootstrap.Modal(document.getElementById('modalVerUsuario'));
                    modal.show();
                } else {
                    mostrarAlerta(data.message || 'Error al cargar usuario', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'danger');
            });
    }

    // Función para mostrar/ocultar campos de rol
    function toggleCamposRol(rol) {
        // Ocultar todos los campos de roles
        document.querySelectorAll('.campos-medico, .campos-admin, .campos-paciente').forEach(el => {
            el.style.display = 'none';
        });
        
        // Mostrar campos del rol seleccionado
        if (rol) {
            document.querySelectorAll('.campos-' + rol).forEach(el => {
                el.style.display = 'block';
            });
        }
    }

    // Función genérica para EDITAR cualquier usuario
    function editarUsuario(usuarioId) {
        fetch(`/obtener-usuario-completo/${usuarioId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.success && data.usuario) {
                    const usuario = data.usuario;
                    const roles = data.roles;
                    
                    // Determinar rol actual
                    let rolActual = 'paciente';
                    if (roles.medico) rolActual = 'medico';
                    if (roles.administrativo) rolActual = 'administrativo';

                    document.getElementById('editar_usuario_id').value = usuario.usuario_id;
                    
                    const contenidoHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username *</label>
                                    <input type="text" name="username" class="form-control" value="${usuario.username}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" value="${usuario.nombre}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellido Paterno *</label>
                                    <input type="text" name="paterno" class="form-control" value="${usuario.paterno}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellido Materno</label>
                                    <input type="text" name="materno" class="form-control" value="${usuario.materno || ''}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Correo *</label>
                                    <input type="email" name="correo" class="form-control" value="${usuario.correo}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña (opcional)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                                    <small class="form-text text-muted">Solo llenar si deseas cambiar la contraseña</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="${usuario.telefono || ''}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Género</label>
                                    <select name="genero" class="form-select">
                                        <option value="M" ${usuario.genero === 'M' ? 'selected' : ''}>Masculino</option>
                                        <option value="F" ${usuario.genero === 'F' ? 'selected' : ''}>Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SECCIÓN DE ROLES -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2">Gestión de Roles</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Rol Principal</label>
                                    <select name="rol" class="form-select" id="selectRol">
                                        <option value="paciente" ${rolActual === 'paciente' ? 'selected' : ''}>Paciente</option>
                                        <option value="medico" ${rolActual === 'medico' ? 'selected' : ''}>Médico</option>
                                        <option value="administrativo" ${rolActual === 'administrativo' ? 'selected' : ''}>Administrativo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Campos específicos para MÉDICO -->
                            <div class="col-md-4 campos-medico" style="${rolActual === 'medico' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">Especialidad</label>
                                    <input type="text" name="especialidad" class="form-control" value="${roles.medico ? roles.medico.especialidad : ''}" placeholder="Ej: Cardiología">
                                </div>
                            </div>
                            <div class="col-md-4 campos-medico" style="${rolActual === 'medico' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">N° Colegiatura</label>
                                    <input type="text" name="num_colegiatura" class="form-control" value="${roles.medico ? roles.medico.num_colegiatura : ''}" placeholder="Número de colegiatura">
                                </div>
                            </div>
                            
                            <!-- Campos específicos para ADMINISTRATIVO -->
                            <div class="col-md-4 campos-admin" style="${rolActual === 'administrativo' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">Cargo</label>
                                    <input type="text" name="cargo" class="form-control" value="${roles.administrativo ? roles.administrativo.cargo : ''}" placeholder="Ej: Recepcionista">
                                </div>
                            </div>
                            <div class="col-md-4 campos-admin" style="${rolActual === 'administrativo' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">Área</label>
                                    <select name="area_id" class="form-select">
                                        <option value="1" ${roles.administrativo && roles.administrativo.area_id == 1 ? 'selected' : ''}>Recepción</option>
                                        <option value="2" ${roles.administrativo && roles.administrativo.area_id == 2 ? 'selected' : ''}>Archivo</option>
                                        <option value="3" ${roles.administrativo && roles.administrativo.area_id == 3 ? 'selected' : ''}>Contabilidad</option>
                                        <option value="4" ${roles.administrativo && roles.administrativo.area_id == 4 ? 'selected' : ''}>Caja</option>
                                        <option value="5" ${roles.administrativo && roles.administrativo.area_id == 5 ? 'selected' : ''}>Coordinación</option>
                                        <option value="6" ${roles.administrativo && roles.administrativo.area_id == 6 ? 'selected' : ''}>RRHH</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Campos específicos para PACIENTE -->
                            <div class="col-md-4 campos-paciente" style="${rolActual === 'paciente' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Sangre</label>
                                    <input type="text" name="tipo_sangre" class="form-control" value="${roles.paciente ? roles.paciente.tipo_de_sangre : ''}" placeholder="Ej: A+">
                                </div>
                            </div>
                            <div class="col-md-4 campos-paciente" style="${rolActual === 'paciente' ? '' : 'display: none;'}">
                                <div class="mb-3">
                                    <label class="form-label">N° Historial</label>
                                    <input type="text" name="num_historial" class="form-control" value="${roles.paciente ? roles.paciente.num_historial : ''}" placeholder="Número de historial">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Nacimiento</label>
                                    <input type="date" name="fec_nac" class="form-control" value="${usuario.fec_nac || ''}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Calle</label>
                                    <input type="text" name="calle" class="form-control" value="${usuario.calle || ''}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Zona</label>
                                    <input type="text" name="zona" class="form-control" value="${usuario.zona || ''}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Municipio</label>
                                    <input type="text" name="municipio" class="form-control" value="${usuario.municipio || ''}">
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('contenidoEditarUsuario').innerHTML = contenidoHTML;
                    
                    // Configurar el event listener para el select de roles
                    setTimeout(() => {
                        const selectRol = document.getElementById('selectRol');
                        if (selectRol) {
                            selectRol.addEventListener('change', function() {
                                toggleCamposRol(this.value);
                            });
                        }
                    }, 100);
                    
                    // Mostrar modal con Bootstrap 5
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                    modal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al cargar datos del usuario', 'danger');
            });
    }

    // Función genérica para ELIMINAR cualquier usuario
    function eliminarUsuario(usuarioId, username) {
        usuarioIdEliminar = usuarioId;

        fetch(`/verificar-eliminar-usuario/${usuarioId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('contenidoEliminarUsuario').innerHTML = `
                        <p>${data.mensaje}</p>
                        <p><strong>Usuario:</strong> ${username}</p>
                        ${data.referencias && data.referencias.length > 0 ? 
                            `<div class="alert alert-warning">
                                <strong>Referencias encontradas:</strong><br>
                                ${data.referencias.join(', ')}
                            </div>` : ''
                        }
                    `;
                    
                    const btnConfirmar = document.getElementById('btnConfirmarEliminar');
                    if (data.puede_eliminar) {
                        btnConfirmar.style.display = 'block';
                        btnConfirmar.onclick = confirmarEliminacion;
                    } else {
                        btnConfirmar.style.display = 'none';
                    }
                    
                    // Mostrar modal con Bootstrap 5
                    const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
                    modal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al verificar eliminación', 'danger');
            });
    }

    // ========== FUNCIÓN CONFIRMAR ELIMINACIÓN ==========
    function confirmarEliminacion() {
        if (!usuarioIdEliminar) {
            mostrarAlerta('No se ha seleccionado ningún usuario para eliminar', 'danger');
            return;
        }

        fetch(`/eliminar-usuario/${usuarioIdEliminar}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarAlerta(data.message);
                
                // Ocultar modal con Bootstrap 5
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                modal.hide();
                
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al eliminar usuario', 'danger');
        });
    }

    // ========== EVENT LISTENERS CORREGIDOS ==========

    // Botones VER
    document.querySelectorAll('.btn-ver').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const usuarioId = this.getAttribute('data-id');
            if (usuarioId) {
                verUsuario(usuarioId);
            }
        });
    });

    // Botones EDITAR
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const usuarioId = this.getAttribute('data-id');
            if (usuarioId) {
                editarUsuario(usuarioId);
            }
        });
    });

    // Botones ELIMINAR
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const usuarioId = this.getAttribute('data-id');
            const username = this.getAttribute('data-username');
            if (usuarioId && username) {
                eliminarUsuario(usuarioId, username);
            }
        });
    });

    // ========== FORMULARIOS ==========

    // Formulario crear usuario
    const formCrearUsuario = document.getElementById('formCrearUsuario');
    if (formCrearUsuario) {
        formCrearUsuario.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route("crear.usuario") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta(data.message);
                    
                    // Ocultar modal con Bootstrap 5
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearUsuario'));
                    modal.hide();
                    
                    this.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarAlerta(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al crear usuario', 'danger');
            });
        });
    }

    // Formulario editar usuario
    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editar_usuario_id').value;
        const formData = new FormData(this);

        fetch(`/actualizar-usuario/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta(data.message);
                
                // Ocultar modal con Bootstrap 5
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario'));
                modal.hide();
                
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al actualizar usuario', 'danger');
        });
    });
    
        
        // Limpiar modal cuando se cierre
        const detalleModal = document.getElementById('detalleAdministradoresModal');
        if (detalleModal) {
            detalleModal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('lista-administradores').innerHTML = '';
                document.getElementById('nombre-area').innerHTML = '';
            });
        }
    });
    // Función auxiliar para formatear números de teléfono (opcional)
    function formatearTelefono(telefono) {
        if (!telefono) return 'No especificado';
        // Formato básico: 123-456-7890
        return telefono.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    }
    // JavaScript para búsqueda en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const tablaSelect = document.getElementById('tabla_buscar');
        const nombreInput = document.getElementById('nombre_buscar');
        const contadorCaracteres = document.getElementById('contador-caracteres');
        const resultadosBusqueda = document.getElementById('resultadosBusqueda');
        const contenidoResultados = document.getElementById('contenidoResultados');
        const contadorResultados = document.getElementById('contador-resultados');

        let timeoutId;
        let busquedaActiva = false;

        // Actualizar contador de caracteres
        nombreInput.addEventListener('input', function() {
            const longitud = this.value.length;
            contadorCaracteres.textContent = longitud;
            
            // Cambiar color del contador
            if (longitud < 3) {
                contadorCaracteres.className = 'input-group-text text-muted';
            } else {
                contadorCaracteres.className = 'input-group-text text-success';
            }
        });

        // Búsqueda en tiempo real
        nombreInput.addEventListener('input', function() {
            const tabla = tablaSelect.value;
            const nombre = this.value.trim();
            
            // Limpiar timeout anterior
            clearTimeout(timeoutId);
            
            // Validaciones
            if (!tabla) {
                if (nombre.length > 0) {
                    mostrarAlerta('Por favor, seleccione una tabla primero.', 'warning');
                }
                return;
            }
            
            if (nombre.length < 3) {
                if (busquedaActiva) {
                    ocultarResultados();
                }
                return;
            }
            
            // Mostrar loading después de 500ms de inactividad
            timeoutId = setTimeout(() => {
                realizarBusqueda(tabla, nombre);
            }, 500);
        });

        // También buscar cuando cambia la tabla seleccionada
        tablaSelect.addEventListener('change', function() {
            const tabla = this.value;
            const nombre = nombreInput.value.trim();
            
            if (tabla && nombre.length >= 3) {
                realizarBusqueda(tabla, nombre);
            }
        });

        function realizarBusqueda(tabla, nombre) {
            busquedaActiva = true;
            
            // Mostrar loading
            contenidoResultados.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <p class="mt-2">Buscando "${nombre}" en ${tabla}...</p>
                </div>
            `;
            resultadosBusqueda.style.display = 'block';

            // Realizar búsqueda
            fetch(`/api/buscar/${tabla}?nombre=${encodeURIComponent(nombre)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarResultados(data.resultados, tabla, nombre);
                    } else {
                        throw new Error(data.message || 'Error en la búsqueda');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta(`Error al realizar la búsqueda: ${error.message}`, 'danger');
                });
        }

        function mostrarResultados(resultados, tabla, terminoBusqueda) {
            contadorResultados.textContent = `${resultados.length} resultado(s)`;
            
            if (resultados.length === 0) {
                contenidoResultados.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-search me-2"></i>
                        No se encontraron resultados para "<strong>${terminoBusqueda}</strong>" en la tabla ${tabla}.
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
            `;

            // Encabezados dinámicos según la tabla
            if (tabla === 'usuarios') {
                html += `
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                `;
            } else if (tabla === 'pacientes') {
                html += `
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Tipo Sangre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                `;
            } else if (tabla === 'medicos') {
                html += `
                    <th>ID</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Licencia</th>
                    <th>Email</th>
                    <th>Acciones</th>
                `;
            } else if (tabla === 'administrativos') {
                html += `
                    <th>ID</th>
                    <th>Administrativo</th>
                    <th>Cargo</th>
                    <th>Área</th>
                    <th>Email</th>
                    <th>Acciones</th>
                `;
            }

            html += `</tr></thead><tbody>`;

            // Filas de resultados
            resultados.forEach(item => {
                html += `<tr>`;
                
                if (tabla === 'usuarios') {
                    html += `
                        <td><small>${item.usuario_id}</small></td>
                        <td><strong>${resaltarCoincidencia(item.username, terminoBusqueda)}</strong></td>
                        <td>${resaltarCoincidencia(item.nombre, terminoBusqueda)} ${resaltarCoincidencia(item.paterno, terminoBusqueda)} ${item.materno ? resaltarCoincidencia(item.materno, terminoBusqueda) : ''}</td>
                        <td>${item.correo}</td>
                        <td>${item.telefono || 'N/A'}</td>
                    `;
                } else if (tabla === 'pacientes') {
                    html += `
                        <td><small>${item.paciente_id}</small></td>
                        <td><strong>${resaltarCoincidencia(item.nombre, terminoBusqueda)} ${resaltarCoincidencia(item.paterno, terminoBusqueda)} ${item.materno ? resaltarCoincidencia(item.materno, terminoBusqueda) : ''}</strong></td>
                        <td>${item.tipo_de_sangre || 'N/A'}</td>
                        <td>${item.correo}</td>
                        <td>${item.telefono || 'N/A'}</td>
                    `;
                } else if (tabla === 'medicos') {
                    html += `
                        <td><small>${item.medico_id}</small></td>
                        <td><strong>Dr. ${resaltarCoincidencia(item.nombre, terminoBusqueda)} ${resaltarCoincidencia(item.paterno, terminoBusqueda)}</strong></td>
                        <td><span class="badge bg-info">${resaltarCoincidencia(item.especialidad, terminoBusqueda)}</span></td>
                        <td>${item.nro_licencia || 'N/A'}</td>
                        <td>${item.correo}</td>
                    `;
                } else if (tabla === 'administrativos') {
                    html += `
                        <td><small>${item.administrativo_id}</small></td>
                        <td><strong>${resaltarCoincidencia(item.nombre, terminoBusqueda)} ${resaltarCoincidencia(item.paterno, terminoBusqueda)}</strong></td>
                        <td>${resaltarCoincidencia(item.cargo, terminoBusqueda)}</td>
                        <td>${item.nombre_area || 'N/A'}</td>
                        <td>${item.correo}</td>
                    `;
                }

                // Botones de acción
                html += `
                    <td>
                        <button class="btn btn-sm btn-outline-primary ver-detalle" 
                                data-id="${tabla === 'usuarios' ? item.usuario_id : (tabla === 'pacientes' ? item.paciente_id : (tabla === 'medicos' ? item.medico_id : item.administrativo_id))}" 
                                data-tabla="${tabla}"
                                title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
            contenidoResultados.innerHTML = html;

            // Agregar event listeners a los botones de ver detalle
            document.querySelectorAll('.ver-detalle').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const tabla = this.getAttribute('data-tabla');
                    verDetalleUsuario(id, tabla);
                });
            });
        }

        function resaltarCoincidencia(texto, busqueda) {
            if (!texto || !busqueda) return texto;
            
            const regex = new RegExp(`(${busqueda})`, 'gi');
            return texto.toString().replace(regex, '<mark class="bg-warning">$1</mark>');
        }

        function ocultarResultados() {
            resultadosBusqueda.style.display = 'none';
            busquedaActiva = false;
        }

        function mostrarAlerta(mensaje, tipo = 'danger') {
            contenidoResultados.innerHTML = `
                <div class="alert alert-${tipo}">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${mensaje}
                </div>
            `;
            resultadosBusqueda.style.display = 'block';
            busquedaActiva = true;
        }

        function verDetalleUsuario(id, tabla) {
            // Aquí puedes implementar la lógica para ver detalles
            alert(`Ver detalle del ID: ${id} en tabla: ${tabla}`);
            // Por ejemplo: window.location.href = `/admin/usuarios/${id}/editar`;
        }

        // Limpiar búsqueda cuando el input se borra
        nombreInput.addEventListener('keyup', function() {
            if (this.value.length === 0 && busquedaActiva) {
                ocultarResultados();
            }
        });
    });
    
</script>
@include('include.footer')


