@include('include.header')
@include('include.navbar')

<div class="content" style="padding: 20px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-md me-2"></i>Detalle del Doctor
                            <small class="text-light">(Datos cifrados para seguridad)</small>
                        </h3>
                        <a href="{{ route('doctores.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user me-2"></i>Información Personal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th class="w-40">Nombre Completo:</th>
                                                <td>{{ $doctor->nombre_completo }}</td>
                                            </tr>
                                            <tr>
                                                <th>Correo (Cifrado):</th>
                                                <td>
                                                    @if(!empty($doctor->correo_cifrado) && $doctor->correo_cifrado != 'NULL')
                                                        <code class="text-danger" title="Correo cifrado AES-256">
                                                            {{ substr($doctor->correo_cifrado, 0, 50) }}...
                                                        </code>
                                                    @else
                                                        <span class="text-warning">No cifrado</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-lock"></i> AES-256
                                                    </small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Teléfono (Cifrado):</th>
                                                <td>
                                                    @if(!empty($doctor->telefono_cifrado) && $doctor->telefono_cifrado != 'NULL')
                                                        <code class="text-danger" title="Teléfono cifrado AES-256">
                                                            {{ substr($doctor->telefono_cifrado, 0, 50) }}...
                                                        </code>
                                                    @else
                                                        <span class="text-warning">No cifrado</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-lock"></i> AES-256
                                                    </small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Contraseña (Hash):</th>
                                                <td>
                                                    <code>{{ substr($doctor->contraseña ?? '', 0, 30) }}...</code>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-key"></i> Hash SHA2-256 (irreversible)
                                                    </small>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-stethoscope me-2"></i>Información Profesional
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th class="w-40">Especialidad:</th>
                                                <td>
                                                    <span class="badge bg-primary">{{ $doctor->especialidad }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Licencia:</th>
                                                <td>
                                                    <code>{{ $doctor->nro_licencia }}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Experiencia:</th>
                                                <td>
                                                    <span class="badge bg-success">{{ $doctor->años_experiencia }} años</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Consultas:</th>
                                                <td>
                                                    <span class="badge bg-info">{{ $doctor->total_consultas ?? 0 }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h5><i class="fas fa-shield-alt me-2"></i>Seguridad de Datos</h5>
                            <p class="mb-2">Los datos sensibles están protegidos con:</p>
                            <ul class="mb-0">
                                <li><strong>Correos:</strong> Cifrado AES-256 con clave secreta</li>
                                <li><strong>Teléfonos:</strong> Cifrado AES-256 con clave secreta</li>
                                <li><strong>Contraseñas:</strong> Hash SHA2-256 con salt (irreversible)</li>
                                <li><strong>Licencias:</strong> Validación de unicidad en base de datos</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('doctores.edit', $doctor->medico_id) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i> Editar Doctor
                            </a>
                            <a href="{{ route('doctores.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list me-1"></i> Ver Todos los Doctores
                            </a>
                            
                            @if(env('APP_DEBUG'))
                            <button type="button" class="btn btn-danger ms-2" id="btnVerReales" onclick="verDatosReales()">
                                <i class="fas fa-eye me-1"></i> Ver Datos Reales (DEBUG)
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(env('APP_DEBUG'))
<script>
function verDatosReales() {
    fetch('/doctores/{{ $doctor->medico_id }}/datos-reales')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            alert('📧 DATOS REALES (Solo desarrollo):\n\n' +
                  'Correo: ' + (data.correo_real || 'No disponible') + '\n' +
                  'Teléfono: ' + (data.telefono_real || 'No disponible') + '\n\n' +
                  '⚠️ ESTOS DATOS SON CONFIDENCIALES ⚠️');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener datos reales');
        });
}
</script>
@endif

@include('include.footer')