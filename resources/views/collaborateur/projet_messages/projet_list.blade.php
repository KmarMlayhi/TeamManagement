@extends('layouts.collaborateur')

@section('title', 'Projets - Discussions')

@section('content')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<div class="container-fluid mt-4">

    <!-- Breadcrumb -->
    <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item active">Projets - Discussions</li>
            </ol>
        </nav>
    </div>

    <h2 class="titre-principal mb-4"><i class="fas fa-comment-alt me-2"></i>Choisir un projet pour discuter</h2>

    @if($projets->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Aucun projet disponible pour la discussion.
        </div>
    @else
        <div class="row g-4">
            @foreach($projets as $projet)
            <div class="col-md-6 col-lg-4">
                <div class="dashboard-card p-3 d-flex flex-column h-100">
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar-sm me-3">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 bg-projet">{{ $projet->nom }}</h5>
                            <div class="mb-1">
                                @if($projet->statut)
                                    <span class="badge 
                                        @switch($projet->statut)
                                            @case('En attente') status-en_attente @break
                                            @case('En cours') status-en_cours @break
                                            @case('Terminé') status-termine @break
                                            @case('Suspendu') status-suspendu @break
                                            @default bg-secondary
                                        @endswitch
                                    ">
                                        {{ $projet->statut }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('projet_messages.index', $projet->id) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-comments me-1"></i> Discussion
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
