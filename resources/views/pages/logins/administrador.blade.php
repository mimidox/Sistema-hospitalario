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
                    <a href="#" class="btn" style="background:#d18cf6; color:white;">ÁREAS Y RECURSOS</a>
                    <a href="#" class="btn btn-primary">CITAS</a>
                    <a href="#" class="btn btn-danger" style="background:#ff7c96;">PACIENTES HOSPITALIZADOS</a>
                    <a href="#" class="btn btn-danger">AUDITORÍA</a>
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
                                            <th>Género</th>
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
                                            <td>{{ $medico->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
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
                                            <th>Fecha Nac.</th>
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
                                            <td>{{ $paciente->fec_nac ? \Carbon\Carbon::parse($paciente->fec_nac)->format('d/m/Y') : 'N/A' }}</td>
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
                                            <td>
                                                @switch($admin->area_id)
                                                    @case(1) Recepción @break
                                                    @case(2) Archivo @break
                                                    @case(3) Contabilidad @break
                                                    @case(4) Caja @break
                                                    @case(5) Coordinación @break
                                                    @case(6) RRHH @break
                                                    @default Área {{ $admin->area_id }}
                                                @endswitch
                                            </td>
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
                </div>
            </div>
        </div>
    </div>
</main>

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

@include('include.footer')
<!-- JavaScript para manejar los modales -->
<script>
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
});
</script>