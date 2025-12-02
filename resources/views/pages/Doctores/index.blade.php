@include('include.header')
@include('include.navbar')

<div class="content" style="padding: 20px;">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center">Gestión de Doctores</h1>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-md me-2"></i>Lista de Doctores
                            <small class="text-light ms-2">(Datos cifrados para seguridad)</small>
                        </h3>
                        <a href="{{ route('doctores.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i> Nuevo Doctor
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Correo (Cifrado)</th>
                                        <th>Teléfono (Cifrado)</th>
                                        <th>Contraseña (Hash)</th>
                                        <th>Especialidad</th>
                                        <th>Licencia</th>
                                        <th>Experiencia</th>
                                        <th>Consultas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doctores as $doctor)
                                    <tr>
                                        <td>{{ $doctor->medico_id }}</td>
                                        <td>{{ $doctor->nombre_completo }}</td>
                                        <td>
                                            @if(!empty($doctor->correo_cifrado) && $doctor->correo_cifrado != 'NULL')
                                                <code class="small" title="Correo cifrado AES-256">
                                                    {{ substr($doctor->correo_cifrado, 0, 30) }}...
                                                </code>
                                            @else
                                                <span class="badge bg-warning">Sin cifrar</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-lock"></i> AES-256
                                            </small>
                                        </td>
                                        <td>
                                            @if(!empty($doctor->telefono_cifrado) && $doctor->telefono_cifrado != 'NULL')
                                                <code class="small" title="Teléfono cifrado AES-256">
                                                    {{ substr($doctor->telefono_cifrado, 0, 30) }}...
                                                </code>
                                            @else
                                                <span class="badge bg-warning">Sin cifrar</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-lock"></i> AES-256
                                            </small>
                                        </td>
                                        <td>
                                            @if(!empty($doctor->contraseña))
                                                <code class="small" title="Hash SHA2-256">
                                                    {{ substr($doctor->contraseña, 0, 30) }}...
                                                </code>
                                            @else
                                                <code class="small text-muted">No disponible</code>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-key"></i> SHA2-256
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $doctor->especialidad }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $doctor->nro_licencia }}</code>
                                        </td>
                                        <td>{{ $doctor->años_experiencia }} años</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $doctor->total_consultas ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('doctores.show', $doctor->medico_id) }}" 
                                                   class="btn btn-info btn-sm" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('doctores.edit', $doctor->medico_id) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('doctores.destroy', $doctor->medico_id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            title="Eliminar" 
                                                            onclick="return confirm('¿Estás seguro de eliminar este doctor?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-user-md fa-2x mb-3"></i><br>
                                            No hay doctores registrados
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-shield-alt me-2"></i>Medidas de Seguridad Aplicadas:</h6>
                                <ul class="small mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Teléfonos cifrados con AES-256</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Correos cifrados con AES-256</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Contraseñas con hash SHA2-256</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Licencias únicas validadas</li>
                                </ul>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>Total Doctores: <span class="badge bg-primary">{{ count($doctores) }}</span></h6>
                                @if(count($doctores) > 0)
                                    <small class="text-muted">
                                    Total consultas: {{ array_sum(array_column($doctores, 'total_consultas')) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include.footer')