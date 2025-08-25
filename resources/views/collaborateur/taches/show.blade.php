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
                    <i class="fas fa-file-alt me-2"></i>Description et Documents
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <h5 class="mb-3"><i class="fas fa-align-left me-2"></i>Description</h5>
                        <div class="description-content">
                            {{ $tache->description ?: "Aucune description fournie" }}
                        </div> <br>
                        <div>
                        @if($tache->taskdocuments->isNotEmpty())
                        <h5 class="mb-3"><i class="fa fa-folder-open"></i> Documents partagés </h5>
                            <ul class="list-group">
                                @foreach($tache->taskdocuments as $doc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ asset('storage/' . $doc->chemin) }}" target="_blank">
                                            <i class="fas fa-file-alt me-2"></i>{{ $doc->nom_original}} 
                                        </a>
                                        <span class="badge bg-secondary">{{ $doc->type }}</span>
                                        <span>{{ $doc->uploader ? $doc->uploader->name : 'Inconnu' }}</span>
                                    </li>
                                @endforeach
                            </ul> <br>
                        <div class="alert alert-warning mb-3" role="alert" style="font-size: 0.9rem;">
                            ⚠️ Les documents partagés ici concernent uniquement une tâche spécifique pas toute l'équipe.  <br>
                            ⚠️ Pour partager des documents avec tous les collaborateurs de ce projet, merci de rejoindre la discussion générale du projet. 
                        </div>
                        @endif
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
                                <form method="POST" action="{{ route('collaborateur.taches.updateStatut', $tache) }}" class="status-form" id="statusForm">
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
                    <i class="fas fa-file-upload me-2"></i> Documents à partager 
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="commentaires-tab" data-bs-toggle="tab" data-bs-target="#commentaires" type="button" role="tab">
                    <i class="fas fa-comments me-2"></i> Commentaires pour le chef d'équipe
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="tacheTabsContent">
            <!-- Documents collaborateur -->
            <div class="tab-pane fade" id="soumission" role="tabpanel">
                <div class="collab-documents mt-3">
                    <h5><i class="fas fa-upload me-2"></i> Vos documents</h5>
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="document" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent (laisser vide si nouveau document)</label>
                            <select name="parent_id" class="form-select" id="parentSelect">
                                <option value="">Nouveau document</option>
                                <!-- Les options seront chargées dynamiquement par JavaScript -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Uploader</button>
                    </form>
                    
                    <!-- Conteneur pour les documents de l'utilisateur courant -->
                    <div class="mt-4">
                        <h6><i class="fas fa-list me-2"></i>Mes documents soumis</h6>
                        <ul id="userDocumentsList" class="list-group mt-2">
                            <!-- Les documents de l'utilisateur seront chargés ici par JavaScript -->
                            <li class="list-group-item text-muted">Chargement des documents...</li>
                        </ul>
                    </div>
                </div>
            </div>
                        <!-- Commentaires -->
            <div class="tab-pane fade show active" id="commentaires" role="tabpanel">
                <div id="commentaires-container">
                    <div id="liste-commentaires"></div>
                </div>

                @if(Auth::id() === $tache->created_by || $tache->users->contains(Auth::user()))
                    <form id="form-commentaire">
                        @csrf
                        <textarea name="contenu" class="form-control mb-2" placeholder="Écrire un commentaire..." required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
                    </form>
                @endif
            </div>
       </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tacheId = {{ $tache->id }};
    const userId = {{ Auth::id() }};
    const isAdmin = userId === {{ $tache->created_by }};

    // ---------- COMMENTAIRES ----------
    const listeCommentaires = document.getElementById('liste-commentaires');
    const formCommentaire = document.getElementById('form-commentaire');
    const textarea = formCommentaire?.querySelector('textarea[name="contenu"]');

    function formatDate(dateString) {
        const options = { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' };
        return new Date(dateString).toLocaleString('fr-FR', options);
    }

    async function loadCommentaires() {
        try {
            const res = await fetch(`/collaborateur/taches/${tacheId}/commentaires`);
            const data = await res.json();

            if (!data.success || !Array.isArray(data.commentaires)) {
                listeCommentaires.innerHTML = `<div class="alert alert-info">Aucun commentaire pour l'instant.</div>`;
                return;
            }

            listeCommentaires.innerHTML = data.commentaires.map(c => {
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
                                <div class="assignee-avatar me-2" title="${auteurName}">${auteurInitial}</div>
                                <div class="flex-grow-1"><strong>${auteurName}</strong> <span class="text-muted small">→ ${destinataireName}</span></div>
                                <small class="text-muted">${formatDate(c.created_at)}</small>
                                ${canEdit ? `
                                <div class="dropdown ms-2">
                                    <button class="btn btn-sm btn-link text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        ${isAuthor ? `<li><button class="dropdown-item edit-comment" data-comment-id="${c.id}"><i class="fas fa-edit me-2"></i>Modifier</button></li>` : ''}
                                        <li><button class="dropdown-item delete-comment text-danger" data-comment-id="${c.id}"><i class="fas fa-trash me-2"></i>Supprimer</button></li>
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
        } catch(err) {
            console.error(err);
            listeCommentaires.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
        }
    }

    function setupCommentActions() {
        document.querySelectorAll('.delete-comment').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.commentId;
                if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) return;
                try {
                    await fetch(`/collaborateur/taches/${tacheId}/${id}`, { 
                        method:'DELETE', 
                        headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' } 
                    });
                    await loadCommentaires();
                } catch(err) { console.error(err); alert(err.message); }
            });
        });

        document.querySelectorAll('.edit-comment').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.commentId;
                const card = document.querySelector(`[data-comment-id="${id}"]`);
                const content = card.querySelector('.comment-content').textContent;

                card.innerHTML = `
                    <div class="card-body">
                        <form class="edit-comment-form" data-comment-id="${id}">
                            <textarea class="form-control mb-2" required>${content}</textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary cancel-edit">Annuler</button>
                                <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                `;

                card.querySelector('.cancel-edit').addEventListener('click', async () => { await loadCommentaires(); });
                card.querySelector('.edit-comment-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const val = e.target.querySelector('textarea').value.trim();
                    if(!val) return alert('Le commentaire ne peut pas être vide');
                    try{
                        await fetch(`/collaborateur/taches/${tacheId}/${id}`, {
                            method:'PUT',
                            headers:{
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                                'Accept':'application/json'
                            },
                            body: JSON.stringify({ contenu: val })
                        });
                        await loadCommentaires();
                    } catch(err){ console.error(err); alert(err.message); }
                });
            });
        });
    }

    if(formCommentaire){
        formCommentaire.addEventListener('submit', async e => {
            e.preventDefault();
            const val = textarea.value.trim();
            if(!val) return alert('Veuillez écrire un commentaire');
            try{
                await fetch(`/collaborateur/taches/${tacheId}/commentaires`, {
                    method:'POST',
                    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body: JSON.stringify({ contenu: val })
                });
                textarea.value='';
                await loadCommentaires();
            }catch(err){ console.error(err); alert(err.message||'Erreur serveur'); }
        });
    }

    loadCommentaires();

    // ---------- DOCUMENTS COLLABORATEUR ----------
    const userDocumentsList = document.getElementById('userDocumentsList');
    const uploadForm = document.getElementById('uploadForm');
    const parentSelect = document.getElementById('parentSelect');

    async function loadUserDocuments() {
        try {
            const res = await fetch(`/collaborateur/taches/${tacheId}/documents`);
            const data = await res.json();

            if (!userDocumentsList) return;

            if (data.success && Array.isArray(data.documents) && data.documents.length) {
                const userDocuments = [];

                data.documents.forEach(doc => {
                    // Ajouter le document principal si c'est l'utilisateur
                    if (doc.uploader_id === userId) {
                        userDocuments.push({
                            id: doc.id,
                            nom_original: doc.nom_original,
                            version: doc.version,
                            uploader: doc.uploader,
                            created_at: doc.created_at,
                            url: doc.url,
                            is_version: false
                        });
                    }

                    // Ajouter toutes les versions
                    if (Array.isArray(doc.versions)) {
                        doc.versions.forEach(version => {
                            if (version.uploader_id === userId) {
                                userDocuments.push({
                                    id: version.id,
                                    nom_original: version.nom_original,
                                    version: version.version,
                                    uploader: version.uploader,
                                    created_at: version.created_at,
                                    url: version.url,
                                    is_version: true,
                                    parent_id: doc.id
                                });
                            }
                        });
                    }
                });

                if (userDocuments.length === 0) {
                    userDocumentsList.innerHTML = '<li class="list-group-item text-muted">Aucun document soumis</li>';
                    return;
                }

                // Affichage de tous les documents et versions
                userDocumentsList.innerHTML = userDocuments.map(doc => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <a href="${doc.url}" target="_blank" class="text-decoration-none">
                                <i class="fas fa-file-alt me-2"></i>${doc.nom_original}
                                <span class="badge bg-secondary">v${doc.version}</span>
                            </a>
                            <small class="text-muted">
                                Uploadé par ${doc.uploader} le ${doc.created_at}
                            </small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger delete-doc" data-doc-id="${doc.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </li>
                `).join('');

            } else {
                userDocumentsList.innerHTML = '<li class="list-group-item text-muted">Aucun document soumis</li>';
            }

            setupDocActions();

        } catch(err) {
            console.error('Erreur chargement documents:', err);
            if(userDocumentsList) userDocumentsList.innerHTML = '<li class="list-group-item text-danger">Erreur lors du chargement des documents</li>';
        }
    }

    async function loadParentOptions() {
        try {
            const res = await fetch(`/collaborateur/taches/${tacheId}/documents`);
            const data = await res.json();

            if (!parentSelect) return;

            parentSelect.innerHTML = '<option value="">Nouveau document</option>';

            if (data.success && Array.isArray(data.documents) && data.documents.length) {
                data.documents.forEach(doc => {
                    const mainOption = document.createElement('option');
                    mainOption.value = doc.id;
                    mainOption.textContent = `${doc.nom_original} (v${doc.version})`;
                    parentSelect.appendChild(mainOption);

                    if (Array.isArray(doc.versions)) {
                        doc.versions.forEach(version => {
                            const versionOption = document.createElement('option');
                            versionOption.value = version.id;
                            versionOption.textContent = `${version.nom_original} (v${version.version})`;
                            parentSelect.appendChild(versionOption);
                        });
                    }
                });
            }
        } catch(err) {
            console.error('Erreur chargement options:', err);
        }
    }

    function setupDocActions() {
        if (!userDocumentsList) return;

        userDocumentsList.querySelectorAll('.delete-doc').forEach(btn => {
            btn.addEventListener('click', async () => {
                const docId = btn.dataset.docId;
                if (!confirm('Voulez-vous vraiment supprimer ce document ?')) return;

                try {
                    const response = await fetch(`/collaborateur/taches/${tacheId}/documents/${docId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        await loadUserDocuments();
                        await loadParentOptions();
                    } else {
                        alert('Erreur lors de la suppression du document');
                    }
                } catch(err) {
                    console.error('Erreur suppression document:', err);
                    alert('Erreur lors de la suppression du document');
                }
            });
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(uploadForm);

            try {
                const response = await fetch(`/collaborateur/taches/${tacheId}/documents`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    uploadForm.reset();
                    await loadUserDocuments();
                    await loadParentOptions();
                    alert('Document uploadé avec succès!');
                } else {
                    alert(result.message || 'Erreur lors de l\'upload du document');
                }
            } catch(err) {
                console.error('Erreur upload document:', err);
                alert('Erreur lors de l\'upload du document');
            }
        });
    }

    loadUserDocuments();
    loadParentOptions();
});
</script> 
@endsection
