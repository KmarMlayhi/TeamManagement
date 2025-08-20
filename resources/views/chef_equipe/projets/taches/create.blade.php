@extends('layouts.chef')

@section('title', isset($tache) ? 'Modifier la tâche' : 'Créer une tâche')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<link rel="stylesheet" href="{{ asset('css/edit-forms.css') }}">
@endsection

@section('content')
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.taches.index', $projet) }}">Tâches de {{ $projet->nom }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ isset($tache) ? 'Modifier la tâche' : 'Créer une tâche' }}</li>
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
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations de la tâche</h5>
        </div>

        <div class="card-body">
            <form action="{{ isset($tache) 
                ? route('chef_equipe.projets.taches.update', [$projet, $tache]) 
                : route('chef_equipe.projets.taches.store', $projet) }}" 
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($tache)) @method('PUT') @endif

                <div class="row mb-4">
                    <!-- Titre -->
                    <div class="col-md-6 mb-3">
                        <label for="titre" class="form-label required-field">Titre</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" 
                               value="{{ old('titre', $tache->titre ?? '') }}" required>
                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Assigné à (multiple) -->
                    <div class="col-md-6 mb-3">
                        <label for="affecte_a" class="form-label required-field">Assigné à</label>
                        <select class="form-select select2-multiple @error('users') is-invalid @enderror" 
                                id="affecte_a" name="users[]" multiple required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (collect(old('users', isset($tache) ? $tache->users->pluck('id')->toArray() : []))->contains($user->id)) ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->fonction?->nom ?? 'Non spécifié' }}
                                </option>
                            @endforeach
                        </select>
                        @error('affecte_a')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $tache->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Dates -->
                    <div class="col-md-3 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" 
                               value="{{ old('date_debut', isset($tache) ? $tache->date_debut->format('Y-m-d') : '') }}" required>
                        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <input type="date" class="form-control @error('date_fin_prevue') is-invalid @enderror" id="date_fin_prevue" name="date_fin_prevue" 
                               value="{{ old('date_fin_prevue', isset($tache) ? $tache->date_fin_prevue->format('Y-m-d') : '') }}" required>
                        @error('date_fin_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Priorité -->
                    <div class="col-md-3 mb-3">
                        <label for="priorite" class="form-label required-field">Priorité</label>
                        <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                            @foreach(($priorites ?? []) as $key => $value)
                                <option value="{{ $key }}" {{ old('priorite', $tache->priorite ?? '') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('priorite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Statut (édition seulement) -->
                    @if(isset($tache))
                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                            @foreach($statuts as $key => $value)
                                <option value="{{ $key }}" {{ old('statut', $tache->statut ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                </div>

                <!-- Documents -->
                <div class="file-upload-container">
                        <label for="taskdocuments" class="form-label">Documents</label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('taskdocuments') is-invalid @enderror" 
                                id="taskdocuments" name="taskdocuments[]" multiple 
                                style="display: none;" 
                                onchange="updateFileList(this)">
                            <button class="btn btn-custom" type="button" 
                                    onclick="document.getElementById('taskdocuments').click()">
                                <i class="fas fa-folder-open me-1"></i> Choisir des fichiers
                            </button>
                        </div>
                        <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers (PDF, DOC, DOCX, etc.)</small>
                         <!-- Zone pour afficher les fichiers sélectionnés -->
                        <div id="selected-files" class="mt-2 text-muted small"></div>
                        @error('taskdocuments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                    </div>

                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ isset($tache) ? 'Mettre à jour' : 'Créer' }}
                        </button>
                        <button type="reset" class="btn btn-outline-danger" id="resetBtn">
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
    $('.select2-multiple').select2({
        placeholder: "Sélectionnez un ou plusieurs membres",
        allowClear: true,
        width: '100%',
        language: { noResults: () => "Aucun membre trouvé", searching: () => "Recherche..." }
    });

    $('#date_fin_prevue').change(function() {
        const debut = new Date($('#date_debut').val());
        const fin = new Date($(this).val());
        if(fin < debut){ alert('La date de fin doit être postérieure à la date de début'); $(this).val(''); }
    });

    const inputFile = document.getElementById('taskdocuments');
    updateFileList(inputFile);
    
    // Réinitialisation complète du formulaire
    $('#resetBtn').click(function() {
        // Réinitialiser l'affichage des fichiers
        $('#selected-files').html('<em>Aucun fichier sélectionné</em>');
        
        // Réinitialiser le champ fichier
        $('#taskdocuments').val('');
    });
});

function updateFileList(input) {
    const displayDiv = document.getElementById('selected-files');
    
    if (input.files && input.files.length > 0) {
        const files = [];
        for (let i = 0; i < input.files.length; i++) {
            files.push(input.files[i].name);
        }

        // Affiche les fichiers dans le div
        displayDiv.innerHTML = `
            <strong>Fichiers sélectionnés :</strong>
            <ul class="mb-0">
                ${files.map(file => `<li>${file}</li>`).join('')}
            </ul>
        `;
    } else {
        displayDiv.innerHTML = '<em>Aucun fichier sélectionné</em>';
    } }
</script>
@endsection
