<header id="header" class="header sticky-top">

    <div class="topbar d-flex align-items-center">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">contact@example.com</a></i>
                <i class="bi bi-phone d-flex align-items-center ms-4"><span>+1 5589 55488 55</span></i>
            </div>
            <div class="social-links d-none d-md-flex align-items-center">
                <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('ControlInicio.index') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('assets/img/LogoLab.png') }}" alt="">
                <h1 class="sitename">Medilab</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('ControlInicio.index') }}" class="active">Home<br></a></li>
                    <li><a href="{{ route('ControlInicio.main') }}">Main</a></li>
                    <li><a href="{{ route('ControlInicio.login') }}">Loggin</a></li>
                    <li><a href="#departments">Departments</a></li>
                    <li><a href="#doctors">Doctors</a></li>
                    
                    <!-- 🆕 DROPDOWN DE DOCTORES -->
                    <li class="dropdown">
                        <a href="#"><span>Gestión Doctores</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="{{ route('doctores.index') }}">Lista de Doctores</a></li>
                            <li><a href="{{ route('doctores.create') }}">Registrar Doctor</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="{{ route('doctores.estadisticas') }}">Estadísticas</a></li>
                            <li><a href="{{ route('doctores.reporte.especialidades') }}">Reporte Especialidades</a></li>
                        </ul>
                    </li>
                    <!-- FIN DEL NUEVO DROPDOWN -->
                    
                    <li><a href="#">Administradores</a></li>
                    <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Dropdown 1</a></li>
                            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                                <ul>
                                    <li><a href="#">Deep Dropdown 1</a></li>
                                    <li><a href="#">Deep Dropdown 2</a></li>
                                    <li><a href="#">Deep Dropdown 3</a></li>
                                    <li><a href="#">Deep Dropdown 4</a></li>
                                    <li><a href="#">Deep Dropdown 5</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Dropdown 2</a></li>
                            <li><a href="#">Dropdown 3</a></li>
                            <li><a href="#">Dropdown 4</a></li>
                        </ul>
                    </li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="cta-btn d-none d-sm-block" href="#appointment">Make an Appointment</a>
        </div>
    </div>
</header>