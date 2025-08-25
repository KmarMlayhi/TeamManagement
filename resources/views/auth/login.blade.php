<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Ministère de l'équipement et de l'habitat</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>

<div class="auth-container">
    <!-- Sidebar avec logos -->
    <div class="sidebar">
        <div class="logo-sidebar-container text-center py-3">
            <img src="{{ asset('images/logo_tunis.png') }}"
                 alt="Drapeau Tunisien"
                 class="logo-sidebar mb-3">
            <h4 class="text-white mb-2">République Tunisienne</h4>
            <p class="text-white-50 small mb-1">Ministère de l'équipement et de l'habitat</p>
            <img src="{{ asset('images/logo_mehat.png') }}" 
                 alt="Logo du Ministère" 
                 class="ministere-logo">
        </div>
    </div>
    <!-- Contenu du formulaire -->
    <div class="auth-content">
        <div class="auth-header text-center">
            <h1 class="platform-title">Team Management Platform</h1>
            <h3 class="mb-2"><i class="fas fa-lock me-2"></i>Accès sécurisé</h3>
            <p class="mb-0">Portail administratif</p>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">Identifiant</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="prenom.nom@mehat.gov.tn" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Votre mot de passe" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-auth btn-lg text-white">
                    <i class="fas fa-sign-in-alt me-2"></i>Connexion
                </button>
            </div>

            <div class="text-center mt-3">
                <p class="mb-0">Première visite ? <a href="{{ url('/register') }}" class="text-decoration-none">Créer un compte</a></p>
            </div>
        </form>

        <div class="credits">
            <p class="mb-0">Réalisé par Kmar Mlayhi</p>
            <p class="mb-0">Encadré par Mr. Hyathem Saad</p>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>