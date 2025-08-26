@extends('layouts.chef')

@section('title', 'Projets - Discussions')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
@endsection

@section('content')
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('chef_equipe.dashboard') }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Discussions projets</li>
        </ol>
    </nav>
</div>

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="titre-principal mb-0"><i class="fas fa-comment-alt me-2"></i> Discussions projets</h2>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Liste des projets</h5>
        </div>
        <div class="card-body">
            @if($projets->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucun projet disponible pour la discussion.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du projet</th>
                                <th>Équipe(s) associée(s)</th>
                                <th width="150px" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projets as $projet)
                            <tr>
                                <td>{{ $projet->nom }}</td>
                                <td>
                                    @forelse($projet->equipes as $equipe)
                                        <span class="badge badge-team mb-1">{{ $equipe->nom }}</span>
                                    @empty
                                        <span class="text-muted small">Aucune équipe</span>
                                    @endforelse
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('chef_equipe.project_messages.index', $projet->id) }}" 
                                       class="btn btn-sm btn-outline-primary btn-task-manager" 
                                       title="Discussion">
                                        <i class="fas fa-comments me-1"></i> Discussion
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($projets, 'links'))
                    <div class="d-flex justify-content-end mt-3">
                        {{ $projets->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
