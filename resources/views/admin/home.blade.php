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
   <style>
    /* Stats Cards - Horizontal Layout with Left Alignment */
    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .clickable-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15) !important;
    }
    .stats-row {
        margin-bottom: 2rem;
    }

    .stats-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        height: 100%;
        background: white;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        display: flex;
        flex-direction: row; /* Changé de column à row */
        align-items: center; /* Centre verticalement */
        text-align: left; /* Alignement du texte à gauche */
        padding: 1.25rem;
        gap: 1rem; /* Espace entre l'icône et le texte */
    }

    /* Reste du CSS inchangé... */
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stats-card.pending {
        border-left-color: var(--warning-color);
    }

    .stats-card.collaborators {
        border-left-color: var(--primary-color);
    }

    .stats-card.chefs {
        border-left-color: var(--purple-color);
    }

    .stats-card.validated {
        border-left-color: var(--success-color);
    }

    /* Adaptation des icônes pour le layout horizontal */
    .card-icon-container {
        width: 50px;
        height: 50px;
        min-width: 50px; /* Empêche le rétrécissement */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0; /* Retiré car inutile en horizontal */
        background: rgba(0, 0, 0, 0.03);
    }

    /* Text Styling - Adapté pour l'alignement à gauche */
    .stats-card h6 {
        font-size: 0.9rem;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }

    .stats-card .stat-value {
        font-size: 1.5rem; /* Légèrement réduit pour le layout horizontal */
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 0.25rem;
    }

    .stats-subtext {
        color: #6c757d;
        font-size: 0.8rem;
        font-weight: 400;
        display: block;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .stats-card {
            flex-direction: column; /* Revert to column on smaller screens */
            text-align: center;
            padding: 1rem;
        }
        
        .card-icon-container {
            margin-bottom: 0.75rem;
        }
        
        .stats-card .stat-value {
            font-size: 1.75rem; /* Taille originale sur mobile */
        }
    }
</style>

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
