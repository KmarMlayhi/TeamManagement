@extends('layouts.collaborateur')

@section('title', 'Détail de l équipe')
@section('content')
<div class="container">
    <h1>{{ $equipe->nom }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5>Informations</h5>
            <p><strong>Niveau :</strong> {{ $equipe->niveau_complet }}</p>
            <p><strong>Créée par :</strong> {{ $equipe->creator->name ?? 'Inconnu' }}</p>
            
            @if($equipe->parent)
                <p><strong>Équipe parente :</strong> {{ $equipe->parent->nom }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Membres</div>
                <ul class="list-group list-group-flush">
                    @foreach($equipe->utilisateurs as $membre)
                        <li class="list-group-item">
                            {{ $membre->name }} ({{ $membre->fonction }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Projets associés</div>
                <ul class="list-group list-group-flush">
                    @foreach($equipe->projets as $projet)
                        <li class="list-group-item">{{ $projet->nom }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    
    @if($equipe->children->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header">Sous-équipes</div>
            <ul class="list-group list-group-flush">
                @foreach($equipe->children as $child)
                    <li class="list-group-item">
                        <a href="{{ route('collaborateur.equipes.show', $child) }}">
                            {{ $child->nom }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection