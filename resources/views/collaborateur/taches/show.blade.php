@extends('layouts.collaborateur')

@section('title', $tache->titre)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home me-1"></i>Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.equipes.index') }}">Mes équipes</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.equipes.show', $tache->projet->equipes->first()) }}">{{ $tache->projet->equipes->first()->nom }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.projets.taches', $tache->projet) }}">Mes tâches</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $tache->titre }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-tasks"></i>
            {{ $tache->titre }}
        </h2>
        <span class="badge bg-primary">
            Projet: {{ $tache->projet->nom }}
        </span>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>Description</h4>
                    <p>{{ $tache->description }}</p>
                    
                    <div class="mt-4">
                        <h4>Instructions</h4>
                        <p>{{ $tache->instructions ?? "Aucune instruction spécifique." }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Détails de la tâche</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Priorité:</strong>
                                    @if($tache->priorite === 'haute')
                                        <span class="badge bg-danger">Haute</span>
                                    @elseif($tache->priorite === 'moyenne')
                                        <span class="badge bg-warning">Moyenne</span>
                                    @else
                                        <span class="badge bg-success">Basse</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Statut:</strong>
                                    @if($tache->statut === 'a_faire')
                                        <span class="badge bg-secondary">À faire</span>
                                    @elseif($tache->statut === 'en_cours')
                                        <span class="badge bg-primary">En cours</span>
                                    @else
                                        <span class="badge bg-success">Terminé</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Date de début:</strong>
                                    <span>{{ $tache->date_debut->format('d/m/Y') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Date d'échéance:</strong>
                                    <span>{{ $tache->date_fin_prevue->format('d/m/Y') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Assignée par:</strong>
                                    <span>{{ $tache->createur->name ?? 'Inconnu' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour les commentaires et soumission de travail -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="tacheTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="commentaires-tab" data-bs-toggle="tab" data-bs-target="#commentaires" type="button" role="tab">Commentaires</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="soumission-tab" data-bs-toggle="tab" data-bs-target="#soumission" type="button" role="tab">Soumettre du travail</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="tacheTabsContent">
                <div class="tab-pane fade show active" id="commentaires" role="tabpanel">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        La fonctionnalité de commentaires sera bientôt disponible.
                    </div>
                </div>
                <div class="tab-pane fade" id="soumission" role="tabpanel">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        La fonctionnalité de soumission de travail sera bientôt disponible.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection