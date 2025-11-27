@include('include.header')
@include('include.navbar')

<div class="content" style=" padding: 20px;">
    <h1 class="text-center">Proyecto laravel</h1>
    <h2 class="text-center">Grupo 6, Sistema Hospitalario</h2>


    <div class="d-flex justify-content-center">
        <div class="card mb-3 bg-dark text-white" style="max-width: 540px;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="{{ asset('assets/img/maleta.jpg') }}" class="img-fluid rounded-start" alt="Card image" style="height: 100%; object-fit: cover;">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">OBJETIVO</h5>
                        <p class="card-text">Al seleccionar una zona, el segundo combo mostrará solo los barrios de esa zona, y la calle se llenará según la zona y barrio elegidos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-center">Enlaces a zonas, barrio y calles:</h2>
    <br>
    <div class="container mt-4 bg-secondary text-white">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <img class="card-img-top" src="{{asset('assets/img/zona.jpg')}}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Zonas</h5>
                        <p class="card-text">zona (codigo_zona, nombre_zona)</p>
                        <a href="#" class="btn btn-dark">IR</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <img class="card-img-top" src="{{asset('assets/img/barrio.jpg')}}" alt="Card image cap">
                    <div class="card-body ">
                        <h5 class="card-title">Barrios</h5>
                        <p class="card-text">barrio (codigo_barrio, nombre_barrio, codigo_zona)</p>
                        <a href="#" class="btn btn-dark">IR</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <img class="card-img-top" src="{{asset('assets/img/calle2.jpg')}}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Calles</h5>
                        <p class="card-text">calle (codigo_calle, nombre_calle, codigo_zona, codigo_barrio)</p>
                        <a href="#" class="btn btn-dark">IR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <br><br>
    <div class="bg-dark text-white py-3 " >
        <div class="container" style="max-width: 700px;">
            <h2 class="mb-4">Teleferico</h2>
            <!-- Tarjeta con imagen arriba -->
            <div class="card mb-3">
                <img class="card-img-top" src="{{ asset('assets/img/telefe.jpg') }}"  alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                </div>
            </div>
        </div>
    </div>

</div>


@include('include.footer')