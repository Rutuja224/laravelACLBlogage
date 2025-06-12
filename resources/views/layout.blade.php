<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Clean Blog</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
</head>
<body style="padding-top: 56px;">

<!-- NAVBAR -->
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="{{ url('/') }}">Blog</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            Menu <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">About</a></li>

                @auth
                    <li class="nav-item"><a class="nav-link text-white" href="{{ url('/posts') }}">Dashboard</a></li>

                    @php
                        $user = auth()->user();
                    @endphp

                    @if($user && $user->hasPermission('manage acl'))
                        <li class="nav-item"><a class="nav-link text-white" href="{{ url('/roles') }}">Roles</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ url('/permissions') }}">Permissions</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ url('/acl') }}">Assign Permissions</a></li>
                    @endif


                    {{-- @if(auth()->user()->hasRole('admin'))
                        <li class="nav-item"><a class="nav-link text-white" href="{{ url('/pending-posts') }}">Approve Posts</a></li>
                    @endif --}}

                    <li class="nav-item d-flex align-items-center">
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white" style="text-decoration: none;">
                                Logout
                            </button>
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
@yield('content')

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

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')

</body>
</html>
