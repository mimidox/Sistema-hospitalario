@include('include.header')
@include('include.navbar')
<main class="container mx-auto mt-8">

        {{-- TARJETAS SUPERIORES --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-gray-600">Doctores</h2>
                <p class="text-4xl font-bold text-blue-700">34</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-gray-600">Pacientes</h2>
                <p class="text-4xl font-bold text-green-700">214</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-gray-600">Citas Hoy</h2>
                <p class="text-4xl font-bold text-yellow-600">18</p>
            </div>

        </div>


        {{-- HOSPITALIZACIONES --}}
        <div class="mt-8">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition w-full md:w-1/2 mx-auto">
                <h2 class="text-gray-600">Hospitalizaciones activas</h2>
                <p class="text-4xl font-bold text-red-700">7</p>
            </div>
        </div>


        {{-- CALENDARIO / ACTIVIDADES --}}
        <div class="mt-10 bg-white p-6 rounded-xl shadow">

            <h2 class="text-xl font-bold text-gray-700 mb-4">Calendario / Actividades</h2>

            {{-- Calendario Placeholder --}}
            <div class="grid grid-cols-7 gap-2 text-center text-gray-700">
                <div class="p-2 bg-gray-200 rounded">Lun</div>
                <div class="p-2 bg-gray-200 rounded">Mar</div>
                <div class="p-2 bg-gray-200 rounded">Mié</div>
                <div class="p-2 bg-gray-200 rounded">Jue</div>
                <div class="p-2 bg-gray-200 rounded">Vie</div>
                <div class="p-2 bg-gray-200 rounded">Sáb</div>
                <div class="p-2 bg-gray-200 rounded">Dom</div>
            </div>

            <div class="mt-6">
                <h3 class="font-semibold text-gray-600">Últimos movimientos:</h3>

                <ul class="list-disc ml-6 mt-2 text-gray-500">
                    <li>Paciente registrado</li>
                    <li>Cita creada</li>
                    <li>Hospitalización activa</li>
                </ul>
            </div>
        </div>

</main>
@include('include.footer')