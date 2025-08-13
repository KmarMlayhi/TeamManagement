<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Kanban - {{ $projet->nom }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables et styles de base */
        :root {
            --primary-color: #0056b3;
            --primary-hover: #004494;
            --secondary-color: #003366;
            --accent-color: #e30613;
            --light-gray: #f8f9fa;
            --light-bg: #f8f9fa;
            --border-color: #e0e6ed;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --text-color: #212529;
            --text-muted: #6c757d;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        /* Kanban Container */
        .kanban-board {
            min-height: 70vh;
        }
        
        .kanban-container {
            display: flex;
            gap: 8px;
            padding: 0 10px 20px;
            min-height: 70vh;
            overflow-x: auto;
        }
        .bg-secondary {
        background-color: #475569 !important;
        }
.card-header.bg-secondary { background-color: #475569 !important; }
        .kanban-column {
            min-height: 60vh;
            transition: background-color 0.3s ease;
            min-width: 300px;
            display: flex;
            flex-direction: column;
        }
        
        .kanban-column-card {
            height: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        /* CORRECTION IMPORTANTE ICI - Utilisation des classes Bootstrap pour les couleurs */
        .card-header-bg-secondary {
            background-color: var(--secondary-color) !important;
            color: white !important;
        }
        
        .card-header-bg-primary {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .card-header-bg-success {
            background-color: var(--success-color) !important;
            color: white !important;
        }
        
        .task-count {
            background-color: rgba(255,255,255,0.3);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .kanban-cards {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            min-height: 200px;
            border-radius:8px; 
            background-color: white;
        }
        
        /* Styles de tâche Kanban */
        .kanban-task {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
        }
        
        .task-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .task-title a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .task-title a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        
        .task-description {
            font-size: 0.85rem;
            line-height: 1.4;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        
        .assignee-info {
            display: flex;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }
        
        .assignee-info img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            transition: transform 0.2s ease;
        }
        
        .assignee-info img:hover {
            transform: scale(1.2);
        }
        
        .assignee-details .name {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .assignee-details .email {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        /* Styles pour les badges de priorité */
        .priority-badge {
            font-size: 0.75rem;
        }
        
        .badge-priority-basse {
            background-color: var(--success-color);
            color: white;
        }
        
        .badge-priority-moyenne {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-priority-haute {
            background-color: var(--danger-color);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: var(--text-muted);
            background-color: rgba(0,0,0,0.02);
            border-radius: 8px;
            margin: 10px;
        }
        
        /* Statistiques comme avant */
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            height: 100%;
        }
        
        .stats-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stats-card.todo .number { color: var(--secondary-color); }
        .stats-card.progress .number { color: var(--primary-color); }
        .stats-card.done .number { color: var(--success-color); }
        
        .stats-card .label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .kanban-column {
                min-height: 40vh;
                min-width: 280px;
            }
            
            .kanban-container {
                flex-wrap: nowrap;
                overflow-x: auto;
            }
        }
        
        .kanban-container-wrapper {
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        /* Améliorations pour le design */
        .kanban-header {
            background: linear-gradient(135deg, var(--secondary-color), #002244);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Titre + bouton retour -->
    <div class="kanban-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="fas fa-columns me-2"></i>Tableau Kanban : {{ $projet->nom }}
            </h2>
        </div>
    </div>
    <!-- Tableau Kanban avec colonnes colorées -->
    <div class="kanban-container-wrapper">
        <div class="kanban-board">
            <div class="kanban-container">
                @foreach($statuts as $statusKey => $statusLabel)
                    <div class="kanban-column">
                        <div class="card h-100 shadow-sm">
                            <!-- CORRECTION APPLIQUÉE ICI - Classes de couleur dynamiques -->
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
                                            
                                            <div class="assignee-info">
                                                @if($tache->affecteA)
                                                    <div class="rounded-circle me-1 d-flex align-items-center justify-content-center" 
                                                        style="width: 24px; height: 24px; background-color: #e2e8f0; font-size: 0.75rem; font-weight: bold;"
                                                        title="{{ $tache->affecteA->name }}">
                                                        {{ strtoupper(substr($tache->affecteA->name, 0, 1) . substr(strrchr($tache->affecteA->name, ' '), 1, 1)) }}
                                                    </div>
                                                @endif


                                                <div class="assignee-details">
                                                    <div class="name">{{ $tache->affecteA->name ?? 'Non assigné' }}</div>
                                                    <div class="email">{{ $tache->affecteA->email ?? '' }}</div>
                                                </div>
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