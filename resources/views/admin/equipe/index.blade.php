@extends('layouts.admin')

@section('title', 'Gestion des équipes')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<h2 class="mb-4">Gestion des Équipes</h2>

<div class="card dashboard-card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Liste des équipes</h5>
            <a href="{{ route('equipes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouvelle équipe
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($equipes->isEmpty())
            <div class="alert alert-info">Aucune équipe créée pour le moment.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Membres</th>
                            <th>Équipe mère</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipes as $equipe)
                            <tr>
                                <td>
                                    <strong>{{ $equipe->nom }}</strong>
                                    @if($equipe->children->count() > 0)
                                        <span class="badge bg-info ms-2">
                                            {{ $equipe->children->count() }} sous-équipe(s)
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($equipe->utilisateurs as $user)
                                        <span class="badge bg-light text-dark me-1 mb-1">
                                            {{ $user->name }}
                                            @if($loop->count > 5 && $loop->iteration == 5)
                                                +{{ $loop->count - 5 }} autres
                                                @break
                                            @endif
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $equipe->parent->nom ?? '-' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('equipes.destroy', $equipe->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                    onclick="return confirm('Supprimer cette équipe ?')">
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
        @endif
    </div>
</div>

<style>
    .badge-user {
        transition: all 0.3s;
    }
    .badge-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection