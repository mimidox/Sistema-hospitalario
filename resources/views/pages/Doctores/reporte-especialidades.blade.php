@include('include.header')
@include('include.navbar')

<main class="main">
    <!-- ======= Page Header ======= -->
    <div class="page-header d-flex align-items-center" style="background-image: url('{{ asset('assets/img/page-header.jpg') }}');">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1>Reporte de Especialidades</h1>
                    <p>Estadísticas y distribución de doctores por especialidad médica</p>
                </div>
            </div>
        </div>
    </div><!-- End Page Header -->

    <!-- ======= Report Section ======= -->
    <section id="reports" class="reports">
        <div class="container">

            <div class="section-header">
                <h2>Reporte por Especialidad</h2>
                <p>Distribución de médicos y experiencia por área médica</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Especialidad</th>
                                    <th class="text-center">Total Doctores</th>
                                    <th class="text-center">Experiencia Promedio</th>
                                    <th class="text-center">Exp. Mínima</th>
                                    <th class="text-center">Exp. Máxima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reporte as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->especialidad }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">{{ $item->total_doctores }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($item->experiencia_promedio, 1) }} años</td>
                                    <td class="text-center">{{ $item->exp_minima }} años</td>
                                    <td class="text-center">{{ $item->exp_maxima }} años</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section><!-- End Report Section -->

</main>

@include('include.footer')