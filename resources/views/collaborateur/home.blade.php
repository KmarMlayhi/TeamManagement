<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Collaborateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashAdmin.css') }}">
    <style>
        /* Corrections et améliorations supplémentaires */
        .stats-row {
            margin-top: 2rem;
        }
        
        .clickable-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            height: 100%;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            color: inherit;
            text-decoration: none;
        }
        
        .card-icon-container {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            background: rgba(0, 86, 179, 0.1);
        }
        
        .card-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1.2;
        }
        
        .card-text-content h6 {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .project-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .progress {
            height: 8px;
            width: 100px;
            margin: 0 1rem;
        }
        
        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1.5rem 0;
        }
        
        .quick-action-btn {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-color);
        }
        
        .quick-action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .quick-action-btn i {
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .project-status {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .progress {
                width: 100%;
                margin: 0.5rem 0;
            }
            
            .list-group-item {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .quick-action-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @extends('layouts.collaborateur')
    
    @section('content')
    <div class="dashboard-container">
        <!-- Section de bienvenue -->
        <div class="welcome-card">
            <div class="welcome-header">
                <h1>Espace Collaborateur</h1>
                <p>Accédez à vos équipes et à vos projets efficacement pour suivre leurs progrès.</p>
            </div>
            <div class="mt-3">
                <span class="user-badge">
                    <i class="fas fa-user"></i>
                    Bienvenue {{ Auth::user()->name }} !
                </span>
            </div>
        </div>

        <!-- Section des statistiques -->
        <div class="stats-grid">
            <!-- Mes projets -->
            <a href="{{ route('collaborateur.equipes.index') }}" class="stat-card clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-folder-open card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Mes Projets</h6>
                    <div class="stat-value">{{ $mesProjets->count() }}</div>
                    <p class="stat-description">Projets assignés</p>
                </div>
            </a>
            
            <!-- Tâches en cours -->
            <a href="{{ route('collaborateur.suivi') }}" class="stat-card clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-tasks card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Tâches en cours</h6>
                    <div class="stat-value">{{ $tachesEnCours ?? '0' }}</div>
                    <p class="stat-description">En cours de réalisation</p>
                </div>
            </a>
            
            <!-- Tâches terminées -->
            <a href="{{ route('collaborateur.suivi') }}?filter=completed" class="stat-card clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-check-circle card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Tâches terminées</h6>
                    <div class="stat-value">{{ $tachesTerminees ?? '0' }}</div>
                    <p class="stat-description">Tâches finalisées</p>
                </div>
            </a>
            
            <!-- Tâches en retard -->
            <a href="{{ route('collaborateur.suivi') }}?filter=overdue" class="stat-card clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-exclamation-triangle card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Tâches en retard</h6>
                    <div class="stat-value">{{ $tachesEnRetard ?? '0' }}</div>
                    <p class="stat-description">Tâches échues</p>
                </div>
            </a>
        </div>

        <!-- Projets récents -->
        <div class="card mt-4 recent-activity">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-folder me-2"></i> 
                    <span>Mes projets récents</span>
                </div>
                <a href="{{ route('collaborateur.equipes.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tous <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @forelse($projetsRecents as $projet)
                        <a href="{{ route('collaborateur.equipes.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div class="fw-bold">{{ $projet->nom }}</div>
                            <div class="project-status">
                                <span class="badge bg-{{ $projet->statut_color }} rounded-pill">
                                    {{ $projet->statut_text }}
                                </span>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $projet->statut_color }}" 
                                         role="progressbar" 
                                         style="width: {{ $projet->avancement }}%" 
                                         aria-valuenow="{{ $projet->avancement }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="text-muted small">{{ $projet->avancement }}%</span>
                            </div>
                        </a>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Aucun projet récent</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
            <!-- Notifications Réunions -->
            <div class="card mt-4 notifications">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-bell me-2"></i>
                        <span>Réunions à venir</span>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($notifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-video text-primary me-2"></i>
                                    {{ $notification->data['message'] }}
                                </div>
                                <a href="{{ route('collaborateur.notification.read', $notification->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    Rejoindre
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>Aucune réunion prévue</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
    @endsection
    
    
</body>
</html>