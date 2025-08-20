<div class="message-response p-3 bg-white border rounded mb-3" id="reponse-{{ $reponse->id }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">
            <i class="fas fa-reply me-1"></i>
            {{ $reponse->auteur->name }} • <span class="response-time">{{ $reponse->created_at->diffForHumans() }}</span>
        </small>
        <button class="btn btn-sm btn-outline-danger delete-comment" data-id="{{ $reponse->id }}">
            <i class="far fa-trash-alt"></i>
        </button>
    </div>
    <p class="mb-0">{{ $reponse->contenu }}</p>
</div>