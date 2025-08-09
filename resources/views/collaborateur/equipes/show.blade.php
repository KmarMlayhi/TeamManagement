<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'équipe - {{ $equipe->nom }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/team.css') }}">
    <style>
        .detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .titre-principal {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            padding: 1rem;
            background: var(--light-gray);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        
        .info-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .info-value {
            font-size: 1.05rem;
            color: var(--text-color);
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .member-item, .project-item, .subteam-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 8px;
            background: var(--light-gray);
            transition: all 0.2s ease;
        }
        
        .member-item:hover, .project-item:hover, .subteam-item:hover {
            background: #edf2f7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        
        .avatar-sm {
            width: 42px;
            height: 42px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .member-info {
            flex: 1;
        }
        
        .member-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .member-function {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .project-icon, .subteam-icon {
            background: rgba(41, 128, 185, 0.1);
            color: #2980b9;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 1.2rem;
        }
        
        .subteam-icon {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }
        
        .project-name, .subteam-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .no-items {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            border-radius: 8px;
            background: var(--light-gray);
            margin: 1rem 0;
        }
        
        .no-items i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #cbd5e0;
        }
        
        .badge-niveau {
            background: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .card-spacing {
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .detail-container {
                padding: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    @extends('layouts.collaborateur')
    
    @section('title', 'Détail de l équipe')
    
    @section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home me-1"></i>Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.equipes.index') }}">Mes équipes</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $equipe->nom }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="detail-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h2 class="titre-principal mb-3 mb-md-0">
                <i class="fas fa-users me-2"></i>{{ $equipe->nom }}
            </h2>
            <span class="badge-niveau">Niveau {{ $equipe->niveau_complet }}</span>
        </div>
        
        <!-- Carte Informations -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations sur l'équipe</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Créée par</div>
                        <div class="info-value">
                            <i class="fas fa-user me-2"></i>{{ $equipe->creator->name ?? 'Inconnu' }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Date de création</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-2"></i>{{ $equipe->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    @if($equipe->parent)
                    <div class="info-item">
                        <div class="info-label">Équipe parente</div>
                        <div class="info-value">
                            <i class="fas fa-level-up-alt me-2"></i>{{ $equipe->parent->nom }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="info-item">
                        <div class="info-label">Nombre de membres</div>
                        <div class="info-value">
                            <i class="fas fa-users me-2"></i>{{ $equipe->utilisateurs->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row card-spacing">
            <!-- Carte Membres -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Membres de l'équipe</h5>
                    </div>
                    <div class="card-body">
                        @if($equipe->utilisateurs->count() > 0)
                            <div class="list-container">
                                @foreach($equipe->utilisateurs as $membre)
                                <div class="member-item">
                                    <div class="avatar-sm">
                                        {{ substr($membre->name, 0, 1) }}
                                    </div>
                                    <div class="member-info">
                                        <div class="member-name">{{ $membre->name }}</div>
                                        <div class="member-function">{{ $membre->fonction?->nom ?? 'Fonction non spécifiée' }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-items">
                                <i class="fas fa-user-slash"></i>
                                <p class="mb-0 mt-2">Aucun membre dans cette équipe</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Carte Projets -->
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Projets associés</h5>
                    </div>
                    <div class="card-body">
                        @if($equipe->projets->count() > 0)
                            <div class="list-container">
                                @foreach($equipe->projets as $projet)
                                <div class="project-item">
                                    <div class="project-icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div>
                                        <div class="project-name">{{ $projet->nom }}</div>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $projet->date_debut->format('d/m/Y') }} - {{ $projet->date_fin_prevue->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-items">
                                <i class="fas fa-folder-minus"></i>
                                <p class="mb-0 mt-2">Aucun projet associé à cette équipe</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Carte Sous-équipes -->
        @if($equipe->children->isNotEmpty())
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Sous-équipes</h5>
            </div>
            <div class="card-body">
                <div class="list-container">
                    @foreach($equipe->children as $child)
                    <div class="subteam-item">
                        <div class="subteam-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="subteam-name">{{ $child->nom }}</div>
                            <div class="text-muted small mt-1">
                                {{ $child->utilisateurs->count() }} membres • Niveau {{ $child->niveau }}
                            </div>
                        </div>
                        <a href="{{ route('collaborateur.equipes.show', $child) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endsection

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>