@extends('layouts.chef')

@section('title', 'Suivi des projets')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style> 
.breadcrumb-container {
    background-color: white;
    padding: 0.75rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.breadcrumb {
    padding: 0;
    margin: 0;
    background-color: transparent;
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-container mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Suivi des projets</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-project-diagram me-2"></i> Suivi des projets
        </h2>
    </div>

    <div class="card dashboard-card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-tasks me-2"></i> Choisissez un projet
            </h5>
        </div>
        <div class="card-body">
            <!-- Sélecteur de projets -->
            <div class="mb-4">
                <select id="projetSelect" class="form-select select2">
                    <option value="">-- Sélectionner un projet --</option>
                    @foreach($projets as $projet)
                        <option value="{{ route('chef_equipe.kanban', $projet->id) }}">
                            {{ $projet->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Zone du Kanban -->
            <iframe 
                id="kanbanFrame" 
                style="width:100%; height:80vh; border:none; background-color: #f8f9fa;"
                title="Kanban du projet"
            ></iframe>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Sélectionner un projet",
            allowClear: true,
            width: '100%'
        });

        $('#projetSelect').on('change', function() {
            let url = this.value;
            $('#kanbanFrame').attr('src', url || '');
        });
    });
</script>
@endsection
