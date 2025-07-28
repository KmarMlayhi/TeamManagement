<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Ministère de l'équipement et de l'habitat</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
            <h2 class="mb-2"><i class="fas fa-user-shield me-2"></i>Création de compte utilisateur</h2>
            <p class="mb-0">Accès au portail administratif</p>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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

        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nom complet</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nom et prénom" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" placeholder="prenom.nom@mehat.gov.tn" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="poste" class="form-label">Poste</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        <input type="text" name="poste" id="poste" class="form-control" placeholder="Votre poste administratif">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Rôle</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">Sélectionner un rôle</option>
                            <option value="collaborateur">Collaborateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe sécurisé" required>
                        <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="form-text">Minimum 8 caractères avec chiffres et lettres</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        <span class="input-group-text password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-auth btn-lg text-white">
                    <i class="fas fa-user-plus me-2"></i>Créer le compte
                </button>
            </div>

            <div class="text-center mt-3">
                <p class="mb-0">Déjà un compte? <a href="{{ url('/login') }}" class="text-decoration-none">Se connecter</a></p>
            </div>
        </form>
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

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>