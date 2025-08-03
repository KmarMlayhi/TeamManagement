@extends('layouts.chef')

@section('title', 'Créer un nouveau projet')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau projet</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-plus-circle me-2"></i>Créer un nouveau projet
        </h2>
        <a href="{{ route('chef_equipe.projets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations du projet</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('chef_equipe.projets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-4">
                    <!-- Nom du projet -->
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label required-field">Nom du projet</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client -->
                    <div class="col-md-6 mb-3">
                        <label for="client" class="form-label">Client</label>
                        <input type="text" class="form-control @error('client') is-invalid @enderror" id="client" name="client" value="{{ old('client') }}">
                        @error('client')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div class="col-md-4 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                        @error('date_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <input type="date" class="form-control @error('date_fin_prevue') is-invalid @enderror" id="date_fin_prevue" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}" required>
                        @error('date_fin_prevue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="col-md-4 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                            <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Équipe -->
                    <!-- Équipe responsable (plusieurs équipes possibles) -->
                    <div class="col-12 mb-4">
                        <label for="equipe_ids" class="form-label required-field">Équipes responsables</label>
                        <select class="form-select @error('equipe_ids') is-invalid @enderror" 
                                id="equipe_ids" 
                                name="equipe_ids[]" 
                                multiple="multiple"
                                required>
                            @foreach($equipes as $equipe)
                                <option value="{{ $equipe->id }}" 
                                    {{ in_array($equipe->id, old('equipe_ids', [])) ? 'selected' : '' }}>
                                    {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipe_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs équipes</small>
                    </div>

                    <!-- Budget -->
                    <div class="col-md-6 mb-3">
                        <label for="budget" class="form-label">Budget (DT)</label>
                        <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget') }}">
                        @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Documents -->
                    <div class="col-12 mb-3">
                        <label for="documents" class="form-label">Documents</label>
                        <input type="file" class="form-control @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple>
                        @error('documents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers (PDF, DOC, DOCX, etc.)</small>
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-outline-danger">
                        <i class="fas fa-eraser me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
     $('#equipe_ids').select2({
        placeholder: "Sélectionnez une ou plusieurs équipes",
        allowClear: true,
        width: '100%',
        language: {
            noResults: () => "Aucune équipe trouvée",
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