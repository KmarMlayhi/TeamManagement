@extends('layouts.chef')

@section('title', 'Modifier la tâche')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/edit-forms.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.taches.index', $projet) }}">Tâches de {{ $projet->nom }}</a></li>
            <li class="breadcrumb-item active">Modifier la tâche</li>
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
            <form action="{{ route('chef_equipe.projets.taches.update', [$projet, $tache]) }}" method="POST" class="admin-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Titre -->
                    <div class="col-md-6 mb-3">
                        <label for="titre" class="form-label required-field">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="{{ old('titre', $tache->titre) }}" required>
                    </div>

                    <!-- Assignation multiple -->
                    <div class="col-md-6 mb-3">
                        <label for="users" class="form-label required-field">Assigné à</label>
                        <select class="form-select select2-multiple" id="users" name="users[]" multiple required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ in_array($user->id, old('users', $tache->users->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->fonction?->nom ?? 'Non spécifié' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $tache->description) }}</textarea>
                    </div>

                    <!-- Dates -->
                    <div class="col-md-3 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" 
                               value="{{ old('date_debut', $tache->date_debut->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <input type="date" class="form-control" id="date_fin_prevue" name="date_fin_prevue" 
                               value="{{ old('date_fin_prevue', $tache->date_fin_prevue->format('Y-m-d')) }}" required>
                    </div>

                    <!-- Priorité et Statut -->
                    <div class="col-md-3 mb-3">
                        <label for="priorite" class="form-label required-field">Priorité</label>
                        <select class="form-select" id="priorite" name="priorite" required>
                            @foreach($priorites as $key => $value)
                                <option value="{{ $key }}" {{ old('priorite', $tache->priorite) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select" id="statut" name="statut" required>
                            @foreach($statuts as $key => $value)
                                <option value="{{ $key }}" {{ old('statut', $tache->statut) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Documents existants -->
                    <div class="col-12 mb-4">
                        <label class="form-label">Documents existants</label>
                        <div id="existing-documents" class="mb-3">
                            @foreach($tache->taskdocuments as $doc)
                                <div class="document-item d-flex align-items-center mb-2 p-2 bg-light rounded" id="document-{{ $doc->id }}">
                                    <div class="flex-grow-1">
                                        <i class="fas fa-file me-2"></i>
                                        <a href="{{ asset('storage/' . $doc->chemin) }}" target="_blank">{{ $doc->nom_original }}</a>
                                        <small class="text-muted ms-2">({{ number_format(Storage::disk('public')->size($doc->chemin) / 1024, 2) }} KB)</small>
                                    </div>
                                    <div class="document-actions">
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" data-document-id="{{ $doc->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Ajouter nouveaux documents -->
                        <div class="file-upload-container">
                            <label class="form-label">Ajouter de nouveaux documents</label>
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" id="new_documents" name="new_documents[]" multiple style="display: none;" onchange="updateFileList(this)">
                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('new_documents').click()">
                                    <i class="fas fa-folder-open me-1"></i> Choisir des fichiers
                                </button>
                            </div>
                            <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max 10MB chacun)</small>
                            <div id="selected-files" class="mt-2 text-muted small"></div>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button>
                        <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Annuler</a>
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
    // Select2 pour l'assignation multiple
    $('.select2-multiple').select2({
        placeholder: "Sélectionnez les membres...",
        width: '100%'
    });

    // Supprimer un document existant
    $(document).on('click', '.delete-document', function() {
        const documentId = $(this).data('document-id');
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            $.ajax({
                url: `/chef-equipe/documents/${documentId}`,
                type: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function() { $('#document-' + documentId).remove(); },
                error: function() { alert('Erreur lors de la suppression'); }
            });
        }
    });
});

// Affichage fichiers sélectionnés
function updateFileList(input) {
    const selectedFilesDiv = document.getElementById('selected-files');
    selectedFilesDiv.innerHTML = '';
    if (input.files.length > 0) {
        const ul = document.createElement('ul');
        ul.classList.add('mb-0', 'ps-3');
        for (let i = 0; i < input.files.length; i++) {
            const li = document.createElement('li');
            li.textContent = input.files[i].name;
            ul.appendChild(li);
        }
        selectedFilesDiv.appendChild(ul);
    } else {
        selectedFilesDiv.textContent = 'Aucun fichier sélectionné';
    }
}
</script>
@endsection
