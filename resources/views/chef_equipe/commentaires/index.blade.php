@extends('layouts.chef')

@section('title', 'Commentaires des collaborateurs')

@section('content')
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Commentaires</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary-dark">
            <i class="fas fa-comments me-2"></i>Commentaires des collaborateurs
        </h2>
    </div>

    <div class="card dashboard-card">
        <div class="card-header bg-primary-dark text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>Boîte de réception</h5>
                <form method="GET" class="w-50 ms-3">
                    <select name="projet_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Sélectionner un projet --</option>
                        @foreach($projets as $projet)
                            <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>
                                {{ $projet->nom }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            @if(request()->has('projet_id') && $selectedProjet)
                <div class="mb-4">
                    <h4 class="text-primary-dark">
                        <i class="fas fa-project-diagram me-2"></i>{{ $selectedProjet->nom }}
                    </h4>
                    <p class="text-muted">{{ $commentaires->total() }} commentaire(s)</p>
                </div>

                @if($commentaires->count() > 0)
                    @foreach($commentaires as $commentaire)
                        <div class="card mb-4 shadow-sm comment-card" id="comment-{{ $commentaire->id }}">
                            <div class="card-header bg-light d-flex align-items-center">
                                <div class="avatar bg-primary-dark text-white rounded-circle me-3">
                                    {{ substr($commentaire->auteur->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $commentaire->auteur->name }}</h6>
                                    <small class="text-muted">
                                        {{ $commentaire->created_at->diffForHumans() }} • {{ $commentaire->tache->titre ?? 'Tâche non spécifiée' }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="card-body">
                            <div class="conversation-thread">
                                <!-- Message initial -->
                                <div class="message-initial p-3 bg-light rounded mb-3">
                                    <p class="mb-0">{{ $commentaire->contenu }}</p>
                                </div>

                                <!-- Réponses -->
                                <div class="reponses-container ms-4" id="reponses-{{ $commentaire->id }}">
                                        @foreach($commentaire->reponses as $reponse)
                                            @php
                                                $isChef = $reponse->auteur->id === Auth::id();
                                                $bgColor = $isChef ? 'bg-chef-response' : 'bg-white';
                                                $borderColor = $isChef ? 'border-primary-dark' : '';
                                            @endphp

                                            <div class="message-response p-3 {{ $bgColor }} border {{ $borderColor }} rounded mb-3" id="reponse-{{ $reponse->id }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-reply me-1"></i>
                                                        {{ $reponse->auteur->name }} • <span class="response-time">{{ $reponse->created_at->diffForHumans() }}</span>
                                                        @if($isChef)
                                                            <span class="badge bg-chef-badge text-white ms-2">Chef d'équipe</span>
                                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-comment" 
                                                                data-id="{{ $reponse->id }}" 
                                                                data-contenu="{{ $reponse->contenu }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </small>
                                                    <button class="btn btn-sm btn-outline-danger delete-comment" data-id="{{ $reponse->id }}">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                                <p class="mb-0">{{ $reponse->contenu }}</p>
                                            </div>
                                        @endforeach
                                </div>


                                <!-- Formulaire de réponse -->
                                <form class="mt-3 ms-4 ajax-form" 
                                    data-comment-id="{{ $commentaire->id }}"
                                    method="POST" 
                                    action="{{ route('chef_equipe.commentaires.repondre', $commentaire->id) }}">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" name="contenu" class="form-control" placeholder="Écrire une réponse..." required>
                                        <button type="submit" class="btn btn-primary-dark">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $commentaires->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun commentaire pour ce projet</h4>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Sélectionnez un projet pour voir les commentaires</h4>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-dark text-white">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('scripts')
<script>
    $(document).ready(function() {

        // ----- Soumission de réponse en AJAX -----
        $('body').on('submit', '.ajax-form', function(e) {
            e.preventDefault();
            const form = $(this);
            const commentId = form.data('comment-id');
            const reponsesContainer = $('#reponses-' + commentId);

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const isChef = response.is_chef;
                        const bgColor = isChef ? 'bg-chef-response' : 'bg-white';
                        const borderColor = isChef ? 'border-primary-dark' : '';
                        const badge = isChef ? '<span class="badge bg-chef-badge text-white ms-2">Chef d\'équipe</span>' : '';
                        
                        const html = `
                            <div class="message-response p-3 ${bgColor} border ${borderColor} rounded mb-3" id="reponse-${response.reponse.id}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-reply me-1"></i>
                                        ${response.reponse.auteur} • <span class="response-time">${response.reponse.created_at_humans}</span>
                                        ${badge}
                                    </small>
                                    <button class="btn btn-sm btn-outline-danger delete-comment" data-id="${response.reponse.id}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                                <p class="mb-0">${response.reponse.contenu}</p>
                            </div>
                        `;
                        reponsesContainer.append(html);
                        form.find('input[name="contenu"]').val('');
                        $('#reponse-' + response.reponse.id).hide().fadeIn(400);
                    }
                },
                error: function(xhr) {
                    alert('Une erreur est survenue: ' + (xhr.responseJSON?.message || 'Erreur inconnue'));
                }
            });
        });
        // ----- Modifier un commentaire (réponse du chef) -----
    $('body').on('click', '.edit-comment', function() {
        const button = $(this);
        const commentId = button.data('id');
        const commentCard = $('#reponse-' + commentId);
        const contenuActuel = button.data('contenu');

        // Si un input existe déjà, ne rien faire
        if (commentCard.find('textarea.edit-input').length) return;

        const textarea = $(`<textarea class="form-control edit-input mb-2">${contenuActuel}</textarea>`);
        const saveBtn = $('<button class="btn btn-sm btn-success me-1">Enregistrer</button>');
        const cancelBtn = $('<button class="btn btn-sm btn-secondary">Annuler</button>');

        // Masquer le contenu actuel
        commentCard.find('p.mb-0').hide();
        // Ajouter l'input et boutons
        commentCard.append(textarea).append(saveBtn).append(cancelBtn);

        // Enregistrer la modification
        saveBtn.on('click', function() {
            $.ajax({
                url: '/chef-equipe/commentaires/' + commentId,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    contenu: textarea.val()
                },
                success: function(response) {
                    if (response.success) {
                        commentCard.find('p.mb-0').text(response.commentaire.contenu).fadeIn();
                        textarea.remove();
                        saveBtn.remove();
                        cancelBtn.remove();
                        button.data('contenu', response.commentaire.contenu); // mettre à jour le data
                    }
                },
                error: function(xhr) {
                    alert('Erreur lors de la modification: ' + (xhr.responseJSON?.message || 'Erreur inconnue'));
                }
            });
        });

        // Annuler la modification
        cancelBtn.on('click', function() {
            textarea.remove();
            saveBtn.remove();
            cancelBtn.remove();
            commentCard.find('p.mb-0').fadeIn();
        });
    });

        // ----- Préparer la suppression -----
        $('body').on('click', '.delete-comment', function() {
            const commentId = $(this).data('id');
            const form = $('#deleteForm');
            form.attr('data-comment-id', commentId); 
            $('#deleteModal').modal('show');
        });

        // ----- Supprimer en AJAX -----
        $('body').on('submit', '#deleteForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const commentId = form.data('comment-id');

            const commentCard = $('#reponse-' + commentId).length ? 
                                $('#reponse-' + commentId) : 
                                $('#comment-' + commentId);

            $.ajax({
                url: '/chef-equipe/commentaires/' + commentId,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        commentCard.fadeOut(300, function() { $(this).remove(); });
                        $('#deleteModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    alert('Erreur lors de la suppression: ' + (xhr.responseJSON?.message || 'Erreur inconnue'));
                }
            });
        });

    });
</script>
@endsection
<style>
        
    .breadcrumb-container {
        background-color: white;
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .breadcrumb {
        padding: 0;
        margin: 0;
        background-color: transparent;
    }
    /* Nouveau design sans lignes verticales */
    .conversation-thread:before,
    .message-response:before {
        display: none !important;
    }

    /* Couleur principale */
    :root {
        --primary-dark: #003366;
        --primary-light: #336699;
        --primary-lighter: #6699cc;
    }

    /* Couleurs améliorées */
    .bg-primary-dark {
        background-color: var(--primary-dark) !important;
        color: white !important;
    }

    .btn-primary-dark {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    .btn-primary-dark:hover {
        background-color: #002244;
        border-color: #002244;
        color: white;
    }

    .border-primary-dark {
        border-color: var(--primary-dark) !important;
    }

    .bg-chef-response {
        background-color: rgba(0, 51, 102, 0.08) !important;
        border-left: 3px solid var(--primary-dark) !important;
    }

    .bg-chef-badge {
        background-color:  #ffc107; !important;
    }

    .text-primary-dark {
        color: var(--primary-dark) !important;
    }

    /* Autres styles */
    .avatar {
        width: 40px;
        height: 40px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-top: 3px solid var(--primary-dark);
    }


    .comment-card {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .comment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }

    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .message-initial {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 3px solid #adb5bd;
    }

    .message-response {
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 15px;
        border-left: 3px solid #dee2e6;
        transition: background-color 0.3s;
    }

    .reponses-container {
        padding-left: 0;
        list-style: none;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
    }

    .delete-comment {
        transition: all 0.2s;
    }

    .delete-comment:hover {
        transform: scale(1.05);
    }

    .modal-header {
        border-bottom: none;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }

    /* Animation pour les nouveaux messages */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-response {
        animation: fadeIn 0.4s ease-out;
    }
</style>
@endsection