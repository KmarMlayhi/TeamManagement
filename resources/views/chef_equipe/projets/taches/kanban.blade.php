@extends('layouts.chef')

@section('title', 'Tableau Kanban: ' . $projet->nom)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/kanban.css') }}">
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
@endsection

@section('content')
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.projets.index') }}">Projets</a></li>
            <li class="breadcrumb-item active">Kanban</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-columns me-2"></i>Tableau Kanban:  {{ $projet->nom }}
        </h3>
        <div>
            <a href="{{ route('chef_equipe.projets.taches.create', $projet) }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i> Nouvelle tâche
            </a>
            <a href="{{ route('chef_equipe.projets.taches.index', $projet) }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i> Vue Liste
            </a>
        </div>
    </div>

    <div class="kanban-board">
        <div class="row g-3">
            @foreach($statuts as $statusKey => $statusLabel)
            <div class="col-lg-4">
                <div class="card  h-100">
                    <div class="card-header bg-{{ [
                        'a_faire' => 'secondary', 
                        'en_cours' => 'primary', 
                        'termine' => 'success'
                    ][$statusKey] }} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-{{ [
                                    'a_faire' => 'clock', 
                                    'en_cours' => 'spinner', 
                                    'termine' => 'check-circle'
                                ][$statusKey] }} me-2"></i>
                                {{ $statusLabel }}
                                <span class="badge bg-white text-{{ [
                                    'a_faire' => 'secondary', 
                                    'en_cours' => 'primary', 
                                    'termine' => 'success'
                                ][$statusKey] }} ms-2">
                                    {{ count($taches[$statusKey] ?? []) }}
                                </span>
                            </h5>
                            @if($statusKey === 'a_faire')
                                <a href="{{ route('chef_equipe.projets.taches.create', ['projet' => $projet, 'statut' => $statusKey]) }}" 
                                   class="btn btn-sm btn-light" 
                                   title="Ajouter une tâche">
                                   <i class="fas fa-plus"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body kanban-column p-2" data-status="{{ $statusKey }}">
                        @foreach($taches[$statusKey] ?? [] as $tache)
                        <div class="card mb-3 kanban-task shadow-sm" data-id="{{ $tache->id }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 task-title">
                                        <a href="{{ route('chef_equipe.projets.taches.edit', [$projet, $tache]) }}" 
                                           class="text-decoration-none">
                                           {{ $tache->titre }}
                                        </a>
                                    </h6>
                                    <span class="badge bg-{{ [
                                        'basse' => 'secondary', 
                                        'moyenne' => 'primary', 
                                        'haute' => 'danger'
                                    ][$tache->priorite] }} rounded-pill">
                                        {{ $priorites[$tache->priorite] }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="assignee-info">
                                        @if($tache->affecteA)
                                        <img src="{{ $tache->affecteA->photo_url ?? asset('images/default-avatar.png') }}" 
                                             alt="{{ $tache->affecteA->name }}" 
                                             class="rounded-circle me-1" 
                                             width="24" 
                                             height="24"
                                             title="{{ $tache->affecteA->name }}">
                                        @else
                                        <span class="text-muted small">Non assigné</span>
                                        @endif
                                    </div>
                                    
                                    <div class="task-meta">
                                        <span class="badge bg-light text-dark">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $tache->date_fin_prevue->format('d/m/Y') }}
                                        </span>
                                        {{-- <span class="badge bg-{{ now()->diffInDays($tache->date_fin_prevue, false) < 0 ? 'danger' : (now()->diffInDays($tache->date_fin_prevue, false) < 3 ? 'warning' : 'light') }} text-dark ms-1">
                                            J-{{ now()->diffInDays($tache->date_fin_prevue, false) }}
                                        </span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        @if(empty($taches[$statusKey]))
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tasks fa-2x mb-2"></i>
                            <p>Aucune tâche dans cette colonne</p>
                            @if($statusKey === 'a_faire')
                            <a href="{{ route('chef_equipe.projets.taches.create', ['projet' => $projet, 'statut' => $statusKey]) }}" 
                               class="btn btn-sm btn-outline-primary">
                               <i class="fas fa-plus me-1"></i> Ajouter une tâche
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function() {
    $('.kanban-task').draggable({
        revert: 'invalid',
        zIndex: 100,
        cursor: 'move',
        containment: '.kanban-board',
        start: function() {
            $(this).addClass('dragging');
        },
        stop: function() {
            $(this).removeClass('dragging');
        }
    });
    
    $('.kanban-column').droppable({
        accept: '.kanban-task',
        hoverClass: 'bg-light',
        tolerance: 'pointer',
        drop: function(event, ui) {
            const task = ui.draggable;
            const oldStatus = task.parent().data('status');
            const newStatus = $(this).data('status');
            const taskId = task.data('id');
            
            // Validation des transitions
            if (oldStatus === 'termine' && newStatus !== 'termine') {
                alert('Une tâche terminée ne peut pas être déplacée vers une autre colonne');
                return;
            }
            
            if (oldStatus === 'a_faire' && newStatus === 'termine') {
                if (!confirm('Voulez-vous vraiment marquer cette tâche comme terminée sans passer par "En cours" ?')) {
                    return;
                }
            }
            
            // Mettre à jour visuellement
            task.detach().css({'top': 0, 'left': 0}).appendTo($(this));
            
            // Mettre à jour en base de données
            $.post("{{ route('chef_equipe.projets.taches.update-status', $projet) }}", {
                _token: "{{ csrf_token() }}",
                tache_id: taskId,
                statut: newStatus
            }).fail(function() {
                alert('Une erreur est survenue lors de la mise à jour du statut');
            });
        }
    });
    
    // Empêcher le glisser-déposer vers des colonnes non autorisées
    $('.kanban-column[data-status="termine"]').droppable('option', 'accept', function(draggable) {
        return draggable.parent().data('status') !== 'termine';
    });
});
</script>
@endsection