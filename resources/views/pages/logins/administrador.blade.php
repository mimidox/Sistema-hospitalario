@include('include.header')
@include('include.navbar')
<main class="container mx-auto mt-8">


<div class="container-fluid mt-4">
    <div class="row">

        {{-- PANEL IZQUIERDO --}}
        <div class="col-3">
            <div class="sidebar text-center shadow">

                <div class="profile-circle mb-3"></div>

                <h4 class="mb-1">{{ session('username') ?? 'Usuario Nombre' }}</h4>
                <p class="text-muted">Datos aquí</p>

            </div>
        </div>

        {{-- PANEL PRINCIPAL --}}
        <div class="col-9">

            {{-- BOTONES SUPERIORES --}}
            <div class="top-buttons mb-3 d-flex gap-2 justify-content-center">

                <a href="#" class="btn btn-success">CREAR</a>

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
                    <div class="table-area shadow">

                        <h3 class="text-center mb-4">Usuarios</h3>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Nombre</th>
                                    <th>Paterno</th>
                                    <th>Materno</th>
                                    <th>Correo</th>
                                    <th>Operaciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- Ejemplo estático. Reemplaza con tu foreach --}}
                                <tr>
                                    <td>1</td>
                                    <td>admin01</td>
                                    <td>Juan</td>
                                    <td>Pérez</td>
                                    <td>López</td>
                                    <td>correo@ejemplo.com</td>
                                    <td>
                                        <a href="#" class="btn btn-success btn-sm">VER</a>
                                        <a href="#" class="btn btn-primary btn-sm">EDITAR</a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>

                {{-- TAB MÉDICOS --}}
                <div class="tab-pane fade" id="tab-medicos">
                    <div class="table-area shadow">
                        <h3 class="text-center mb-4">Médicos</h3>
                        <p class="text-center text-muted">Aquí aparecerá la lista de médicos.</p>
                    </div>
                </div>

                {{-- TAB PACIENTES --}}
                <div class="tab-pane fade" id="tab-pacientes">
                    <div class="table-area shadow">
                        <h3 class="text-center mb-4">Pacientes</h3>
                        <p class="text-center text-muted">Aquí aparecerá la lista de pacientes.</p>
                    </div>
                </div>

                {{-- TAB ADMINISTRADORES --}}
                <div class="tab-pane fade" id="tab-admins">
                    <div class="table-area shadow">
                        <h3 class="text-center mb-4">Administradores</h3>
                        <p class="text-center text-muted">Aquí aparecerá la lista de administradores.</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


</main>
@include('include.footer')