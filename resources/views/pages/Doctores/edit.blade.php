@include('include.header')
@include('include.navbar')

<div class="content" style="padding: 20px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Doctor
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('doctores.update', $doctor->medico_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-user me-2"></i>Información Personal
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nombre *</label>
                                        <input type="text" name="nombre" class="form-control" 
                                               value="{{ old('nombre', $doctor->nombre) }}" required>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Apellido Paterno *</label>
                                        <input type="text" name="paterno" class="form-control" 
                                               value="{{ old('paterno', $doctor->paterno) }}" required>
                                        @error('paterno')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Apellido Materno</label>
                                        <input type="text" name="materno" class="form-control" 
                                               value="{{ old('materno', $doctor->materno) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-stethoscope me-2"></i>Información Profesional
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Correo Electrónico *</label>
                                        <input type="email" name="correo" class="form-control" 
                                               value="{{ old('correo', $doctor->correo) }}" required>
                                        @error('correo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" 
                                               value="{{ old('telefono', $doctor->telefono) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Especialidad *</label>
                                        <select name="especialidad" class="form-control" required>
                                            <option value="">Seleccionar Especialidad</option>
                                            @foreach($especialidades as $esp)
                                            <option value="{{ $esp->especialidad }}" 
                                                {{ (old('especialidad', $doctor->especialidad) == $esp->especialidad) ? 'selected' : '' }}>
                                                {{ $esp->especialidad }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('especialidad')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Número de Licencia *</label>
                                        <input type="text" name="nro_licencia" class="form-control" 
                                               value="{{ old('nro_licencia', $doctor->nro_licencia) }}" required>
                                        @error('nro_licencia')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Años de Experiencia *</label>
                                        <input type="number" name="años_experiencia" class="form-control" 
                                               value="{{ old('años_experiencia', $doctor->años_experiencia) }}" min="0" required>
                                        @error('años_experiencia')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg me-3">
                                        <i class="fas fa-save me-2"></i>Actualizar Doctor
                                    </button>
                                    <a href="{{ route('doctores.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include.footer')