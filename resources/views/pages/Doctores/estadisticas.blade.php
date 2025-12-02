@include('include.header')
@include('include.navbar')

<main class="main">
    <div class="page-header d-flex align-items-center" style="background-image: url('{{ asset('assets/img/page-header.jpg') }}');">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1>Estadísticas de Doctores</h1>
                    <p>Distribución de médicos por especialidades</p>
                </div>
            </div>
        </div>
    </div>

    <section id="statistics" class="statistics">
        <div class="container">
            <div class="section-header">
                <h2>Estadísticas Generales</h2>
                <p>Resumen de doctores en el sistema hospitalario</p>
            </div>

            {{-- Mostrar mensajes --}}
            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ $error }}
                </div>
            @endif

            @if(isset($mensaje))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ $mensaje }}
                </div>
            @endif

            {{-- Mostrar estadísticas si hay datos --}}
            @if($totalDoctores > 0)
                {{-- Tarjetas de resumen --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary text-center">
                            <div class="card-body">
                                <h3>{{ $totalDoctores }}</h3>
                                <p>Total Doctores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success text-center">
                            <div class="card-body">
                                <h3>{{ $totalEspecialidades }}</h3>
                                <p>Especialidades</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info text-center">
                            <div class="card-body">
                                <h3>{{ number_format($totalDoctores / $totalEspecialidades, 1) }}</h3>
                                <p>Promedio por Especialidad</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de distribución --}}
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>
                            Distribución por Especialidad
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Especialidad</th>
                                        <th class="text-center">Cantidad de Doctores</th>
                                        <th class="text-center">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats as $especialidad => $cantidad)
                                    <tr>
                                        <td class="fw-bold">{{ $especialidad }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $cantidad }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">
                                                {{ number_format(($cantidad / $totalDoctores) * 100, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td><strong>TOTAL</strong></td>
                                        <td class="text-center"><strong>{{ $totalDoctores }}</strong></td>
                                        <td class="text-center"><strong>100%</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No hay datos de estadísticas disponibles. Asegúrate de que existan doctores registrados en el sistema.
                </div>
            @endif
        </div>
    </section>
</main>

@include('include.footer')