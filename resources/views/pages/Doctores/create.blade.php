@include('include.header')
@include('include.navbar')

<main class="main">
    <div class="page-header d-flex align-items-center" style="background-image: url('{{ asset('assets/img/page-header.jpg') }}');">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1>Registrar Nuevo Doctor</h1>
                    <p>Complete la información del médico para agregarlo al sistema</p>
                </div>
            </div>
        </div>
    </div>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-10">
                    <div class="info-wrap">
                        <div class="section-header">
                            <h2>Información del Doctor</h2>
                            <p>Complete todos los campos requeridos</p>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="bi bi-exclamation-triangle me-2"></i>Errores de validación
                            </h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('doctores.store') }}" method="POST" class="php-email-form" id="formDoctor">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Username *</label>
                                    <input type="text" name="username" class="form-control" 
                                           value="{{ old('username') }}" placeholder="usuario123" required>
                                    @error('username')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <label class="form-label fw-bold">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="{{ old('nombre') }}" placeholder="Juan" required>
                                    @error('nombre')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Apellido Paterno *</label>
                                    <input type="text" name="paterno" class="form-control" 
                                           value="{{ old('paterno') }}" placeholder="Pérez" required>
                                    @error('paterno')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <label class="form-label fw-bold">Apellido Materno</label>
                                    <input type="text" name="materno" class="form-control" 
                                           value="{{ old('materno') }}" placeholder="López">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Género</label>
                                    <select name="genero" class="form-control">
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option value="O">Otro</option>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <label class="form-label fw-bold">Correo Electrónico *</label>
                                    <input type="email" name="correo" class="form-control" 
                                           value="{{ old('correo') }}" placeholder="doctor@medilab.com" required>
                                    @error('correo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Contraseña *</label>
                                    <input type="password" name="contraseña" class="form-control" 
                                           placeholder="Mínimo 6 caracteres" required>
                                    @error('contraseña')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" 
                                           value="{{ old('telefono') }}" placeholder="+591 12345678">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Especialidad *</label>
                                    <select name="especialidad" class="form-control" required>
                                        <option value="">Seleccionar Especialidad</option>
                                        @foreach($especialidades as $esp)
                                        <option value="{{ $esp->especialidad }}" 
                                            {{ old('especialidad') == $esp->especialidad ? 'selected' : '' }}>
                                            {{ $esp->especialidad }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('especialidad')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <label class="form-label fw-bold">Número de Licencia *</label>
                                    <input type="text" name="nro_licencia" class="form-control" 
                                           value="{{ old('nro_licencia') }}" placeholder="LIC123456" required
                                           id="nro_licencia_input">
                                    @error('nro_licencia')
                                        <div class="text-danger small mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    
                                    <div id="licencia_error" class="text-danger small mt-1 d-none">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        <span id="licencia_error_text"></span>
                                    </div>
                                    <div id="licencia_success" class="text-success small mt-1 d-none">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <span id="licencia_success_text">Licencia disponible</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label fw-bold">Años de Experiencia *</label>
                                    <input type="number" name="años_experiencia" class="form-control" 
                                           value="{{ old('años_experiencia', 0) }}" min="0" required>
                                    @error('años_experiencia')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>Registrar Doctor
                                </button>
                                <a href="{{ route('doctores.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@include('include.footer')

{{-- SCRIPT PARA VALIDACIÓN DE LICENCIA --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const licenciaInput = document.getElementById('nro_licencia_input');
    const errorDiv = document.getElementById('licencia_error');
    const errorText = document.getElementById('licencia_error_text');
    const successDiv = document.getElementById('licencia_success');
    const successText = document.getElementById('licencia_success_text');
    const submitBtn = document.getElementById('submitBtn');
    
    // Verificar licencia cuando el usuario cambia el campo
    licenciaInput.addEventListener('blur', function() {
        const licencia = this.value.trim();
        
        if (licencia.length < 3) {
            hideMessages();
            return;
        }
        
        verificarLicencia(licencia);
    });
    
    function verificarLicencia(licencia) {
        // Mostrar loading
        licenciaInput.classList.add('loading');
        
        // Hacer petición AJAX
        fetch('{{ route("doctores.validarLicencia") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nro_licencia: licencia
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta');
            }
            return response.json();
        })
        .then(data => {
            // Quitar loading
            licenciaInput.classList.remove('loading');
            
            if (data.error) {
                // Mostrar error
                errorText.textContent = data.message;
                errorDiv.classList.remove('d-none');
                successDiv.classList.add('d-none');
                licenciaInput.classList.add('is-invalid');
                licenciaInput.classList.remove('is-valid');
            } else {
                // Mostrar éxito
                successText.textContent = data.message;
                errorDiv.classList.add('d-none');
                successDiv.classList.remove('d-none');
                licenciaInput.classList.remove('is-invalid');
                licenciaInput.classList.add('is-valid');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            licenciaInput.classList.remove('loading');
            hideMessages();
        });
    }
    
    function hideMessages() {
        errorDiv.classList.add('d-none');
        successDiv.classList.add('d-none');
        licenciaInput.classList.remove('is-invalid');
        licenciaInput.classList.remove('is-valid');
        licenciaInput.classList.remove('loading');
    }
    
    // Resetear cuando el usuario empieza a escribir
    licenciaInput.addEventListener('input', function() {
        hideMessages();
    });
});

// Estilos para la validación
const style = document.createElement('style');
style.textContent = `
    .loading {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="%23007bff" stroke-width="8" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px 20px;
        padding-right: 40px !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff8f8;
    }
    .is-valid {
        border-color: #28a745 !important;
        background-color: #f8fff9;
    }
    #licencia_error, #licencia_success {
        font-size: 0.875em;
        margin-top: 5px;
        padding: 5px 10px;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    #licencia_error {
        background-color: #f8d7da;
        border-left: 3px solid #dc3545;
        color: #721c24;
    }
    #licencia_success {
        background-color: #d4edda;
        border-left: 3px solid #28a745;
        color: #155724;
    }
`;
document.head.appendChild(style);
</script>