@extends('layouts.chef')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Chef d'Équipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashAdmin.css') }}">
</head>
<body>
    @section('content')
    <div class="dashboard-container">
        <!-- Section de bienvenue -->
        <div class="welcome-card">
            <div class="welcome-header">
                <h1>Espace Chef d'Équipe</h1>
                <p>Gérez votre équipe efficacement et suivez les progrès des projets</p>
            </div>
            <div class="mt-3">
                <span class="user-badge">
                    <i class="fas fa-user"></i>
                    Bienvenue {{ Auth::user()->name }} !
                </span>
            </div>
        </div>
        
        
       <!-- Section des fonctionnalités -->
        <div class="features-section">
            <h3 class="section-title">Fonctionnalités disponibles</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon planning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Gestion des projets</h3>
                    <p>Planifiez les projets de vos équipes, attribuez des tâches et suivez les échéances.</p>
                    <a href="{{ route('chef_equipe.projets.index') }}" class="btn btn-outline-primary">Accéder <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon tracking">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Suivi des équipes</h3>
                    <p>Créer les équipes et assurer le bon déroulement de vos projets.</p>
                    <a href="{{ route('chef_equipe.equipes.index') }}" class="btn btn-outline-primary">Accéder <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Section des statistiques -->
        <div class="stats-section">
            <h3 class="section-title">Aperçu de votre activité</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Équipes créées</h3>
                    <div class="stat-number">{{ $stats['equipes_count'] }}</div>
                    <p class="stat-description">Vos équipes actives</p>
                </div>
                
                <div class="stat-card">
                    <h3>Membres total</h3>
                    <div class="stat-number">{{ $stats['membres_count'] }}</div>
                    <p class="stat-description">Collaborateurs dans vos équipes</p>
                </div>
                
                <div class="stat-card">
                    <h3>Tâches en cours</h3>
                    <div class="stat-number">{{ $stats['taches_en_cours'] }}</div>
                    <p class="stat-description">Tâches actives dans vos projets</p>
                </div>
                
                <div class="stat-card">
                    <h3>Projets actifs</h3>
                    <div class="stat-number">{{ $stats['projets_actifs'] }}</div>
                    <p class="stat-description">Projets en cours de réalisation</p>
                </div>
            </div>
        </div>
        
 
       
    </div>
    @endsection

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        // Animation pour les cartes de statistiques
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'none';
                card.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.03)';
            });
        });
        
        // Animation pour la carte de bienvenue
        const welcomeCard = document.querySelector('.welcome-card');
        setTimeout(() => {
            welcomeCard.style.transform = 'translateY(0)';
            welcomeCard.style.opacity = '1';
        }, 100);
        
        welcomeCard.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
        welcomeCard.style.transform = 'translateY(20px)';
        welcomeCard.style.opacity = '0';
    </script>
</body>
</html>