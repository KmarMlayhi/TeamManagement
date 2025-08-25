@extends('layouts.collaborateur')

@section('title', 'Discussion - ' . $projet->nom)



@section('styles')
<link rel="stylesheet" href="{{ asset('css/team.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css">
<style>
    /* Chatbox spécifique au layout avec sidebar */
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
                <li class="breadcrumb-item"><a href="{{ route('collaborateur.home') }}"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('projet_messages.projet_list') }}"> Projets</a></li>
                <li class="breadcrumb-item active">{{ $projet->nom }}</li>
            </ol>
        </nav>
    </div>

    <h3 class="titre-principal mb-4"><i class="fas fa-comments me-2"></i> Discussion : {{ $projet->nom }}</h3>

    <div id="chatBox" class="chat-container mb-3">
        @forelse($messages as $msg)
            @php $isMe = $msg->user_id === Auth::id(); @endphp
            <div class="d-flex mb-2 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="message-bubble {{ $isMe ? 'message-sent' : 'message-received' }}">
                    <small class="d-block mb-1">
                        <strong>
                            @if($msg->user->role && $msg->user->role->name === 'chef_equipe')
                                <i class="fas fa-crown me-1" style="color: gold;"></i>
                            @endif
                            {{ $msg->user->name }}
                        </strong> • 
                        <span class="text-muted">{{ $msg->created_at->format('H:i') }}</span>
                    </small>
                    <p class="mb-0">{{ $msg->message }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-muted mt-3">Aucun message pour le moment.</p>
        @endforelse
    </div>

    <form action="{{ route('projet_messages.store', $projet->id) }}" method="POST" id="chatForm">
        @csrf
        <div class="input-group mb-3">
            <input type="text" name="message" class="form-control" placeholder="Écrire un message..." required>
            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </div>
        @error('message')
            <p class="text-danger mt-1">{{ $message }}</p>
        @enderror
    </form>

</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // récupérer les utilisateurs du projet
        fetch("{{ route('projet.users', $projet->id) }}")
    .then(res => res.json())
    .then(users => {
        const tribute = new Tribute({
            values: users.map(u => ({
                key: u.name + (u.is_chef ? "👑" : ""),
                value: u.id
            })),
            trigger: "@",
            selectTemplate: function (item) {
                return `@${item.original.key}`;
            },
            menuItemTemplate: function (item) {
                // On n’affiche que le nom, jamais d’ancien message
                return item.original.key;
            },
            positionMenu: true
        });


        const input = document.querySelector('input[name="message"]');
        tribute.attach(input);
    })
    .catch(err => console.error('Erreur fetch users:', err));

    });
    const chatBox = document.getElementById('chatBox');
if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
}

</script>

@endsection
