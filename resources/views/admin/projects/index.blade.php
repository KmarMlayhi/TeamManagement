@extends('layouts.admin')

@section('title', 'Gestion des projets')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Projets</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-project-diagram me-2"></i>Gestion des Projets
        </h2>
        <a href="{{ route('admin.projets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouveau projet
        </a>
    </div>
    <!-- Ajoutez cette section avant la carte dashboard-card -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.projets.index') }}" method="GET">
            <div class="input-group">
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Rechercher par nom..." 
                       value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.projets.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des projets</h5>
            <span class="badge bg-primary rounded-pill">{{ $projets->total() }}</span>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($projets->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>Aucun projet créé pour le moment.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Client</th>
                                <th>Équipe</th>
                                <th>Dates</th>
                                <th>Statut</th>
                                <th style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projets as $projet)
                            <tr>
                                <td>
                                    <strong>{{ $projet->nom }}</strong>
                                    @if($projet->taches_count > 0)
                                        <span class="badge badge-team ms-2">
                                            <i class="fas fa-tasks me-1"></i>{{ $projet->taches_count }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $projet->client ?? '-' }}</td>
                                <td>
                                    @if($projet->equipe)
                                        <span class="badge badge-team">
                                            <i class="fas fa-users me-1"></i>{{ $projet->equipe->nom }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted d-block">Début: {{ $projet->date_debut->format('d/m/Y') }}</small>
                                    <small class="text-muted">Fin: {{ $projet->date_fin_prevue->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $projet->statut }}">
                                        <i class="fas fa-circle me-1 small"></i>{{ $projet->statut_text }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.projets.show', $projet) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.projets.edit', $projet) }}" class="btn btn-sm btn-outline-primary" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.projets.destroy', $projet) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
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

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Affichage de <strong>{{ $projets->firstItem() }}</strong> à <strong>{{ $projets->lastItem() }}</strong> sur <strong>{{ $projets->total() }}</strong> projets
                    </div>
                    <div>
                        {{ $projets->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection