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
                    <div class="profile-circle mb-3 mx-auto" style="width: 80px; height: 80px; border-radius: 50%;">
                        <img src="assets/img/usuario_login_icon.jpg" alt="" style="width: 80px; height: 80px; border-radius: 50%;">
                    </div>
                    <h4 class="mb-1">{{ session('username') ?? 'Usuario' }}</h4>
                    <p class="text-muted mb-1">ID: {{ session('usuario_id') }}</p>
                    <p class="text-success fw-bold">{{ ucfirst(session('rol')) }}</p>
                    <p class="text-muted mb-1">Nombre: {{ session('nombre') }}</p>
                    <p class="text-muted mb-1">Apellido: {{ session('paterno') }} {{ session('materno') }}</p>
                    <p class="text-muted mb-1">Correo: {{ session('correo') }}</p>
                    <p class="text-muted mb-1">Teléfono: {{ session('telefono') }}</p>

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
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $usuario->usuario_id }}">VER</button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $usuario->usuario_id }}">EDITAR</button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $usuario->usuario_id }}" data-username="{{ $usuario->username }}">ELIMINAR</button>
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
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $medico->usuario_id }}">VER</button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $medico->usuario_id }}">EDITAR</button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $medico->usuario_id }}" data-username="{{ $medico->username }}">ELIMINAR</button>
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
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $paciente->usuario_id }}">VER</button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $paciente->usuario_id }}">EDITAR</button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $paciente->usuario_id }}" data-username="{{ $paciente->username }}">ELIMINAR</button>
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

                    {{-- TAB ADMINISTRATIVOS --}}
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
                                                    @case(5) Laboratorio @break
                                                    @default Sin área
                                                @endswitch
                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-ver" data-id="{{ $admin->usuario_id }}">VER</button>
                                                <button class="btn btn-primary btn-sm btn-editar" data-id="{{ $admin->usuario_id }}">EDITAR</button>
                                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $admin->usuario_id }}" data-username="{{ $admin->username }}">ELIMINAR</button>
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

                </div>{{-- FIN TABS --}}
            </div> {{-- FIN COL-9 --}}
        </div> {{-- FIN ROW --}}
    </div> {{-- FIN CONTAINER-FLUID --}}
</main>
@endif
