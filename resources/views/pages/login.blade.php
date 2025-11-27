@include('include.header')
@include('include.navbar')
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 380px; border-radius: 15px;">

        <h3 class="text-center mb-3">Iniciar Sesión</h3>

        {{-- SELECTOR DE ROL ARRIBA --}}
        <form method="POST" action="">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Selecciona tu rol</label>
                <select name="rol" class="form-select" required>
                    <option value="">-- Elegir rol --</option>
                    <option value="doctor">Doctor</option>
                    <option value="paciente">Paciente</option>
                    <option value="administrativo">Administrativo</option>
                </select>
            </div>

            <hr>

            {{-- FORMULARIO --}}
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            {{-- BOTÓN ABAJO --}}
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                    Ingresar
                </button>
            </div>

        </form>
    </div>
</div>

@include('include.footer')