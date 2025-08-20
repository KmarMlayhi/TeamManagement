<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Espace Collaborateur')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    @yield('styles')
    
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
         <!-- Logo et Titres dans la sidebar -->
        <div class="logo-sidebar-container text-center py-3">
            <a href="{{ route('collaborateur.home') }}">
            <img src="{{ asset('images/logo_tunis.png') }}"
                 alt="Drapeau Tunisien"
                 class="logo-sidebar mb-2">
            </a>
            <h4 class="text-white mb-1">République Tunisienne</h4>
            <p class="text-white-50 small mb-0">Ministère de l'équipement et de l'habitat</p>
        </div>

        <ul class="sidebar-menu">

            <!-- Dashboard -->
            <li class="{{ Request::routeIs('collaborateur.home') ? 'active' : '' }}">
                <a href="{{ route('collaborateur.home') }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            </li>

            <li class="{{ Request::is('collaborateur/equipes*') ? 'active' : '' }}">
                <a href="{{ route('collaborateur.equipes.index') }}">
                    <i class="fas fa-users"></i> Mes équipes
                </a>
            </li>
           <li>
                <a href="{{ route('collaborateur.suivi') }}">
                    <i class="fas fa-project-diagram"></i> Suivi
                </a>
            </li>
            <li>
                <a href="{{ route('projet_messages.projet_list') }}">
                    <i class="fas fa-comment-alt"></i> Discussions projets
                </a>
            </li>


        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light">
       <div class="container-fluid"> 
            <button id="sidebarToggle" class="btn d-md-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        <span class="badge bg-primary ms-2">Collaborateur</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profil.show') }}">
                                <i class="fas fa-user me-2"></i> Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
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
        @include('partials.flash-messages')
        @yield('content')
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar on small devices
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Fermer le dropdown user quand on clique à l'extérieur (optionnel)
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            if (!userDropdown) return;

            const dropdownMenu = userDropdown.nextElementSibling;
            if (!userDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
