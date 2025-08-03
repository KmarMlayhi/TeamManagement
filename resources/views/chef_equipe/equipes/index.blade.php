@extends('layouts.chef')

@section('title', 'Gestion des équipes')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Équipes</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-secondary-color">
            <i class="fas fa-users me-2"></i>Gestion des Équipes
        </h2>
        <a href="{{ route('chef_equipe.equipes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle équipe
        </a>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des équipes</h5>
        </div>

        <div class="card-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($equipes->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Aucune équipe créée pour le moment.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle table-equipes">
                <thead class="table-light">
                    <tr>
                        <th width="80px">Niveau</th>
                        <th>Nom de l'équipe</th>
                        <th width="120px">Sous-équipes</th>
                        <th>Membres</th>
                        <th>Équipe parente</th>
                        <th width="100px" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipes as $equipe)
                        <tr>
                            <td>
                                <span class="badge bg-primary rounded-pill">
                                    Niveau {{ $equipe->niveau }}
                                </span>
                            </td>
                            <td>
                                <div class="team-name">
                                    {{ $equipe->nom }}
                                </div>
                            </td>
                            <td>
                                @if($equipe->children->count() > 0)
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-sitemap me-1"></i>
                                        {{ $equipe->children->count() }} sous-équipe(s)
                                    </span>
                                @else
                                    <span class="text-muted small">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <div class="members-badges">
                                    @forelse($equipe->utilisateurs as $user)
                                        <span class="badge bg-light text-dark member-badge" title="{{ $user->name }} - {{ $user->fonction }}">
                                            <i class="fas fa-user-circle me-1"></i>
                                            {{ Str::limit($user->name, 10) }}
                                            @if($user->role === 'chef_equipe')
                                                <i class="fas fa-crown text-warning ms-1"></i>
                                            @endif
                                        </span>
                                    @empty
                                        <span class="text-muted small">Aucun membre</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if($equipe->parent)
                                    <span class="parent-team" title="{{ $equipe->parent->nom }}">
                                        <i class="fas fa-level-up-alt me-1"></i>
                                        {{ Str::limit($equipe->parent->nom, 20) }}
                                    </span>
                                @else
                                    <span class="text-muted small">Aucune</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="#" class="btn btn-sm btn-outline-primary view-equipe" title="Voir détails" data-id="{{ $equipe->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <a href="{{ route('chef_equipe.equipes.edit', $equipe->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                       <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('chef_equipe.equipes.destroy', $equipe->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                title="Supprimer"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette équipe ?')">
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
        
        <!-- Pagination -->
        @if($equipes->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Affichage de {{ $equipes->firstItem() }} à {{ $equipes->lastItem() }} sur {{ $equipes->total() }} équipes
            </div>
            @if($equipes->hasPages())
            <div>
                {{ $equipes->links() }}
            </div>
            @endif
        </div>
        @endif
    @endif
</div>
    </div>
</div>
<!-- Modal pour voir les détails -->
<div class="modal fade" id="equipeDetailsModal" tabindex="-1" aria-labelledby="equipeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipeDetailsModalLabel">Détails de l'équipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="equipeDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<!-- Ajoutez ceci AVANT votre script personnalisé -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Activation des tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover'
        })
    })

    // Confirmation avant suppression
    $('form[method="POST"]').on('submit', function(e) {
        if ($(this).find('input[name="_method"][value="DELETE"]').length && 
            !confirm('Êtes-vous sûr de vouloir supprimer cette équipe ?')) {
            e.preventDefault()
        }
    })
    // Dans la section scripts
$('.select2-single').select2({
    templateResult: formatEquipe,
    templateSelection: formatEquipe
});

function formatEquipe(equipe) {
    if (!equipe.id) return equipe.text;
    const niveau = $(equipe.element).data('niveau');
    return $('<span>').html(
        '<span style="margin-left:' + (niveau * 15) + 'px">' + 
        (niveau > 1 ? '└ ' : '') + equipe.text + '</span>'
    );
}

    // Gestion du clic sur le bouton "Voir"
    $('.view-equipe').click(function(e) {
        e.preventDefault();
        const equipeId = $(this).data('id');
        
        // Chargement des données via AJAX
        $.get(`/chef-equipe/equipes/${equipeId}/details`, function(data) {
            let html = `
                <div class="mb-4">
                    <h6>${data.nom}</h6>
                    <p class="mb-1"><strong>Niveau:</strong> ${data.niveau}</p>
                    ${data.parent ? `<p class="mb-1"><strong>Équipe parente:</strong> ${data.parent.nom} (Niveau ${data.parent.niveau})</p>` : ''}
                </div>
                
                <div class="mb-4">
                    <h6>Membres (${data.membres.length})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Fonction</th>
                                    <th>Rôle</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            data.membres.forEach(membre => {
                html += `
                    <tr>
                        <td>${membre.name}</td>
                        <td>${membre.fonction}</td>
                        <td>${membre.role === 'chef_equipe' ? '<span class="badge bg-warning">Chef d\'équipe</span>' : 'Membre'}</td>
                        <td>${membre.email}</td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                
                ${data.sous_equipes.length > 0 ? `
                <div class="mb-3">
                    <h6>Sous-équipes (${data.sous_equipes.length})</h6>
                    <ul class="list-group">
                ` : ''}
            `;
            
            data.sous_equipes.forEach(equipe => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${equipe.nom}
                        <span class="badge bg-primary rounded-pill">${equipe.membres_count} membres</span>
                    </li>
                `;
            });
            
            if (data.sous_equipes.length > 0) {
                html += `</ul></div>`;
            }
            
            $('#equipeDetailsContent').html(html);
            $('#equipeDetailsModal').modal('show');
        });
    });
})
</script>
@endsection