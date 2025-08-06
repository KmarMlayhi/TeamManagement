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
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($taches->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>Aucune tâche créée pour ce projet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-taches">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center"><i class="fas fa-arrows-alt"></i></th>
                                <th>Titre</th>
                                <th>Assigné à</th>
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
                                    @if($tache->affecteA)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                {{ substr($tache->affecteA->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $tache->affecteA->name }}</div>
                                                <small class="text-muted">{{ $tache->affecteA->fonction }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<script>
$(function() {
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
    
    // Tooltips
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>
@endsection