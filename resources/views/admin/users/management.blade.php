@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Utilisateurs</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 page-title">
            <i class="fas fa-users-cog me-2"></i>Gestion des Utilisateurs
        </h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4 stats-row">
        <div class="col-md-3 mb-3">
            <div class="card stats-card pending">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-clock card-icon"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">En attente</h5=6>
                            <h4 class="mb-0">{{ $pendingCollaborateurs + $pendingChefs }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card collaborators">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users card-icon"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Collaborateurs</h6>
                            <h4 class="mb-0">{{ $totalCollaborateurs }}</h4>
                            <small class="stats-subtext">{{ $totalCollaborateurs - $pendingCollaborateurs }} validés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card chefs">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-tie card-icon"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Chefs d'Équipe</h6>
                            <h4 class="mb-0">{{ $totalChefs }}</h4>
                            <small class="stats-subtext">{{ $totalChefs - $pendingChefs }} validés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card validated">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-check card-icon"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Validés</h6>
                            <h4 class="mb-0">{{ ($totalCollaborateurs - $pendingCollaborateurs) + ($totalChefs - $pendingChefs) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card dashboard-card">
        <div class="card-body">
            <div class="card-header">
                <h5 class="card-header-title"><i class="fas fa-list me-2"></i>Liste des Utilisateurs</h5>
                <form method="GET" action="{{ route('admin.users.management') }}" class="search-form">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Rechercher par nom ou email..."
                               value="{{ request('search') }}"
                               aria-label="Recherche">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.users.management') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            @if($users->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucun utilisateur à afficher.
                </div>
            @else
                <div class="table-responsive">
                    <table class="user-management-table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Grade</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Direction</th>
                                <th>Rôle</th>
                                <th>Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>   
                                    <td>
                                        @if($user->is_validated)
                                            <span class="status-badge status-validated">
                                                <i class="fas fa-check-circle me-1"></i> Validé
                                            </span>
                                        @else
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $user->grade ? $user->grade->nom : 'Aucun grade' }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->direction ? $user->direction->nom : 'Aucune direction' }}</td>
                                    <td>
                                        @php $roleName = $user->role?->name ?? 'inconnu'; @endphp

                                        @if($roleName === 'chef_equipe')
                                            <span class="badge badge-chef">
                                                <i class="fas fa-user-tie me-1"></i> Chef d'Équipe
                                            </span>
                                        @elseif($roleName === 'admin')
                                            <span class="badge badge-admin">
                                                <i class="fas fa-user-shield me-1"></i> Admin
                                            </span>
                                        @elseif($roleName === 'collaborateur')
                                            <span class="badge badge-collaborator">
                                                <i class="fas fa-user me-1"></i> Collaborateur
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ ucfirst($roleName) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td> {{ $user->created_at->format('d/m/Y') }}<br>
    {{ $user->created_at->format('H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @if(!$user->is_validated && $roleName !== 'admin')
                                                <form action="{{ route('admin.users.validate', $user) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-validate" title="Valider">
                                                        <i class="fas fa-check"></i> 
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->is_validated && $roleName !== 'admin')
                                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Suspendre">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($roleName !== 'admin')
                                                <form action="{{ route('admin.users.delete', $user) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-delete" title="Supprimer"
                                                        onclick="return confirm('Confirmer la suppression de {{ $user->name }} ?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection