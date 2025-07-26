@extends('layouts.admin')

@section('title', 'Créer une équipe')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<h2 class="mb-4">Gestion des Équipes</h2>

<div class="row mb-4">
    <!-- Vous pouvez ajouter des compteurs ici si nécessaire -->
</div>

<div class="card dashboard-card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Créer une nouvelle équipe</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('equipes.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <div class="row mb-4">
                <!-- Nom de l'équipe -->
                <div class="col-md-6 mb-3">
                    <label for="nom" class="form-label">Nom de l'équipe <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                    <div class="invalid-feedback">Veuillez saisir un nom valide.</div>
                </div>

                <!-- Équipe mère -->
                <div class="col-md-6 mb-3">
                    <label for="parent_id" class="form-label">Équipe parente</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">Aucune (équipe principale)</option>
                        @foreach($equipes as $equipe)
                            <option value="{{ $equipe->id }}">{{ $equipe->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sélection des membres -->
                <div class="col-12 mb-3">
                    <label for="user_ids" class="form-label">Membres <span class="text-danger">*</span></label>
                    <select class="form-select select2-multiple" id="user_ids" name="user_ids[]" multiple="multiple" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Sélectionnez au moins un membre.</div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex justify-content-between">
                <div>
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Annuler
                    </a>
                </div>
                <div>
                    <a href="{{ route('equipes.index') }}" class="btn btn-info">
                        <i class="fas fa-list me-1"></i> Voir les équipes
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "Sélectionnez les membres",
            width: '100%'
        });

        // Validation Bootstrap
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    });
</script>
@endsection