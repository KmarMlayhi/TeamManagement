@extends('layouts.admin')

@section('title', 'Créer une équipe')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('equipes.index') }}">Équipes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouvelle équipe</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-users me-2"></i>Gestion des Équipes
        </h2>
        <a href="{{ route('equipes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Création d'une nouvelle équipe</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('equipes.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row mb-4">
                    <!-- Nom de l'équipe -->
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label required-field">Nom de l'équipe</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>Ce champ est obligatoire
                            </div>
                        @enderror
                    </div>

                    <!-- Équipe mère -->
                    <div class="col-md-6 mb-3">
                        <label for="parent_id" class="form-label">Équipe parente</label>
                        <select class="form-select select2-single @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id">
                            <option value="">Aucune (équipe principale)</option>
                            @foreach($equipes as $equipe)
                                <option value="{{ $equipe->id }}" {{ old('parent_id') == $equipe->id ? 'selected' : '' }}>
                                    {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sélection des membres -->
                    <div class="col-12 mb-4">
                        <label for="user_ids" class="form-label required-field">Membres de l'équipe</label>
                        <select class="form-select select2-multiple @error('user_ids') is-invalid @enderror" 
                                id="user_ids" name="user_ids[]" multiple="multiple" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>Veuillez sélectionner au moins un membre
                            </div>
                        @enderror
                        <small class="form-text text-muted">Sélection multiple possible</small>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <button type="reset" class="btn btn-outline-danger">
                            <i class="fas fa-eraser me-1"></i> Effacer
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('equipes.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Liste des équipes
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialisation des Select2
    $('.select2-single').select2({
        placeholder: "Sélectionnez une option",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Aucun résultat trouvé";
            }
        }
    });

    $('.select2-multiple').select2({
        placeholder: "Sélectionnez les membres...",
        width: '100%',
        language: {
            noResults: function() {
                return "Aucun résultat trouvé";
            }
        }
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