@extends('layouts.chef') 

@section('content')
<div class="container py-4">
    <!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1" style="color: var(--secondary-color);">
                <i class="fas fa-user-cog me-2"></i>Mon Profil 
            </h2>
    
        </div>
        <div class="badge bg-secondary rounded-pill p-2">
            <i class="fas fa-shield-alt me-1"></i> Chef d'équipe
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Informations du profil</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf

                <!-- Colonne gauche - Avatar -->
                <div class="col-md-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="position-relative mb-3">
                            <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0056b3&color=fff' }}" 
                                 alt="Avatar" class="rounded-circle shadow-sm" width="150" height="150" id="avatarPreview">
                            <label for="avatarInput" class="btn btn-primary rounded-circle position-absolute" style="bottom: 10px; right: 10px; width: 40px; height: 40px; padding: 0;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                        </div>
                        <div class="text-center">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            <small class="text-muted">Membre depuis {{ $user->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite - Formulaire -->
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Informations personnelles</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required-field">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="name" id="name" class="form-control" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required-field">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" name="password" id="password" class="form-control" 
                                       placeholder="Saisissez un nouveau mot de passe">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmation</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" placeholder="Confirmez le nouveau mot de passe">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.home') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Section informations supplémentaires -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de compte</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Statut du compte:</span>
                        <span class="badge bg-success">Actif</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Email vérifié:</span>
                        <span class="badge bg-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                            {{ $user->email_verified_at ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Dernière connexion:</span>
                        <span class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Sécurité du compte</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Pour la sécurité de votre compte, utilisez un mot de passe complexe et ne le partagez jamais.</small>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Authentification à deux facteurs: <span class="text-muted">Non activée</span></small>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Session active: <span class="text-muted">En cours</span></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required-field::after {
    content: " *";
    color: var(--accent-color);
}

.toggle-password {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

#avatarPreview {
    transition: all 0.3s ease;
    object-fit: cover;
}

#avatarPreview:hover {
    opacity: 0.9;
    transform: scale(1.03);
}

.input-group-text {
    background-color: var(--light-gray);
    border-right: none;
}

.form-control:focus + .input-group-text {
    border-color: var(--primary-color);
}

.list-group-item {
    border: none;
    padding: 0.5rem 0;
    background: transparent;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aperçu de l'avatar avant upload
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Basculer la visibilité du mot de passe
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
@endsection