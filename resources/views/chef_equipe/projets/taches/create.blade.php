@extends('layouts.chef')

@section('title', isset($tache) ? 'Modifier la tâche' : 'Créer une tâche')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
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
            <li class="breadcrumb-item active" aria-current="page">
                {{ isset($tache) ? 'Modifier la tâche' : 'Créer une tâche' }}
            </li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-tasks me-2"></i>
            {{ isset($tache) ? 'Modifier la tâche' : 'Créer une tâche' }}
        </h2>
        <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informations de la tâche
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ isset($tache) 
                ? route('chef_equipe.projets.taches.update', [$projet, $tache]) 
                : route('chef_equipe.projets.taches.store', $projet) }}" 
                method="POST">
                @csrf
                @if(isset($tache)) @method('PUT') @endif

                <div class="row mb-4">
                    <!-- Titre -->
                    <div class="col-md-6 mb-3">
                        <label for="titre" class="form-label required-field">Titre</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" 
                               value="{{ old('titre', $tache->titre ?? '') }}" required>
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Assigné à -->
                    <div class="col-md-6 mb-3">
                        <label for="affecte_a" class="form-label required-field">Assigné à</label>
                        <select class="form-select select2-single @error('affecte_a') is-invalid @enderror" id="affecte_a" name="affecte_a" required>
                            <option value="">Sélectionnez un membre</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ old('affecte_a', $tache->affecte_a ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->fonction }})
                                </option>
                            @endforeach
                        </select>
                        @error('affecte_a')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $tache->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div class="col-md-3 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <div class="position-relative">
                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" 
                                   value="{{ old('date_debut', isset($tache) ? $tache->date_debut->format('Y-m-d') : '') }}" required>
                            <i class="fas fa-calendar-day input-icon"></i>
                        </div>
                        @error('date_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <div class="position-relative">
                            <input type="date" class="form-control @error('date_fin_prevue') is-invalid @enderror" id="date_fin_prevue" name="date_fin_prevue" 
                                   value="{{ old('date_fin_prevue', isset($tache) ? $tache->date_fin_prevue->format('Y-m-d') : '') }}" required>
                            <i class="fas fa-calendar-check input-icon"></i>
                        </div>
                        @error('date_fin_prevue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Priorité -->
                    <div class="col-md-3 mb-3">
                        <label for="priorite" class="form-label required-field">Priorité</label>
                        <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                            @foreach(($priorites ?? []) as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ old('priorite', $tache->priorite ?? '') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('priorite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Statut (seulement pour l'édition) -->
                    @if(isset($tache))
                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                           @foreach($statuts as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ old('statut', $tache->statut ?? '') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ isset($tache) ? 'Mettre à jour' : 'Créer' }}
                        </button>
                        <button type="reset" class="btn btn-outline-danger">
                            <i class="fas fa-eraser me-1"></i> Réinitialiser
                        </button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialiser Select2 pour le champ "Assigné à"
    $('.select2-single').select2({
        placeholder: "Sélectionnez un membre",
        allowClear: true,
        width: '100%',
        language: {
            noResults: () => "Aucun membre trouvé",
            searching: () => "Recherche..."
        }
    });

    // Validation des dates
    $('#date_fin_prevue').change(function() {
        const debut = new Date($('#date_debut').val());
        const fin = new Date($(this).val());
        
        if (fin < debut) {
            alert('La date de fin doit être postérieure à la date de début');
            $(this).val('');
        }
    });
});
</script>
@endsection