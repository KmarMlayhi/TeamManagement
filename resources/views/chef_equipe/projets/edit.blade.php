@extends('layouts.chef')

@section('title', 'Modifier un projet')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/edit-forms.css') }}">
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier {{ $projet->nom }}</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-project-diagram me-2"></i>Modifier le projet
        </h2>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modification du projet</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('chef_equipe.projets.update', $projet->id) }}" method="POST" class="admin-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Nom du projet -->
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label required-field">Nom du projet</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="{{ old('nom', $projet->nom) }}" required>
                    </div>

                    <!-- Client -->
                    <div class="col-md-6 mb-3">
                        <label for="client" class="form-label">Client</label>
                        <input type="text" class="form-control" id="client" name="client" 
                               value="{{ old('client', $projet->client) }}">
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3">{{ old('description', $projet->description) }}</textarea>
                    </div>

                    <!-- Dates -->
                    <div class="col-md-6 mb-3">
                        <label for="date_debut" class="form-label required-field">Date de début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" 
                               value="{{ old('date_debut', $projet->date_debut->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="date_fin_prevue" class="form-label required-field">Date de fin prévue</label>
                        <input type="date" class="form-control" id="date_fin_prevue" name="date_fin_prevue" 
                               value="{{ old('date_fin_prevue', $projet->date_fin_prevue->format('Y-m-d')) }}" required>
                    </div>

                    <!-- Statut -->
                    <div class="col-md-6 mb-3">
                        <label for="statut" class="form-label required-field">Statut</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="en_attente" {{ $projet->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_cours" {{ $projet->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ $projet->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                            <option value="suspendu" {{ $projet->statut == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>

                    <!-- Budget -->
                    <div class="col-md-6 mb-3">
                        <label for="budget" class="form-label">Budget (DH)</label>
                        <input type="number" class="form-control" id="budget" name="budget" 
                               value="{{ old('budget', $projet->budget) }}" step="0.01">
                    </div>

                    <!-- Équipes -->
                    <div class="col-12 mb-4">
                        <label for="equipe_ids" class="form-label required-field">Équipes assignées</label>
                        <select class="form-select select2-multiple" id="equipe_ids" name="equipe_ids[]" multiple="multiple" required>
                            @foreach($equipes as $equipe)
                                <option value="{{ $equipe->id }}" 
                                    {{ in_array($equipe->id, old('equipe_ids', $projet->equipes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Section Documents -->
            <div class="col-12 mb-4">
                <label class="form-label">Documents</label>
                
                <!-- Liste des documents existants -->
                <div id="existing-documents" class="mb-3">
                    @foreach($projet->documents as $document)
                    <div class="document-item d-flex align-items-center mb-2 p-2 bg-light rounded" id="document-{{ $document->id }}">
                        <div class="flex-grow-1">
                            <i class="fas fa-file me-2"></i>
                            <a href="{{ $document->url }}" target="_blank">{{ $document->nom }}</a>
                            <small class="text-muted ms-2">({{ number_format($document->taille / 1024, 2) }} KB)</small>
                        </div>
                        <div class="document-actions">
                            <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                    data-document-id="{{ $document->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Nouveaux documents -->
                <div class="file-upload-container">
                    <label class="form-label">Ajouter de nouveaux documents</label>
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" id="new_documents" name="new_documents[]" multiple 
                            style="display: none;" onchange="updateFileList(this)">
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="document.getElementById('new_documents').click()">
                            <i class="fas fa-folder-open me-1"></i> Choisir des fichiers
                        </button>
                        <input type="text" class="form-control" id="file-list" 
                            placeholder="Aucun fichier sélectionné" readonly>
                    </div>
                    <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max 10MB chacun)</small>
                </div>
            </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('chef_equipe.projets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('chef_equipe.projets.index') }}" class="btn btn-outline-primary">
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
    // Initialisation de Select2
    $('.select2-multiple').select2({
        placeholder: "Sélectionnez les équipes...",
        width: '100%',
        language: {
            noResults: function() {
                return "Aucun résultat trouvé";
            }
        }
    });

    // Gestion de la suppression des documents
    $(document).on('click', '.delete-document', function() {
        const documentId = $(this).data('document-id');
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            $.ajax({
                url: `/chef-equipe/documents/${documentId}`,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#document-' + documentId).remove();
                    showToast('success', 'Document supprimé avec succès');
                },
                error: function(xhr) {
                    showToast('error', 'Une erreur est survenue lors de la suppression');
                }
            });
        }
    });

    // Affichage de la liste des fichiers sélectionnés
    function updateFileList(input) {
        const fileList = document.getElementById('file-list');
        if (input.files.length > 0) {
            const files = [];
            for (let i = 0; i < input.files.length; i++) {
                files.push(input.files[i].name);
            }
            fileList.value = files.join(', ');
        } else {
            fileList.value = 'Aucun fichier sélectionné';
        }
    }

    // Fonction pour afficher les notifications
    function showToast(type, message) {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
        $('body').append(toast);
        $('.toast').toast('show');
        setTimeout(() => $('.toast').remove(), 3000);
    }
});
</script>
@endsection