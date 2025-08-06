@extends('layouts.chef')

@section('title', 'Modifier la tâche')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/edit-forms.css') }}">
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.taches.index', $projet) }}">Tâches de {{ $projet->nom }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier la tâche</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-edit me-2"></i>Modifier la tâche
        </h2>
        <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Modification de la tâche</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('chef_equipe.projets.taches.update', [$projet, $tache]) }}" method="POST" class="admin-form">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="titre" class="form-label required-field">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="{{ old('titre', $tache->titre) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="affecte_a" class="form-label required-field">Assigné à</label>
                        <select class="form-select" id="affecte_a" name="affecte_a" required>
                            <option value="">Sélectionnez un membre</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ old('affecte_a', $tache->affecte_a) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->fonction }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3">{{ old('description', $tache->description) }}</textarea>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <div class="position-relative">
                            <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                   value="{{ old('date_debut', $tache->date_debut->format('Y-m-d')) }}" required>
                            <i class="fas fa-calendar-day input-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <div class="position-relative">
                            <input type="date" class="form-control" id="date_fin_prevue" name="date_fin_prevue" 
                                   value="{{ old('date_fin_prevue', $tache->date_fin_prevue->format('Y-m-d')) }}" required>
                            <i class="fas fa-calendar-check input-icon"></i>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="priorite" class="form-label required-field">Priorité</label>
                        <select class="form-select" id="priorite" name="priorite" required>
                            @foreach($priorites as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ old('priorite', $tache->priorite) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select" id="statut" name="statut" required>
                            @foreach($statuts as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ old('statut', $tache->statut) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajout d'icônes pour les champs date
    const dateFields = document.querySelectorAll('input[type="date"]');
    dateFields.forEach(field => {
        const icon = document.createElement('i');
        icon.className = 'fas fa-calendar input-icon';
        field.parentNode.appendChild(icon);
    });
    
    // Validation des dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin_prevue');
    
    dateFin.addEventListener('change', function() {
        if (new Date(dateFin.value) < new Date(dateDebut.value)) {
            alert('La date de fin ne peut pas être antérieure à la date de début');
            dateFin.value = dateDebut.value;
        }
    });
});
</script>
@endsection