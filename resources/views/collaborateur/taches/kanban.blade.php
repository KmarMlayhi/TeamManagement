@extends('layouts.collaborateur')

@section('title', 'Tableau Kanban: ' . $projet->nom)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kanban.css') }}">
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
@endsection

@section('content')
  <!-- Breadcrumb -->
    <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.home') }}">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.equipes.index') }}">Mes équipes</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.equipes.show', $equipe) }}">
                        <i class="fas fa-users me-1"></i>{{ $equipe->nom }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('collaborateur.projets.taches', ['projet' => $projet, 'equipe_id' => $equipe->id]) }}">
                        <i class="fas fa-tasks me-1"></i>Mes tâches
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-columns me-1"></i>Vue Kanban
                </li>
            </ol>
        </nav>
    </div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-columns me-2"></i>Tableau Kanban: {{ $projet->nom }}
        </h3>
        <div>
            <a href="{{ route('collaborateur.projets.taches', ['projet' => $projet->id, 'equipe_id' => $equipe->id]) }}" class="btn btn-outline-primary">
    <i class="fas fa-list me-1"></i> Vue Liste
</a>

        </div>
    </div>

    <div class="kanban-board">
        <div class="row g-3">
            @foreach($statuts as $statusKey => $statusLabel)
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm">
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
                        <div class="card mb-3 kanban-task" data-id="{{ $tache->id }}">
                            <div class="card-body p-3">
                                <h6 class="mb-2">
                                    <a href="{{ route('chef_equipe.projets.taches.edit', [$projet, $tache]) }}" 
                                       class="text-decoration-none">
                                       {{ $tache->titre }}
                                    </a>
                                </h6>
                                @if($tache->description)
                                    <p class="small text-muted mb-2">
                                        {{ Str::limit($tache->description, 70) }}
                                    </p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-{{ [
                                        'basse' => 'secondary', 
                                        'moyenne' => 'primary', 
                                        'haute' => 'danger'
                                    ][$tache->priorite] }}">
                                        {{ $priorites[$tache->priorite] }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $tache->date_fin_prevue->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if(empty($taches[$statusKey]))
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tasks fa-2x mb-2"></i>
                            <p>Aucune tâche dans cette colonne</p>
                         
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
            
            // Autoriser toutes les transitions
            // (suppression des restrictions précédentes)
            
            // Mettre à jour visuellement
            task.detach().css({'top': 0, 'left': 0}).appendTo($(this));
            
            // Mettre à jour en base de données
            $.post("{{ route('collaborateur.taches.updateStatutKanban') }}", {
                _token: "{{ csrf_token() }}",
                tache_id: taskId,   // Utilisez 'tache_id' au lieu de 'tache'
                statut: newStatus
            }).fail(function() {
                alert('Une erreur est survenue lors de la mise à jour du statut');
                // Revert visual change on error
                task.detach().appendTo($('.kanban-column[data-status="' + oldStatus + '"]'));
            });
        }
    });
});
</script>
@endsection