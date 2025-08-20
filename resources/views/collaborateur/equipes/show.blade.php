<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'équipe - {{ $equipe->nom }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/projetsCollab.css') }}">
    <script>
    
    window.currentGlobalEquipeId = {{ $equipe->id }};
    </script>
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
            <h2 class="titre-principal">
                <i class="fas fa-users"></i>
                <span>{{ $equipe->nom }}</span>
            </h2>
            <span class="badge-niveau">
                <i class="fas fa-layer-group"></i>
                Niveau {{ $equipe->niveau_complet }}
            </span>
        </div>
        
        <!-- Carte Informations -->
        <div class="card mb-3 team-card-access">
            <div class="card-header team-card-access">
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
        
        <div class="row-cards">
            <!-- Carte Membres -->
            <div class="card team-card-access">
                <div class="card-header team-card-access">
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
                                    <div class="member-email">{{ $membre->email ?? 'Email non spécifié' }}</div>
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
            
            <!-- Carte Projets avec détails -->
            <div class="card team-card-access">
                <div class="card-header team-card-access">
                    <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Projets associés</h5>
                </div>
                <div class="card-body">
                    @if($equipe->projets->count() > 0)
                        <div class="list-container">
                            @foreach($equipe->projets as $projet)
                            <div class="project-item" data-id="{{ $projet->id }}">
                                <div class="project-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="project-name">{{ $projet->nom }}</div>
                                        <span class="project-statut 
                                            @if($projet->statut == 'en_attente') statut-en-attente
                                            @elseif($projet->statut == 'en_cours') statut-en-cours
                                            @elseif($projet->statut == 'termine') statut-termine
                                            @elseif($projet->statut == 'suspendu') statut-suspendu
                                            @endif">
                                            {{ $projet->statut_text }}
                                        </span>
                                    </div>
                                    
                                    <div class="project-description">
                                        {{ $projet->description ?? 'Aucune description disponible' }}
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $projet->date_debut->format('d/m/Y') }} - {{ $projet->date_fin_prevue->format('d/m/Y') }}
                                        </div>
                                        
                                        <div class="text-muted small">
                                            <i class="fas fa-percentage me-1"></i>
                                            {{ $projet->avancement }}% complété
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-2">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: {{ $projet->avancement }}%;" 
                                            aria-valuenow="{{ $projet->avancement }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary view-details-btn">
                                    <i class="fas fa-eye me-1"></i> Détails
                                </button>
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
        
        <!-- Carte Sous-équipes -->
        @if($equipe->children->isNotEmpty())
        <div class="card team-card-access">
            <div class="card-header team-card-access">
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
    
    <!-- Modal pour les détails du projet -->
    <div class="modal fade" id="projetDetailsModal" tabindex="-1" aria-labelledby="projetDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projetDetailsModalLabel">
                        <i class="fas fa-project-diagram"></i>
                        <span id="modal-project-name"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="projetDetailsContent">
                    <div class="spinner-container">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-3">Chargement des détails du projet...</p>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endsection

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('js/detailsCollaborateur.js') }}"></script>
</body>
</html>