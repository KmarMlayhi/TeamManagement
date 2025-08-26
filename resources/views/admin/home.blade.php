@extends('layouts.admin')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeUser.css') }}">
</head>
<body>
    @section('content')
    <div class="dashboard-container">
        <!-- Section de bienvenue -->
        <div class="welcome-card">
            <div class="welcome-header">
                <h1>Espace Administrateur </h1>
                <p>Suivi du déroulement des projets</p>
            </div>
            <div class="mt-3">
                <span class="user-badge">
                    <i class="fas fa-user"></i>
                    Bienvenue {{ Auth::user()->name }} !
                </span>
            </div>
        </div>
    </div>
    <!-- Stats Cards -->
<div class="row stats-row">
    <!-- Carte En attente -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.users.management') }}" class="text-decoration-none">
            <div class="stats-card pending clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-user-clock card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>En attente</h6>
                    <div class="stat-value">{{ $pendingCollaborateurs + $pendingChefs }}</div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Carte Collaborateurs -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.users.management') }}" class="text-decoration-none">
            <div class="stats-card collaborators clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-users card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Collaborateurs</h6>
                    <div class="stat-value">{{ $totalCollaborateurs }}</div>
                    <span class="stats-subtext">{{ $totalCollaborateurs - $pendingCollaborateurs }} validés</span>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Carte Chefs d'Équipe -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.users.management') }}" class="text-decoration-none">
            <div class="stats-card chefs clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-user-tie card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Chefs d'Équipe</h6>
                    <div class="stat-value">{{ $totalChefs }}</div>
                    <span class="stats-subtext">{{ $totalChefs - $pendingChefs }} validés</span>
                </div>
            </div>
        </a>
    </div>
    <!-- Carte Total Validés -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.users.management') }}" class="text-decoration-none">
            <div class="stats-card validated clickable-card">
                <div class="card-icon-container">
                    <i class="fas fa-user-check card-icon"></i>
                </div>
                <div class="card-text-content">
                    <h6>Total Validés</h6>
                    <div class="stat-value">{{ ($totalCollaborateurs - $pendingCollaborateurs) + ($totalChefs - $pendingChefs) }}</div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
