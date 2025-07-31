@extends('layouts.admin')

@section('title', 'Gestion des équipes')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Équipes</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-users me-2"></i>Gestion des Équipes
        </h2>
        <a href="{{ route('equipes.create') }}" class="btn btn-primary">
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
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($equipes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>Aucune équipe créée pour le moment.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Membres</th>
                                <th>Équipe mère</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipes as $equipe)
                                <tr>
                                    <td>
                                        <strong>{{ $equipe->nom }}</strong>
                                        @if($equipe->children->count() > 0)
                                            <span class="badge badge-team ms-2">
                                                <i class="fas fa-sitemap me-1"></i>{{ $equipe->children->count() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($equipe->utilisateurs as $user)
                                            <span class="badge badge-user">
                                                <i class="fas fa-user-circle me-1"></i>{{ $user->name }}
                                                @if($loop->count > 5 && $loop->iteration == 5)
                                                    +{{ $loop->count - 5 }} autres
                                                    @break
                                                @endif
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($equipe->parent)
                                            <span class="badge badge-team">
                                                <i class="fas fa-level-up-alt me-1"></i>{{ $equipe->parent->nom }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('equipe.edit', $equipe->id) }}" class="btn btn-sm btn-outline-primary" title="Éditer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('equipes.destroy', $equipe->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
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
                 {{-- <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Affichage de <strong>{{ $equipes->firstItem() }}</strong> à <strong>{{ $equipes->lastItem() }}</strong> sur <strong>{{ $equipes->total() }}</strong> equipes
                    </div>
                    <div>
                        {{ $equipes->links() }}
                    </div>
                </div> --}}
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection