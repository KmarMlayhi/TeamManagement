<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation mot de passe - Ministère Tunisien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth/passwords.css') }}">
</head>
<body>
    <!-- En-tête gouvernemental -->
    <header class="gov-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo_tunis.png') }}" alt="République Tunisienne" class="gov-logo">
                <div>
                    <div class="gov-title">République Tunisienne</div>
                    <div class="gov-subtitle">Ministère de l'équipement et de l'habitat</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h2><i class="fas fa-key me-2"></i>Réinitialisation du mot de passe</h2>
                <p class="mb-0">Portail administratif</p>
            </div>

            <div class="auth-body">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="password-instructions">
                    <p><i class="fas fa-info-circle text-primary me-2"></i>Veuillez saisir votre adresse email professionnelle pour recevoir le lien de réinitialisation.</p>
                </div>

                <form method="post" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email professionnel</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="prenom.nom@mehat.gov.tn" required>
                        </div>
                        @error('email')
                            <div class="text-danger mt-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Retour à la page de connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>