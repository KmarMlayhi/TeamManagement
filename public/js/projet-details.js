$(document).ready(function () {
    $(".view-projet").click(function (e) {
        e.preventDefault();
        const projetId = $(this).data("id");

        $("#projetDetailsContent").html(`
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Chargement des détails du projet...</p>
            </div>
        `);

        $("#projetDetailsModal").modal("show");

        $.get(`/chef-equipe/projets/${projetId}/details`, function (data) {
            let html = `
                <div class="mb-4">
                    <h3 class="text-primary mb-3"><i class="fas fa-project-diagram me-2"></i>${
                        data.nom
                    }</h3>
                    <div class="bg-light p-4 rounded mb-4">
                        <p class="lead mb-0">${
                            data.description || "Aucune description disponible"
                        }</p>
                    </div>
                </div>
                
                <h5 class="section-title"><i class="fas fa-info-circle me-2"></i>Informations clés</h5>
                <div class="info-grid">
                    <div class="info-card">
                        <h6><i class="fas fa-user-tie me-2"></i>Client</h6>
                        <p>${data.client || "Non spécifié"}</p>
                    </div>
                    
                    <div class="info-card">
                        <h6><i class="fas fa-calendar-day me-2"></i>Date de début</h6>
                        <p>${data.date_debut_formatted}</p>
                    </div>
                    
                    <div class="info-card">
                        <h6><i class="fas fa-calendar-check me-2"></i>Date de fin prévue</h6>
                        <p>${data.date_fin_prevue_formatted}</p>
                    </div>
                    
                    <div class="info-card">
                        <h6><i class="fas fa-tasks me-2"></i>Statut</h6>
                        <p><span class="badge bg-${data.statut_class}">${
                data.statut_text
            }</span></p>
                    </div>
                </div>
                
                <div class="info-card mb-4">
                    <h6><i class="fas fa-chart-line me-2"></i>Progression</h6>
                    <div class="progress-container">
                        <div class="progress-bar-custom" style="width: ${
                            data.progression
                        }%;">${data.progression}%</div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small>Début du projet</small>
                        <small>Objectif</small>
                    </div>
                </div>
                
                <h5 class="section-title"><i class="fas fa-file-alt me-2"></i>Documents</h5>
            `;

            if (data.documents && data.documents.length > 0) {
                html += `<div class="mb-4">`;
                data.documents.forEach((doc) => {
                    const iconClass =
                        doc.type === "pdf"
                            ? "doc-pdf"
                            : doc.type === "word"
                            ? "doc-word"
                            : doc.type === "excel"
                            ? "doc-excel"
                            : "doc-other";

                    const iconType =
                        doc.type === "pdf"
                            ? "fa-file-pdf"
                            : doc.type === "word"
                            ? "fa-file-word"
                            : doc.type === "excel"
                            ? "fa-file-excel"
                            : "fa-file";

                    html += `
                        <div class="document-card">
                            <div class="doc-icon ${iconClass}">
                                <i class="fas ${iconType}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">${doc.nom}</div>
                                <small class="text-muted">${doc.type.toUpperCase()} Document</small>
                            </div>
                            <a href="${
                                doc.url
                            }" class="btn btn-sm btn-outline-primary" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    `;
                });
                html += `</div>`;
            } else {
                html += `
                    <div class="empty-state mb-4">
                        <i class="fas fa-file-circle-exclamation"></i>
                        <h5 class="mt-3">Aucun document disponible</h5>
                        <p class="text-muted">Aucun document n'a été ajouté à ce projet.</p>
                    </div>
                `;
            }

            html += `<h5 class="section-title"><i class="fas fa-users me-2"></i>Équipes et membres</h5>`;

            if (data.equipes && data.equipes.length > 0) {
                data.equipes.forEach((equipe) => {
                    html += `
                        <div class="team-card mb-4">
                            <div class="team-header">
                                <div class="team-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="team-title">${
                                    equipe.nom || "Nom non spécifié"
                                }</h5>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Membres (${equipe.utilisateurs.length})</h6>
                    `;

                    if (equipe.utilisateurs && equipe.utilisateurs.length > 0) {
                        html += `
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Fonction</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        equipe.utilisateurs.forEach((user) => {
                            html += `
                                <tr>
                                    <td>${user.name}</td>
                                    <td>${user.fonction || "Non spécifié"}</td>
                                    <td>${user.email}</td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="empty-state p-3">
                                <i class="fas fa-user-slash"></i>
                                <p class="mb-0 mt-2">Aucun membre dans cette équipe</p>
                            </div>
                        `;
                    }

                    html += `</div></div>`;
                });
            } else {
                html += `
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h5 class="mt-3">Aucune équipe assignée</h5>
                        <p class="text-muted">Aucune équipe n'est actuellement assignée à ce projet.</p>
                    </div>
                `;
            }

            $("#projetDetailsContent").html(html);
        }).fail(function () {
            $("#projetDetailsContent").html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Une erreur s'est produite lors du chargement des détails du projet.
                </div>
            `);
        });
    });
});
