@extends('layouts.collaborateur')

@section('title', 'Accès des équipes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Mes équipes</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
       <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-users me-2"></i>Mes équipes
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Équipes auxquelles j'appartiens</h5>
        </div>

        <div class="card-body">
            @if($equipes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Vous n'appartenez à aucune équipe pour le moment.
                </div>
            @else
                <div class="team-grid">
                    @foreach($equipes as $equipe)
                    <div class="team-card-access">
                        <div class="team-header-access">
                            <div class="team-icon-access">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h5 class="team-title-access">{{ $equipe->nom }}</h5>
                                <div class="team-meta">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-layer-group me-1"></i>Niveau {{ $equipe->niveau }}
                                    </span>
                                    <span class="badge bg-info">
                                        <i class="fas fa-users me-1"></i>{{ $equipe->utilisateurs_count }} membres
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="team-info">
                            @if($equipe->parent)
                            <div class="team-parent">
                                <i class="fas fa-level-up-alt me-2"></i>
                                <strong>Équipe parente:</strong> {{ $equipe->parent->nom }}
                            </div>
                            @endif
                            
                            @if($equipe->description)
                            <div class="team-description mt-2">
                                <p class="mb-0">{{ $equipe->description }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <div class="team-actions mt-3">
                            <a href="{{ route('collaborateur.equipes.show', $equipe) }}" 
                               class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i> Voir les détails
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

<style>
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .team-card-access {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border-top: 3px solid var(--primary-color);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .team-card-access:hover {
        transform: none;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .team-header-access {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .team-icon-access {
        background: var(--secondary-color);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.5rem;
    }
    
    .team-title-access {
        font-weight: 600;
        color: var(--secondary-color);
        margin: 0 0 5px 0;
    }
    
    .team-meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .team-info {
        flex: 1;
    }
    
    .team-parent {
        font-size: 0.9rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }
    
    .team-description {
        font-size: 0.95rem;
        color: #495057;
        border-top: 1px solid #f1f1f1;
        padding-top: 10px;
    }
    
    .team-actions {
        margin-top: auto;
    }
</style>