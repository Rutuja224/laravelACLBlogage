<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Blog</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('head')
</head>
<body style="padding-top: 56px;">

<!-- NAVBAR -->
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="{{ route('home') }}">Blog</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            Menu <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">About</a></li>

                @auth
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('dashboard') }}">Dashboard</a></li>
                    @if(auth()->user()->hasPermission('manage acl'))
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('roles.index') }}">Roles</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('permissions.index') }}">Permissions</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('acl.index') }}">Assign Permissions</a></li>
                    @endif
                    <li class="nav-item d-flex align-items-center">
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white" style="text-decoration: none;">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('showLogin') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('showRegister') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container mt-5 pt-4">
    @yield('content')
</div>

<!-- FOOTER -->
<footer class="border-top mt-5 py-4 bg-light">
    <div class="container text-center">
        <ul class="list-inline mb-2">
            <li class="list-inline-item"><a href="#"><i class="fab fa-twitter fa-lg"></i></a></li>
            <li class="list-inline-item"><a href="#"><i class="fab fa-facebook-f fa-lg"></i></a></li>
            <li class="list-inline-item"><a href="#"><i class="fab fa-github fa-lg"></i></a></li>
        </ul>
        <p class="small text-muted fst-italic mb-0">&copy; Your Website 2025</p>
    </div>
</footer>

<!-- Loader  -->
<div id="loaderOverlay" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7);
    z-index: 9999;">
    <div style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);">
        
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>



<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')
</body>
</html>
