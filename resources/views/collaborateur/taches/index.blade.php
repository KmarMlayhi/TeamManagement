@extends('layouts.collaborateur')

@section('title', 'Mes tâches - Projet ' . $projet->nom)

@section('content')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<style>
    .tache-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 10px;
        overflow: hidden;
        border: none;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    .tache-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }
    .card-header-custom {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 15px 20px;
    display: flex;
    flex-direction: column; /* empile verticalement */
    align-items: flex-start; /* aligne à gauche */
    gap: 4px; /* petit espace entre titre et priorité */
    }
    .tache-priority {
        padding: 4px 6px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 200;
    }
    .tache-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .tache-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.05rem;
        margin-bottom: 5px;
    }
    .tache-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 10px;
        line-height: 1.5;
    }
    .tache-date {
        font-size: 0.85rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    .tache-date i {
        margin-right: 6px;
        width: 16px;
        text-align: center;
    }
    .action-btn {
        padding: 8px 15px;
        font-size: 0.85rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .no-tasks-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
    }
    .no-tasks-icon {
        font-size: 3rem;
        color: #a0aec0;
        margin-bottom: 20px;
    }
    .task-count-badge {
        background-color: var(--primary-color);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.home') }}">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.equipes.index') }}">Mes équipes</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.equipes.show', $equipe) }}">
                        <i class="fas fa-users me-1"></i>{{ $equipe->nom }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-tasks me-1"></i>Mes tâches
                </li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-tasks me-2"></i>Mes tâches - Projet: {{ $projet->nom }}
        </h3>
        <a href="{{ route('collaborateur.projets.taches.kanban', ['projet' => $projet, 'equipe_id' => $equipe->id]) }}" 
           class="btn btn-outline-primary me-2">
            <i class="fas fa-columns me-1"></i> Vue Kanban
        </a>
        {{-- <span class="task-count-badge">
            {{ $taches->count() }} tâche(s)
        </span> --}}
         <!-- Nouveau bouton pour la vue Kanban -->
      
    </div>

    @if($taches->count() > 0)
        <div class="row">
            @foreach($taches as $tache)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card tache-card h-100">
                    <div class="card-header-custom">
                        <div class="tache-title">
                            <i class="fas fa-tag me-1 text-muted"></i>
                            {{ $tache->titre }}
                        </div>

                        @if($tache->priorite === 'haute')
                            <span class="tache-priority bg-danger text-white">
                                <i class="fas fa-exclamation-circle me-1"></i>Haute
                            </span>
                        @elseif($tache->priorite === 'moyenne')
                            <span class="tache-priority bg-warning text-dark">
                                <i class="fas fa-flag me-1"></i>Moyenne
                            </span>
                        @else
                            <span class="tache-priority bg-success text-white">
                                <i class="fas fa-flag me-1"></i>Basse
                            </span>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="tache-description">
                            @if($tache->description)
                                {{ \Illuminate\Support\Str::limit($tache->description, 120) }}
                            @else
                                <span class="text-muted fst-italic">Aucune description</span>
                            @endif
                        </div>
                        
                        <div class="tache-date">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <span class="me-3">Début: {{ $tache->date_debut->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="tache-date">
                            <i class="fas fa-flag-checkered text-danger"></i>
                            <span>Échéance: {{ $tache->date_fin_prevue->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                @if($tache->statut === 'a_faire')
                                    <span class="tache-status bg-light text-dark border">
                                        <i class="fas fa-circle-notch me-1"></i>À faire
                                    </span>
                                @elseif($tache->statut === 'en_cours')
                                    <span class="tache-status bg-primary text-white">
                                        <i class="fas fa-spinner me-1"></i>En cours
                                    </span>
                                @else
                                    <span class="tache-status bg-success text-white">
                                        <i class="fas fa-check-circle me-1"></i>Terminé
                                    </span>
                                @endif
                            </div>
                            
                            <a href="{{ route('collaborateur.taches.show', $tache) }}" 
                               class="btn btn-sm btn-outline-primary action-btn">
                                <i class="fas fa-eye me-1"></i>Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card no-tasks-card">
            <div class="card-body">
                <div class="no-tasks-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h5 class="mb-3" style="color: #718096;">Aucune tâche assignée</h5>
                <p class="text-muted mb-4">
                    Vous n'avez actuellement aucune tâche dans ce projet. 
                    Vous serez notifié lorsqu'une nouvelle tâche vous sera assignée.
                </p>
                <a href="{{ route('collaborateur.equipes.show', $equipe) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Retour à l'équipe
                </a>
            </div>
        </div>
    @endif
</div>
@endsection