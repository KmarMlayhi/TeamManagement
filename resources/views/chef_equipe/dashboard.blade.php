@extends('layouts.chef')

@section('content')
    <div class="container">
        <h1>Espace Chef d'Équipe</h1>
        <p>Bienvenue {{ Auth::user()->name }} !</p>
        
        <!-- Contenu spécifique -->
        <div class="mt-4">
            <h3>Fonctionnalités disponibles :</h3>
            <ul>
                <li>Gestion des plannings</li>
                <li>Validation des demandes</li>
                <li>Suivi des collaborateurs</li>
            </ul>
        </div>
    </div>
@endsection