@extends('layouts.admin') {{-- Hérite du layout admin --}}

@section('title', 'Gestion des Utilisateurs') {{-- Définit le titre de la page --}}

@section('styles')
    <style>
        .status-badge {
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
            color: white;
            font-size: 0.75em;
        }
        .status-validated { background-color: green; }
        .status-pending { background-color: orange; }
        .btn-action {
            display: flex; /* Utilise flexbox pour aligner icône et texte */
            align-items: center; /* Centre verticalement */
            gap: 5px; /* Espace entre l'icône et le texte */
        }
    </style>
@endsection

@section('content')
    <h2 class="mb-4">Gestion des Comptes Utilisateurs</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Les compteurs sont toujours ici si tu veux les afficher sur la page de gestion aussi --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users card-icon text-primary"></i>
                    <h5 class="card-title">Total Utilisateurs</h5>
                    <h2 class="mb-0">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-clock card-icon text-warning"></i>
                    <h5 class="card-title">En attente</h5>
                    <h2 class="mb-0">{{ $pendingUsersCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-check card-icon text-success"></i>
                    <h5 class="card-title">Validés</h5>
                    <h2 class="mb-0">{{ $validatedUsersCount }}</h2>
                </div>
            </div>
        </div>
    </div>


    <!-- Users Table -->
    <div class="card dashboard-card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Liste des Collaborateurs</h5>
        </div>
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info">Aucun collaborateur enregistré ou à valider.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Statut</th>
                                <th>Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
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
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if(!$user->is_validated)
                                                <form action="{{ route('admin.users.validate', $user) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-action">
                                                        <i class="fas fa-check"></i> Valider
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Confirmer la suppression de {{ $user->name }} ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-action">
                                                    <i class="fas fa-trash-alt"></i> Supprimer
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
