@include('include.header')
@include('include.navbar')
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 380px; border-radius: 15px;">

        <h3 class="text-center mb-3">Iniciar Sesión</h3>

        {{-- Mostrar mensajes de error/success --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Éxito:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- LOGIN FORM --}}
        <form method="POST" action="{{ route('ControlInicio.login') }}">
            @csrf

            {{-- SELECTOR DE ROL --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Selecciona tu rol</label>
                <select name="rol" class="form-select @error('rol') is-invalid @enderror" required>
                    <option value="">-- Elegir rol --</option>
                    <option value="medico" {{ old('rol') == 'medico' ? 'selected' : '' }}>Doctor / Médico</option>
                    <option value="paciente" {{ old('rol') == 'paciente' ? 'selected' : '' }}>Paciente</option>
                    <option value="administrativo" {{ old('rol') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                </select>
                @error('rol')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            {{-- USERNAME --}}
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                       value="{{ old('username') }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- SUBMIT --}}
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                    Ingresar
                </button>
            </div>

            {{-- Link para recuperar contraseña (opcional) --}}
            <div class="text-center mt-3">
                <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>

        </form>
    </div>
</div>

@include('include.footer')