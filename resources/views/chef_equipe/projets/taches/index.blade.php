@extends('layouts.chef')

@section('title', 'Tâches du projet: ' . $projet->nom)

@section('content')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tâches de {{ $projet->nom }}</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-tasks me-2"></i>Tâches du projet: {{ $projet->nom }}
        </h3>
        <div>
            <a href="{{ route('chef_equipe.projets.taches.kanban', $projet) }}" class="btn btn-outline-primary">
                <i class="fas fa-columns me-1"></i> Vue Kanban
            </a>
            <a href="{{ route('chef_equipe.projets.taches.create', $projet) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouvelle tâche
            </a>
        </div>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des tâches</h5>
            @if(!$taches->isEmpty())
                <span class="badge bg-primary rounded-pill">{{ $taches->count() }}</span>
            @endif
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
                </div>
            @endif

            @if($taches->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>Aucune tâche créée pour ce projet.
                </div>
            @else
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-hover table-taches" style="min-width: 850px;">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center"><i class="fas fa-arrows-alt"></i></th>
                                <th>Titre</th>
                                <th>Assigné à</th>
                                <th>Documents</th>
                                <th>Dates</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th width="120" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @foreach($taches as $tache)
                            <tr data-id="{{ $tache->id }}">
                                <td class="handle text-center" style="cursor: move;">
                                    <i class="fas fa-grip-lines text-muted"></i>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $tache->titre }}</div>
                                    @if($tache->description)
                                        <small class="text-muted d-block mt-1">{{ Str::limit($tache->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($tache->users->isNotEmpty())
                                        <div class="d-flex flex-column">
                                            @foreach($tache->users as $user)
                                                <div class="d-flex align-items-center mb-1">
                                                    <div>
                                                        <div class="fw-medium">{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->fonction?->nom ?? 'Non spécifié' }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="openModal({{ $tache->id }})">
                                        <i class="fas fa-file-alt me-1"></i> Documents
                                    </button>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="text-muted"><i class="fas fa-play-circle me-1"></i> {{ $tache->date_debut->format('d/m/Y') }}</small>
                                        <small class="text-muted"><i class="fas fa-flag-checkered me-1"></i> {{ $tache->date_fin_prevue->format('d/m/Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $priorityClass = [
                                            'basse' => 'badge bg-light text-dark border',
                                            'moyenne' => 'badge bg-primary',
                                            'haute' => 'badge bg-danger'
                                        ][$tache->priorite];
                                    @endphp
                                    <span class="{{ $priorityClass }}">
                                        <i class="fas fa-flag me-1"></i>{{ $priorites[$tache->priorite] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'a_faire' => 'badge bg-light text-dark border',
                                            'en_cours' => 'badge bg-primary',
                                            'termine' => 'badge bg-success'
                                        ][$tache->statut];
                                    @endphp
                                    <span class="{{ $statusClass }}">
                                       <i class="fas fa-circle me-1 small"></i>{{ $statuts[$tache->statut] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('chef_equipe.projets.taches.edit', [$projet, $tache]) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Modifier">
                                           <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('chef_equipe.projets.taches.destroy', [$projet, $tache]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modales Documents Custom -->
                @foreach($taches as $tache)
                <div id="documentsModal{{ $tache->id }}" class="custom-modal">
                    <div class="custom-modal-content">
                        <div class="custom-modal-header">
                            <h5>Documents - {{ $tache->titre }}</h5>
                            <span class="custom-modal-close" onclick="closeModal({{ $tache->id }})">&times;</span>
                        </div>
                        <div class="custom-modal-body">
                            <ul class="list-group mb-3">
                                @php
                                    $parents = $tache->taskdocuments->whereNull('parent_id');
                                @endphp

                                @foreach($parents as $parent)
                                    <li class="list-group-item">
                                        <strong>{{ $parent->nom_original }}</strong>
                                        @php
                                            $versions = $tache->taskdocuments->where('parent_id', $parent->id)->sortBy('version');
                                        @endphp
                                        {{-- Afficher V1 (le parent lui-même) --}}
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <span>v1 - {{ $parent->uploader?->name ?? 'Inconnu' }} - {{ $parent->created_at->format('d/m/Y H:i') }}</span>
                                            <a href="{{ asset('storage/' . $parent->chemin) }}" target="_blank" class="btn btn-sm btn-outline-primary">Télécharger</a>
                                        </div>

                                        {{-- Afficher les autres versions si elles existent --}}
                                        @foreach($versions as $doc)
                                            <div class="d-flex justify-content-between align-items-center mt-1 ms-3">
                                                <span>v{{ $doc->version }} - {{ $doc->uploader?->name ?? 'Inconnu' }} - <strong>{{ $doc->nom_original }}</strong> -
                                                         {{ $doc->created_at->format('d/m/Y H:i') }}</span>
                                                <a href="{{ asset('storage/' . $doc->chemin) }}" target="_blank" class="btn btn-sm btn-outline-primary">Télécharger</a>
                                            </div>
                                        @endforeach
                                    </li>
                                @endforeach
                            </ul>


                            <form action="{{ route('chef_equipe.projets.taches.uploadDocument', [$projet, $tache]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="document" class="form-label">Ajouter / Mettre à jour un document</label>
                                    <input type="file" name="document" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">Parent (laisser vide si nouveau document)</label>
                                    <select name="parent_id" class="form-select">
                                        <option value="">Nouveau document</option>
                                        @foreach($tache->taskdocuments->whereNull('parent_id') as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->nom_original }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Ajouter</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach

            @endif
        </div>
    </div>
</div>

<!-- Styles modales custom -->
<style>
.custom-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    
}

.custom-modal-content {
    background-color: white;
    padding: 1rem;
    border-radius: 5px;
    max-width: 800px;
    width: 90%;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
   padding: 1rem;
   color: var(--secondary-color);
}

.custom-modal-close {
    cursor: pointer;
    font-size: 1.5rem;
}

.custom-modal-body {
  padding: 1rem;
  background-color: var(--light-color);
}
</style>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

<script>
$(function() {
    // Table triable
    $("#sortable").sortable({
        handle: ".handle",
        opacity: 0.7,
        helper: "clone",
        update: function(event, ui) {
            const tacheIds = $(this).sortable('toArray', { attribute: 'data-id' });
            $.post("{{ route('chef_equipe.projets.taches.reorder', $projet) }}", {
                _token: "{{ csrf_token() }}",
                taches: tacheIds
            });
        }
    }).disableSelection();
});

// Fonctions modales custom
function openModal(id){
    document.getElementById('documentsModal'+id).style.display = 'flex';
}
function closeModal(id){
    document.getElementById('documentsModal'+id).style.display = 'none';
}
window.onclick = function(event) {
    document.querySelectorAll('.custom-modal').forEach(function(modal){
        if(event.target == modal){
            modal.style.display = 'none';
        }
    });
}
</script>
@endsection
