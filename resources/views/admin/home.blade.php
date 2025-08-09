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
    @endsection
