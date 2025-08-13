<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Kanban Projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --todo-color: #e74c3c;
            --progress-color: #f39c12;
            --done-color: #27ae60;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #333;
            --border-color: #e0e0e0;
        }
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--secondary-color), #1a2530);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .project-info {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        
        .project-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .project-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Kanban Container */
        .kanban-board {
            min-height: 70vh;
        }
        
        .kanban-container {
            display: flex;
            gap: 20px;
            padding: 0 10px 20px;
            min-height: 70vh;
            overflow-x: auto;
        }
        
        .kanban-column {
            flex: 1;
            min-width: 300px;
            background-color: var(--light-bg);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 60vh;
            transition: background-color 0.3s ease;
        }
        
        .kanban-column-header {
            padding: 15px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .kanban-column-todo .kanban-column-header {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--todo-color);
        }
        
        .kanban-column-progress .kanban-column-header {
            background-color: rgba(243, 156, 18, 0.15);
            color: var(--progress-color);
        }
        
        .kanban-column-done .kanban-column-header {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--done-color);
        }
        
        .task-count {
            background-color: rgba(255,255,255,0.8);
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
        }
        
        .kanban-task {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: grab;
            border-left: 4px solid;
            margin-bottom: 10px;
        }
        
        .kanban-task:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .kanban-task.dragging {
            transform: rotate(3deg);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1000 !important;
        }
        
        .kanban-task-todo {
            border-left-color: var(--todo-color);
        }
        
        .kanban-task-progress {
            border-left-color: var(--progress-color);
        }
        
        .kanban-task-done {
            border-left-color: var(--done-color);
        }
        
        .task-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 8px;
        }
        
        .task-description {
            font-size: 0.85rem;
            line-height: 1.4;
            color: #666;
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
        
        .assignee-details .role {
            font-size: 0.8rem;
            color: #888;
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 0.85rem;
            color: #888;
        }
        
        .priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .priority-high {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--todo-color);
        }
        
        .priority-medium {
            background-color: rgba(243, 156, 18, 0.2);
            color: #e67e22;
        }
        
        .priority-low {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--primary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: #aaa;
            font-style: italic;
            background-color: rgba(0,0,0,0.02);
            border-radius: 8px;
            margin: 10px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .kanban-container {
                flex-direction: column;
            }
            
            .kanban-column {
                min-width: 100%;
                min-height: auto;
            }
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stats-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stats-card.todo .number { color: var(--todo-color); }
        .stats-card.progress .number { color: var(--progress-color); }
        .stats-card.done .number { color: var(--done-color); }
        
        .stats-card .label {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0"><i class="fas fa-project-diagram me-2"></i> Suivi des Projets</h1>
                    <p class="mb-0">Tableau Kanban pour le suivi des tâches</p>
                </div>
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Collaborateur&background=random" class="rounded-circle me-2" width="40" height="40">
                    <div>
                        <div class="text-white">Collaborateur</div>
                        <small class="text-white-50">Membre d'équipe</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mb-5">
        <!-- Info projet -->
        <div class="project-info">
            <h2 class="project-title">Projet: Refonte de l'application web</h2>
            <div class="project-meta">
                <span class="me-3"><i class="fas fa-calendar me-1"></i> Date de début: 15 Mai 2023</span>
                <span class="me-3"><i class="fas fa-calendar-check me-1"></i> Date de fin estimée: 30 Août 2023</span>
                <span><i class="fas fa-users me-1"></i> Équipe: Développement Frontend</span>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card todo">
                    <i class="fas fa-list-alt fa-2x"></i>
                    <div class="number">8</div>
                    <div class="label">Tâches à faire</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card progress">
                    <i class="fas fa-spinner fa-2x"></i>
                    <div class="number">5</div>
                    <div class="label">En cours</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card done">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <div class="number">12</div>
                    <div class="label">Terminées</div>
                </div>
            </div>
        </div>
        
        <!-- Tableau Kanban -->
        <div class="kanban-board">
            <div class="kanban-container">
                <!-- Colonne À faire -->
                <div class="kanban-column kanban-column-todo">
                    <div class="kanban-column-header">
                        <span><i class="fas fa-list me-2"></i> À faire</span>
                        <span class="task-count">3 tâches</span>
                    </div>
                    <div class="kanban-cards">
                        <div class="kanban-task kanban-task-todo">
                            <div class="task-title">Conception de l'interface utilisateur</div>
                            <div class="task-description">Créer les maquettes pour les écrans principaux</div>
                            <div class="priority priority-high">Haute priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 15 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 3</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Marie+Dupont&background=e74c3c&color=fff" alt="Marie Dupont">
                                <div class="assignee-details">
                                    <div class="name">Marie Dupont</div>
                                    <div class="role">Designer UI/UX</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kanban-task kanban-task-todo">
                            <div class="task-title">Documentation technique</div>
                            <div class="task-description">Rédiger la documentation des API</div>
                            <div class="priority priority-medium">Moyenne priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 20 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 1</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Thomas+Martin&background=3498db&color=fff" alt="Thomas Martin">
                                <div class="assignee-details">
                                    <div class="name">Thomas Martin</div>
                                    <div class="role">Tech Lead</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kanban-task kanban-task-todo">
                            <div class="task-title">Configurer l'environnement de test</div>
                            <div class="task-description">Mettre en place les environnements de test</div>
                            <div class="priority priority-low">Basse priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 25 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 0</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Alex+Petit&background=2c3e50&color=fff" alt="Alex Petit">
                                <div class="assignee-details">
                                    <div class="name">Alex Petit</div>
                                    <div class="role">DevOps</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Colonne En cours -->
                <div class="kanban-column kanban-column-progress">
                    <div class="kanban-column-header">
                        <span><i class="fas fa-spinner me-2"></i> En cours</span>
                        <span class="task-count">4 tâches</span>
                    </div>
                    <div class="kanban-cards">
                        <div class="kanban-task kanban-task-progress">
                            <div class="task-title">Développement API</div>
                            <div class="task-description">Implémenter les endpoints pour les utilisateurs</div>
                            <div class="priority priority-high">Haute priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 10 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 5</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Jean+Petit&background=27ae60&color=fff" alt="Jean Petit">
                                <div class="assignee-details">
                                    <div class="name">Jean Petit</div>
                                    <div class="role">Développeur Backend</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kanban-task kanban-task-progress">
                            <div class="task-title">Intégration du frontend</div>
                            <div class="task-description">Intégrer les maquettes avec React</div>
                            <div class="priority priority-high">Haute priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 12 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 7</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Lucie+Martin&background=f39c12&color=fff" alt="Lucie Martin">
                                <div class="assignee-details">
                                    <div class="name">Lucie Martin</div>
                                    <div class="role">Développeuse Frontend</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kanban-task kanban-task-progress">
                            <div class="task-title">Tests unitaires</div>
                            <div class="task-description">Écrire les tests pour le module d'authentification</div>
                            <div class="priority priority-medium">Moyenne priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 18 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 2</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Pierre+Leroy&background=9b59b6&color=fff" alt="Pierre Leroy">
                                <div class="assignee-details">
                                    <div class="name">Pierre Leroy</div>
                                    <div class="role">QA Engineer</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Colonne Terminé -->
                <div class="kanban-column kanban-column-done">
                    <div class="kanban-column-header">
                        <span><i class="fas fa-check-circle me-2"></i> Terminé</span>
                        <span class="task-count">2 tâches</span>
                    </div>
                    <div class="kanban-cards">
                        <div class="kanban-task kanban-task-done">
                            <div class="task-title">Analyse des besoins</div>
                            <div class="task-description">Réaliser l'analyse fonctionnelle détaillée</div>
                            <div class="priority priority-medium">Moyenne priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 5 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 4</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Sarah+Dubois&background=3498db&color=fff" alt="Sarah Dubois">
                                <div class="assignee-details">
                                    <div class="name">Sarah Dubois</div>
                                    <div class="role">Product Owner</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kanban-task kanban-task-done">
                            <div class="task-title">Base de données</div>
                            <div class="task-description">Modéliser et implémenter le schéma de base de données</div>
                            <div class="priority priority-high">Haute priorité</div>
                            <div class="task-meta">
                                <span><i class="far fa-calendar me-1"></i> 8 Juin</span>
                                <span><i class="far fa-comment me-1"></i> 6</span>
                            </div>
                            <div class="assignee-info">
                                <img src="https://ui-avatars.com/api/?name=Marc+Bernard&background=2c3e50&color=fff" alt="Marc Bernard">
                                <div class="assignee-details">
                                    <div class="name">Marc Bernard</div>
                                    <div class="role">DBA</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulation de fonctionnalité de glisser-déposer
        document.addEventListener('DOMContentLoaded', function() {
            const tasks = document.querySelectorAll('.kanban-task');
            
            tasks.forEach(task => {
                task.addEventListener('dragstart', () => {
                    task.classList.add('dragging');
                });
                
                task.addEventListener('dragend', () => {
                    task.classList.remove('dragging');
                });
            });
            
            // Message pour montrer la fonctionnalité
            setTimeout(() => {
                alert("Bienvenue sur le tableau Kanban !\n\nCette interface permet de suivre l'avancement des tâches de votre projet.\n\nVous pouvez glisser-déposer les tâches entre les colonnes pour mettre à jour leur statut.");
            }, 1000);
        });
    </script>
</body>
</html>