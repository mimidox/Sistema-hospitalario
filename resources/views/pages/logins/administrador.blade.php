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
                    <div class="profile-circle mb-3 mx-auto" style="width: 80px; height: 80px; background: #ccc; border-radius: 50%;"></div>
                    <h4 class="mb-1">{{ session('username') ?? 'Usuario' }}</h4>
                    <p class="text-muted mb-1">ID: {{ session('usuario_id') }}</p>
                    <p class="text-success fw-bold">{{ ucfirst(session('rol')) }}</p>
                    
                    {{-- BOTÓN CERRAR SESIÓN --}}
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar Sesión</button>
                    </form>
                </div>
            </div>

            {{-- PANEL PRINCIPAL --}}
            <div class="col-9">
                {{-- MENSAJES DE ALERTA --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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
                                                <a href="#" class="btn btn-success btn-sm">VER</a>
                                                <a href="#" class="btn btn-primary btn-sm">EDITAR</a>
                                                <form action="{{ route('eliminar.usuario', $usuario->usuario_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este usuario?')">ELIMINAR</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB MÉDICOS --}}
                    <div class="tab-pane fade" id="tab-medicos">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Médicos</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Médico</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
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
                                            <td>{{ $medico->especialidad ?? 'Sin especialidad' }}</td>
                                            <td>
                                                <a href="#" class="btn btn-success btn-sm">VER</a>
                                                <a href="#" class="btn btn-primary btn-sm">EDITAR</a>
                                                <button class="btn btn-danger btn-sm">ELIMINAR</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB PACIENTES --}}
                    <div class="tab-pane fade" id="tab-pacientes">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Pacientes</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Paciente</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
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
                                            <td>
                                                <a href="#" class="btn btn-success btn-sm">VER</a>
                                                <a href="#" class="btn btn-primary btn-sm">EDITAR</a>
                                                <button class="btn btn-danger btn-sm">ELIMINAR</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB ADMINISTRADORES --}}
                    <div class="tab-pane fade" id="tab-admins">
                        <div class="table-area shadow p-3">
                            <h3 class="text-center mb-4">Administradores</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Admin</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
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
                                            <td>{{ $admin->cargo }}</td>
                                            <td>{{ $admin->area_id }}</td>
                                            <td>
                                                <a href="#" class="btn btn-success btn-sm">VER</a>
                                                <a href="#" class="btn btn-primary btn-sm">EDITAR</a>
                                                <button class="btn btn-danger btn-sm">ELIMINAR</button>
                                            </td>
                                        </tr>
                                        @endforeach
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

<!-- Modal para Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('crear.usuario') }}">
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
                                <input type="password" name="contraseña" class="form-control" required>
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
@endif

@include('include.footer')