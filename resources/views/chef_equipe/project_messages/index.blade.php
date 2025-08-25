@extends('layouts.chef')

@section('title', 'Discussion - ' . $projet->nom)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css">

<style>
    #chatBox {
        height: calc(100vh - var(--navbar-height) - 150px); /* Ajusté pour navbar et espace input */
        overflow-y: auto;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .message-bubble {
        padding: 0.5rem 1rem;
        border-radius: 15px;
        max-width: 70%;
        word-wrap: break-word;
    }

    .message-sent {
        background-color: var(--secondary-color);
        color: white;
        margin-left: auto;
    }

    .message-received {
        background-color: #e9ecef;
        color: #212529;
        margin-right: auto;
    }

    .message-bubble small {
        display: block;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        color: #6c757d;
    }

    /* Scrollbar stylée */
    #chatBox::-webkit-scrollbar {
        width: 6px;
    }

    #chatBox::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }

    /* Formulaire input en bas */
    #chatForm .input-group {
        max-width: 100%;
    }
    .tribute-container {
    position: absolute !important;
    bottom: auto !important;
    top: 100% !important;   /* toujours en dessous de l'input */
    left:25% !important;
    z-index: 9999;
}
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('chef_equipe.dashboard') }}"><i class="fas fa-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('chef_equipe.project_messages.projet_list') }}">Projets</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $projet->nom }}</li>
            </ol>
        </nav>
    </div>

    <h3 class="titre-principal mb-4"><i class="fas fa-comments me-2"></i> Discussion : {{ $projet->nom }}</h3>

    <div id="chatBox" class="mb-3">
        @forelse($messages as $msg)
            <div class="d-flex mb-2 {{ $msg->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="message-bubble {{ $msg->user_id == auth()->id() ? 'message-sent' : 'message-received' }}">
                    <small>{{ $msg->user->name }} • {{ $msg->created_at->format('H:i') }}</small>
                    <p>{{ $msg->message }}</p>
                    
                    @if($msg->fichier)
                       <p class="mt-2">
                             <a href="{{ asset('storage/' . $msg->fichier) }}" target="_blank">
                                 <i class="fas fa-file-alt me-1"></i> {{ $msg->fichier_original }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-muted mt-3">Aucun message pour le moment.</p>
        @endforelse

    </div>

    <form action="{{ route('chef_equipe.project_messages.store', $projet->id) }}" method="POST" id="chatForm" enctype="multipart/form-data">
    @csrf
        <div class="input-group mb-3">
            <input type="text" name="message" class="form-control" placeholder="Écrire un message...">
            <input type="file" name="fichier" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </div>
        @error('message')
            <p class="text-danger mt-1">{{ $message }}</p>
        @enderror
        @error('fichier')
            <p class="text-danger mt-1">{{ $message }}</p>
        @enderror
    </form>

</div>


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les utilisateurs du projet pour le @
    fetch("{{ route('chef_equipe.project_users', $projet->id) }}")
        .then(res => res.json())
        .then(users => {
            const tribute = new Tribute({
                values: users.map(u => ({ key: u.name, value: u.id })),
                trigger: "@",
                selectTemplate: function (item) {
                    return `@${item.original.key}`; 
                }
            });

            const input = document.querySelector('input[name="message"]');
            input.value = ''; // vide l'input avant d'attacher Tribute
            tribute.attach(input);
        })
        .catch(err => console.error("Erreur fetch users:", err));
});

// Scroll automatique vers le bas
const chatBox = document.getElementById('chatBox');
if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>
@endsection

