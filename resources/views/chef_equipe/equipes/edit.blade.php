@extends('layouts.chef')

@section('title', 'Modifier une équipe')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.equipes.index') }}">Équipes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier {{ $equipe->nom }}</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-users me-2"></i>Modifier l'équipe
        </h2>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modification de l'équipe</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('chef_equipe.equipes.update', $equipe->id) }}" method="POST" class="admin-form">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Nom de l'équipe -->
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label required-field">Nom de l'équipe</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="{{ old('nom', $equipe->nom) }}" required>
                    </div>

                    <!-- Équipe mère -->
                    <div class="col-md-6 mb-3">
                        <label for="parent_id" class="form-label">Équipe parente</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Aucune (équipe principale)</option>
                            @foreach($autresEquipes as $autre)
                                <option value="{{ $autre->id }}" {{ $equipe->parent_id == $autre->id ? 'selected' : '' }}>
                                    {{ $autre->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                  <!-- Sélection des membres -->
<div class="col-12 mb-4">
    <label for="user_ids" class="form-label required-field">Membres de l'équipe</label>
    <select class="form-select select2-multiple" id="user_ids" name="user_ids[]" multiple="multiple" required>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ isset($equipe) && $equipe->utilisateurs->contains($user->id) ? 'selected' : '' }}>
                {{ $user->name }} ({{ $user->fonction }})
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">Sélectionnez un ou plusieurs membres</small>
</div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('chef_equipe.equipes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('chef_equipe.equipes.index') }}" class="btn btn-outline-primary">
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
            placeholder: "Sélectionnez les membres...",
            width: '100%',
            language: {
                noResults: function() {
                    return "Aucun résultat trouvé";
                }
            }
        });
    });
</script>
@endsection