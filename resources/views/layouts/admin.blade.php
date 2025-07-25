<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles') {{-- Pour ajouter des styles spécifiques à une page --}}
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo et Titres dans la sidebar -->
        <div class="logo-sidebar-container text-center py-3">
            <img src="{{ asset('images/logo_tunis.png') }}"
                 alt="Drapeau Tunisien"
                 class="logo-sidebar mb-2">
            <h4 class="text-white mb-1">République Tunisienne</h4>
            <p class="text-white-50 small mb-0">Ministère de l'équipement et de l'habitat</p>
        </div>

        <ul class="sidebar-menu">
            <li class="{{ Request::routeIs('admin.home') ? 'active' : '' }}">
                <a href="{{ route('admin.home') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="{{ Request::routeIs('admin.users.management') ? 'active' : '' }}">
                <a href="{{ route('admin.users.management') }}"><i class="fas fa-users"></i> Utilisateurs</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-chart-bar"></i> Projets</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light">
        <div class="container-fluid">
            <button class="btn d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            {{-- Le contenu spécifique à chaque page sera injecté ici --}}
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
    @yield('scripts') {{-- Pour ajouter des scripts spécifiques à une page --}}
</body>
</html>
