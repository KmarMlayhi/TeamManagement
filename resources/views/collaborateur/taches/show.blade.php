@extends('layouts.collaborateur')

@section('title', $tache->titre)

@section('content')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .tache-detail-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .tache-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary-color);
    }
    .detail-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.06);
        border: none;
        margin-bottom: 30px;
    }
    .detail-card .card-header {
        background-color: #f8f9fa;
        padding: 20px 25px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 600;
        font-size: 1.25rem;
        color: var(--secondary-color);
    }
    .detail-card .card-body {
        padding: 30px;
    }
    .tache-info-list .list-group-item {
        padding: 15px 20px;
        border: none;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
    }
    .tache-info-list .list-group-item:last-child {
        border-bottom: none;
    }
    .info-label {
        flex: 0 0 40%;
        font-weight: 500;
        color: #495057;
    }
    .info-value {
        flex: 0 0 60%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .priority-badge, .status-badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .priority-high {
        background: linear-gradient(135deg, #ff6b6b 0%, #ff2b2b 100%);
        color: white;
    }
    .priority-medium {
        background: linear-gradient(135deg, #ffd166 0%, #ffb700 100%);
        color: #343a40;
    }
    .priority-low {
        background: linear-gradient(135deg, #06d6a0 0%, #05a678 100%);
        color: white;
    }
    .status-todo {
        background: linear-gradient(135deg, #e9ecef 0%, #ced4da 100%);
        color: #495057;
    }
    .status-in-progress {
        background: linear-gradient(135deg, #4cc9f0 0%, #3a86ff 100%);
        color: white;
    }
    .status-done {
        background: linear-gradient(135deg, #80ed99 0%, #38b000 100%);
        color: white;
    }
    .description-content {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        line-height: 1.7;
        font-size: 1.05rem;
        color: #495057;
        border-left: 3px solid var(--primary-color);
    }
    .tabs-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    }
    .tabs-card .card-header {
        background-color: #f8f9fa;
        padding: 0;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .tabs-card .nav-tabs .nav-link {
        padding: 18px 25px;
        font-weight: 500;
        color: #6c757d;
        border: none;
        border-radius: 0;
    }
    .tabs-card .nav-tabs .nav-link.active {
        color: var(--primary-color);
        background-color: white;
        border-bottom: 3px solid var(--primary-color);
    }
    .tabs-card .tab-content {
        padding: 30px;
    }
    .feature-coming {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
    }
    .feature-icon {
        font-size: 3.5rem;
        color: #adb5bd;
        margin-bottom: 20px;
    }
    .date-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 15px;
        background-color: rgba(0, 123, 255, 0.1);
        border-radius: 8px;
        color: var(--primary-color);
        font-weight: 500;
    }
     .date-infos{
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 15px;
        background-color: rgba(245, 35, 35, 0.856);
        border-radius: 8px;
        color: white;
        font-weight: 500;
    }
    .date-icon {
        font-size: 1.2rem;
    }
    /* Styles pour la version compacte */
    .status-selector {
        display: flex;
        gap: 8px;
    }
    .status-option {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .status-option:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .status-option.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    .status-option[data-value="a_faire"] {
        background-color: #e9ecef;
        color: #495057;
    }
    .status-option[data-value="en_cours"] {
        background-color: #cfe2ff;
        color: #084298;
    }
    .status-option[data-value="termine"] {
        background-color: #d1e7dd;
        color: #0a3622;
    }
    .status-change-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
}
.assignee-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.dropdown-toggle::after {
    display: none;
}

.comment-content {
    white-space: pre-line;
}
</style>

<div class="tache-detail-container">
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
                    <a href="{{ route('collaborateur.projets.taches', ['projet' => $tache->projet, 'equipe_id' => $equipe->id]) }}">
                        <i class="fas fa-tasks me-1"></i>Mes tâches
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ \Illuminate\Support\Str::limit($tache->titre, 25) }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="tache-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="mb-2" style="color: var(--secondary-color);">
                    <i class="fas fa-tasks me-2"></i>{{ $tache->titre }}
                </h3>

                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="date-info">
                        <i class="fas fa-play-circle date-icon"></i>
                        <span>Début: {{ $tache->date_debut->format('d/m/Y') }}</span>
                    </div>
                    <div class="date-infos " >
                        <i class="fas fa-exclamation-circle date-icon"></i>
                        <span>Échéance: {{ $tache->date_fin_prevue->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-primary fs-6 py-2 px-3">
                    <i class="fas fa-project-diagram me-1"></i>Projet: {{ $tache->projet->nom }}
                </span>
                <a href="{{ route('collaborateur.projets.taches.kanban', ['projet' => $tache->projet, 'equipe_id' => $equipe->id]) }}" 
                            class="btn btn-outline-primary me-2">
                            <i class="fas fa-columns me-1"></i> Vue Kanban
                </a> 
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-file-alt me-2"></i>Description et commentaires
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <h5 class="mb-3"><i class="fas fa-align-left me-2"></i>Description</h5>
                        <div class="description-content">
                            {{ $tache->description ?: "Aucune description fournie" }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Détails de la tâche
                </div>
                <div class="card-body">
                    <ul class="list-group tache-info-list">
                        <li class="list-group-item">
                            <div class="info-label">Priorité</div>
                            <div class="info-value">
                                @if($tache->priorite === 'haute')
                                    <span class="priority-badge priority-high">
                                        <i class="fas fa-exclamation-circle"></i>Haute priorité
                                    </span>
                                @elseif($tache->priorite === 'moyenne')
                                    <span class="priority-badge priority-medium">
                                        <i class="fas fa-flag"></i>Moyenne priorité
                                    </span>
                                @else
                                    <span class="priority-badge priority-low">
                                        <i class="fas fa-flag"></i>Basse priorité
                                    </span>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="info-label">Statut actuel</div>
                            <div class="info-value">
                                @if($tache->statut === 'a_faire')
                                    <span class="status-badge status-todo">
                                        <i class="fas fa-circle-notch me-1"></i>À faire
                                    </span>
                                @elseif($tache->statut === 'en_cours')
                                    <span class="status-badge status-in-progress">
                                        <i class="fas fa-spinner me-1"></i>En cours
                                    </span>
                                @else
                                    <span class="status-badge status-done">
                                        <i class="fas fa-check-circle me-1"></i>Terminé
                                    </span>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="info-label">Changer le statut</div>
                            <div class="info-value">
                                <form method="POST" action="{{ route('collaborateur.taches.updateStatut', $tache) }}" 
                                    class="status-form" id="statusForm">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="statut" id="selectedStatus" value="{{ $tache->statut }}">
                                    
                                    <button type="submit" class="btn btn-primary btn-sm status-change-btn mb-2">
                                        <i class="fas fa-sync-alt me-1"></i>
                                        Mettre à jour
                                    </button>
                                    
                                    <div class="status-selector d-flex gap-2" id="statusSelector">
                                        <div class="status-option @if($tache->statut === 'a_faire') active @endif" 
                                            data-value="a_faire" title="À faire">
                                            <i class="fas fa-circle-notch"></i>
                                        </div>
                                        <div class="status-option @if($tache->statut === 'en_cours') active @endif" 
                                            data-value="en_cours" title="En cours">
                                            <i class="fas fa-spinner"></i>
                                        </div>
                                        <div class="status-option @if($tache->statut === 'termine') active @endif" 
                                            data-value="termine" title="Terminé">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="info-label">Assignée par</div>
                            <div class="info-value">
                                <span class="fw-medium">{{ $tache->createur->name ?? 'Inconnu' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="info-label">Date de création</div>
                            <div class="info-value">
                                {{ $tache->created_at->format('d/m/Y') }}
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="info-label">Dernière mise à jour</div>
                            <div class="info-value">
                                {{ $tache->updated_at->format('d/m/Y') }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Section pour les commentaires et soumission de travail -->
    <div class="tabs-card">
        <div class="card-header">
            <ul class="nav nav-tabs" id="tacheTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="soumission-tab" data-bs-toggle="tab" data-bs-target="#soumission" type="button" role="tab">
                        <i class="fas fa-file-upload me-2"></i>Soumission du travail
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="commentaires-tab" data-bs-toggle="tab" data-bs-target="#commentaires" type="button" role="tab">
                        <i class="fas fa-comments me-2"></i>Commentaires
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">
                        <i class="fas fa-history me-2"></i>Historique
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
    <div class="tab-content" id="tacheTabsContent">
        <div class="tab-pane fade show active" id="commentaires" role="tabpanel">
            <div class="feature-coming">
                <div id="commentaires-container">
                    <div id="liste-commentaires"></div>
                </div>

                    @if(Auth::id() === $tache->created_by || Auth::id() === $tache->affecte_a)
                    <form id="form-commentaire">
                        @csrf
                        <textarea name="contenu" class="form-control mb-2" placeholder="Écrire un commentaire..." required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
                    </form>
                    @endif
                </div>
            </div>
           
        </div>

        <div class="tab-pane fade" id="soumission" role="tabpanel">
            <div class="feature-coming">
                <div class="feature-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <h4 class="mb-3">Fonctionnalité à venir</h4>
                <p class="text-muted mb-4">
                    Bientôt, vous pourrez soumettre votre travail directement depuis cette page 
                    et suivre l'avancement de votre tâche.
                </p>
            </div>
        </div>

        <div class="tab-pane fade" id="historique" role="tabpanel">
            <div class="feature-coming">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h4 class="mb-3">Fonctionnalité à venir</h4>
                <p class="text-muted mb-4">
                    L'historique des modifications vous montrera bientôt toutes les actions 
                    effectuées sur cette tâche depuis sa création.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tacheId = {{ $tache->id }};
    const userId = {{ Auth::id() }};
    const isAdmin = userId === {{ $tache->created_by }};
    const listeCommentaires = document.getElementById('liste-commentaires');
    const formCommentaire = document.getElementById('form-commentaire');
    const textarea = formCommentaire ? formCommentaire.querySelector('textarea[name="contenu"]') : null;

    function formatDate(dateString) {
        const options = { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit', 
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleString('fr-FR', options);
    }

    async function loadCommentaires() {
        try {
            const response = await fetch(`/collaborateur/taches/${tacheId}/commentaires`);
            if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
            const data = await response.json();

            if (!data.success || !Array.isArray(data.commentaires)) {
                listeCommentaires.innerHTML = `<div class="alert alert-info">Aucun commentaire pour l'instant.</div>`;
                return;
            }

            const commentaires = data.commentaires;
            if (commentaires.length === 0) {
                listeCommentaires.innerHTML = `<div class="alert alert-info">Aucun commentaire pour l'instant.</div>`;
                return;
            }

            listeCommentaires.innerHTML = commentaires.map(c => {
                const auteurName = c.auteur?.name || 'Utilisateur inconnu';
                const destinataireName = c.destinataire?.name || 'Toute l\'équipe';
                const auteurInitial = auteurName.charAt(0).toUpperCase();
                const isAuthor = c.auteur_id === userId;
                const canEdit = isAuthor || isAdmin;
                const editTime = c.edited_at ? `<small class="text-muted d-block mt-1">Modifié le ${formatDate(c.edited_at)}</small>` : '';

                return `
                    <div class="card mb-3" data-comment-id="${c.id}">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="assignee-avatar me-2" title="${auteurName}">
                                    ${auteurInitial}
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${auteurName}</strong>
                                    <span class="text-muted small">→ ${destinataireName}</span>
                                </div>
                                <small class="text-muted">${formatDate(c.created_at)}</small>
                                ${canEdit ? `
                                <div class="dropdown ms-2">
                                    <button class="btn btn-sm btn-link text-muted dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        ${isAuthor ? `
                                        <li>
                                            <button class="dropdown-item edit-comment" 
                                                    data-comment-id="${c.id}">
                                                <i class="fas fa-edit me-2"></i>Modifier
                                            </button>
                                        </li>` : ''}
                                        <li>
                                            <button class="dropdown-item delete-comment text-danger"
                                                    data-comment-id="${c.id}">
                                                <i class="fas fa-trash me-2"></i>Supprimer
                                            </button>
                                        </li>
                                    </ul>
                                </div>` : ''}
                            </div>
                            <p class="mb-0 comment-content">${c.contenu}</p>
                            ${editTime}
                        </div>
                    </div>
                `;
            }).join('');

            setupCommentActions();

        } catch (error) {
            console.error('Erreur:', error);
            listeCommentaires.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        }
    }

    function setupCommentActions() {
        // Suppression
        document.querySelectorAll('.delete-comment').forEach(btn => {
            btn.addEventListener('click', async function() {
                const commentId = this.dataset.commentId;
                if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) return;

                try {
                    const response = await fetch(`/collaborateur/taches/${tacheId}/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('Erreur lors de la suppression');
                    await loadCommentaires();
                } catch (error) {
                    console.error(error);
                    alert(error.message);
                }
            });
        });

        // Modification
        document.querySelectorAll('.edit-comment').forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
                const content = commentCard.querySelector('.comment-content').textContent;

                commentCard.innerHTML = `
                    <div class="card-body">
                        <form class="edit-comment-form" data-comment-id="${commentId}">
                            <textarea class="form-control mb-2" required>${content}</textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary cancel-edit">
                                    Annuler
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                `;

                commentCard.querySelector('.cancel-edit').addEventListener('click', async () => {
                    await loadCommentaires();
                });

                commentCard.querySelector('.edit-comment-form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const newContent = e.target.querySelector('textarea').value.trim();
                    if (!newContent) return alert('Le commentaire ne peut pas être vide');

                    try {
                        const response = await fetch(`/collaborateur/taches/${tacheId}/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ contenu: newContent })
                        });
                        if (!response.ok) throw new Error('Erreur lors de la modification');
                        await loadCommentaires();
                    } catch (error) {
                        console.error(error);
                        alert(error.message);
                    }
                });
            });
        });
    }

    // Ajouter un nouveau commentaire
    if(formCommentaire) {
        formCommentaire.addEventListener('submit', async function(e) {
            e.preventDefault();
            const content = textarea.value.trim();
            if (!content) return alert('Veuillez écrire un commentaire');

            try {
                const response = await fetch(`/collaborateur/taches/${tacheId}/commentaires`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ contenu: content })
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Erreur serveur');
                }
                textarea.value = '';
                await loadCommentaires();
            } catch (error) {
                console.error('Erreur:', error);
                alert(error.message || 'Erreur lors de l\'envoi du commentaire');
            }
        });
    }

    loadCommentaires();
});

</script>

@endsection