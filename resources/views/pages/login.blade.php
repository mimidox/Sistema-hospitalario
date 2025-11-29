@include('include.header')
@include('include.navbar')
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 380px; border-radius: 15px;">

        <h3 class="text-center mb-3">Iniciar Sesión</h3>

        {{-- LOGIN FORM --}}
        <form method="POST" action="{{ route('ControlInicio.login') }}">
            @csrf

            {{-- SELECTOR DE ROL --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Selecciona tu rol</label>
                <select name="rol" class="form-select" required>
                    <option value="">-- Elegir rol --</option>
                    <option value="medico">Doctor / Médico</option>
                    <option value="paciente">Paciente</option>
                    <option value="administrativo">Administrativo</option>
                </select>
            </div>

            <hr>

            {{-- USERNAME O CORREO SEGÚN TU BD --}}
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            {{-- SUBMIT --}}
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                    Ingresar
                </button>
            </div>

        </form>
    </div>
</div>

@include('include.footer')