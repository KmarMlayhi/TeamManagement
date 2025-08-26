<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Kanban - {{ $projet->nom }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/kanbanChef.css') }}">
</head>
<body>
    <div class="kanban-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="fas fa-columns me-2"></i>Tableau Kanban {{ $projet->nom }}
            </h2>
        </div>
    </div>

    <div class="kanban-container-wrapper">
        <div class="kanban-board">
            <div class="kanban-container">
                @foreach($statuts as $statusKey => $statusLabel)
                    <div class="kanban-column">
                        <div class="card h-100 shadow-sm">
                            
                            <div class="card-header card-header-bg-{{ 
                                ['a_faire' => 'bg-secondary', 
                                 'en_cours' => 'primary', 
                                 'termine' => 'success'][$statusKey] 
                            }}">
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
                                </div>
                            </div>
                            <div class="kanban-cards">
                                @if(isset($taches[$statusKey]) && count($taches[$statusKey]) > 0)
                                    @foreach($taches[$statusKey] as $tache)
                                        <div class="kanban-task">
                                            <div class="task-title">
                                                {{ $tache->titre }}
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                @if($tache->priorite)
                                                    <span class="badge priority-badge badge-priority-{{ $tache->priorite }}">
                                                        {{ $priorites[$tache->priorite] ?? 'Priorité' }}
                                                    </span>
                                                @endif
                                                
                                                <span class="badge bg-light text-dark">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    @if($tache->date_fin_prevue)
                                                        {{ $tache->date_fin_prevue->format('d/m/Y') }}
                                                    @else
                                                        Non définie
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="assignee-info d-flex align-items-center">
                                                @if($tache->users->count() > 0)
                                                    @foreach($tache->users as $user)
                                                        <div class="rounded-circle me-1 d-flex align-items-center justify-content-center" 
                                                            style="width: 24px; height: 24px; background-color: #e2e8f0; font-size: 0.75rem; font-weight: bold;"
                                                            title="{{ $user->name }}">
                                                            {{ strtoupper(substr($user->name, 0, 1) . (str_contains($user->name, ' ') ? substr(strrchr($user->name, ' '), 1, 1) : '')) }}
                                                        </div>
                                                    @endforeach

                                                    <div class="assignee-details ms-2">
                                                        @foreach($tache->users as $user)
                                                            <div class="name">{{ $user->name }}</div>
                                                            <div class="email">{{ $user->email }}</div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-muted">Non assigné</div>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
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
</body>
</html>