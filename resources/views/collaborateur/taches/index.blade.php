@extends('layouts.collaborateur')

@section('title', 'Mes tâches - Projet ' . $projet->nom)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home me-1"></i>Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.equipes.index') }}">Mes équipes</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.equipes.show', $equipe) }}">{{ $equipe->nom }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mes tâches</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-tasks"></i>
            Mes tâches - Projet {{ $projet->nom }}
        </h2>
        <span class="badge bg-primary">
            {{ $taches->count() }} tâche(s)
        </span>
    </div>

    @if($taches->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Échéance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taches as $tache)
                            <tr>
                                <td>{{ $tache->titre }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($tache->description, 50) }}</td>
                                <td>
                                    @if($tache->priorite === 'haute')
                                        <span class="badge bg-danger">Haute</span>
                                    @elseif($tache->priorite === 'moyenne')
                                        <span class="badge bg-warning">Moyenne</span>
                                    @else
                                        <span class="badge bg-success">Basse</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tache->statut === 'a_faire')
                                        <span class="badge bg-secondary">À faire</span>
                                    @elseif($tache->statut === 'en_cours')
                                        <span class="badge bg-primary">En cours</span>
                                    @else
                                        <span class="badge bg-success">Terminé</span>
                                    @endif
                                </td>
                                <td>{{ $tache->date_fin_prevue->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('collaborateur.taches.show', $tache) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucune tâche ne vous est assignée dans ce projet.
        </div>
    @endif
</div>
@endsection