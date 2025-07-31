@extends('layouts.admin')

@section('title', 'Créer un nouveau projet')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau projet</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-plus-circle me-2"></i>Créer un nouveau projet
        </h2>
        <a href="{{ route('admin.projets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations du projet</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.projets.store') }}" method="POST" enctype="multipart/form-data">
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
                    <div class="col-12 mb-4">
                        <label for="equipe_id" class="form-label ">Équipe responsable</label>
                        <select class="form-select @error('equipe_id') is-invalid @enderror" id="equipe_id" name="equipe_id"  >
                            <option value="">Sélectionnez une équipe</option>
                            @foreach($equipes as $equipe)
                                <option value="{{ $equipe->id }}" {{ old('equipe_id') == $equipe->id ? 'selected' : '' }}>
                                    {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipe_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Budget -->
                    <div class="col-md-6 mb-3">
                        <label for="budget" class="form-label">Budget (DT)</label>
                        <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget') }}">
                        @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cahier des charges -->
                    <div class="col-12 mb-3">
                        <label for="cahier_charge" class="form-label">Cahier des charges</label>
                        <input type="file" class="form-control @error('cahier_charge') is-invalid @enderror" id="cahier_charge" name="cahier_charge">
                        @error('cahier_charge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Formats acceptés: PDF, DOC, DOCX (Max: 5MB)</small>
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Détails importants -->
                    <div class="col-12 mb-3">
                        <label for="details_importants" class="form-label">Détails importants</label>
                        <textarea class="form-control @error('details_importants') is-invalid @enderror" id="details_importants" name="details_importants" rows="3">{{ old('details_importants') }}</textarea>
                        @error('details_importants')
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
    // Initialisation de Select2
    $('#equipe_id').select2({
        placeholder: "Sélectionnez une équipe",
        allowClear: true
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