$(document).ready(function () {
    // Définir currentEquipeId dans le scope global du script
    let currentEquipeId = null;

    // Récupérer l'ID de l'équipe depuis le stockage local ou les données de la page
    if (typeof window.currentGlobalEquipeId !== "undefined") {
        currentEquipeId = window.currentGlobalEquipeId;
    } else if ($("#currentEquipeId").length) {
        currentEquipeId = $("#currentEquipeId").val();
    }

    // Gestion du clic sur le bouton "Détails"
    $(".view-details-btn, .project-item").click(function (e) {
        e.preventDefault();
        const projectItem = $(this).closest(".project-item");
        const projetId = projectItem.data("id");

        // Afficher le modal avec l'indicateur de chargement
        $("#projetDetailsContent").html(`
                <div class="spinner-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3">Chargement des détails du projet...</p>
                </div>
            `);

        $("#projetDetailsModal").modal("show");

        // Correction de la route - utiliser l'ID du projet
        $.get(`/collaborateur/projets/${projetId}/details`, function (data) {
            let html = `
                    <div class="mb-4">
                        <h3 class="titre-principal mb-3">${data.nom}</h3>
                        <div class="description-container">
                            <h5 class="section-title">Description du projet</h5>
                            <div class="description-content bg-light p-3 rounded">
                                <p class="mb-0">${
                                    data.description ||
                                    "Aucune description disponible"
                                }</p>
                            </div>
                        </div>
                    </div> 
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="section-title">Informations de base</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th>Client</th>
                                        <td>${
                                            data.client || "Non spécifié"
                                        }</td>
                                    </tr>
                                    <tr>
                                        <th>Date de début</th>
                                        <td>${data.date_debut_formatted}</td>
                                    </tr>
                                    <tr>
                                        <th>Date de fin prévue</th>
                                        <td>${
                                            data.date_fin_prevue_formatted
                                        }</td>
                                    </tr>
                                    <tr>
                                        <th>Date de fin réelle</th>
                                        <td>${
                                            data.date_fin_reelle_formatted ||
                                            "Non définie"
                                        }</td>
                                    </tr>
                                    <tr>
                                        <th>Budget</th>
                                        <td>${
                                            data.budget || "Non spécifié"
                                        }</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="section-title">État du projet</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th>Statut</th>
                                        <td><span class="badge bg-${
                                            data.statut_class
                                        }">${data.statut_text}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Progression</th>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" 
                                                    style="width: ${
                                                        data.progression
                                                    }%" 
                                                    aria-valuenow="${
                                                        data.progression
                                                    }" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted">${
                                                data.progression
                                            }% complété</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Créé par</th>
                                        <td>${data.created_by || "Inconnu"}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <h5 class="section-title">Documents importants </h5>
                `;

            // Affichage des documents
            if (data.documents && data.documents.length > 0) {
                html += `<ul class="list-group mb-4">`;
                data.documents.forEach((doc) => {
                    html += `
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="document-icon">
                                        <i class="fas fa-file text-${
                                            doc.type === "pdf"
                                                ? "danger"
                                                : "primary"
                                        }"></i>
                                    </div>
                                    <div class="document-info">
                                        <div class="fw-bold">${doc.nom}</div>
                                        <div class="document-type">${doc.type.toUpperCase()} - ${
                        doc.size
                    }</div>
                                    </div>
                                    <div class="document-actions">
                                        <a href="${
                                            doc.url
                                        }" class="btn btn-sm btn-outline-primary" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        `;
                });
                html += `</ul>`;
            } else {
                html += `
                        <div class="alert alert-light">
                            <i class="fas fa-info-circle me-2"></i>Aucun document disponible pour ce projet
                        </div>
                    `;
            }

            html += `<h5 class="section-title">Équipes impliquées</h5>`;

            // Affichage des équipes impliquées
            if (data.equipes && data.equipes.length > 0) {
                data.equipes.forEach((equipe) => {
                    html += `
                            <div class="mb-4">
                                <h6 class="fw-bold">${
                                    equipe.nom || "Équipe sans nom"
                                }</h6>
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rôle</th>
                                            <th>Nom</th>
                                            <th>Fonction</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                    if (equipe.utilisateurs && equipe.utilisateurs.length > 0) {
                        equipe.utilisateurs.forEach((user) => {
                            html += `
                                    <tr>
                                        <td> ${
                                            user.role === "chef_equipe"
                                                ? '<span class="badge bg-warning text-dark">Chef d\'équipe</span>'
                                                : '<span class="badge bg-primary">Collaborateur</span>'
                                        }
                                        </td>
                                        <td>${user.name}</td>
                                        <td>${user.fonction}</td>
                                        <td>${user.email}</td>
                                    </tr>
                                `;
                        });
                    } else {
                        html += `
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Aucun membre dans cette équipe</td>
                                </tr>
                            `;
                    }

                    html += `</tbody></table></div>`;
                });
            } else {
                html += `
                        <div class="alert alert-light">
                            <i class="fas fa-info-circle me-2"></i>Aucune équipe assignée à ce projet
                        </div>
                    `;
            }
            html += `<h5 class="section-title"> Mes tâches </h5>`;
            const equipeIdParam =
                currentEquipeId ||
                (data.equipes && data.equipes.length > 0
                    ? data.equipes[0].id
                    : "");
            html += `
                <div class="mt-4 text-center">
                    <a href="/collaborateur/projets/${data.id}/taches?equipe_id=${equipeIdParam}" class="btn btn-primary">
                        <i class="fas fa-tasks me-1"></i> Voir mes tâches dans ce projet
                    </a>
                </div>
            `;

            $("#projetDetailsContent").html(html);
        }).fail(function () {
            $("#projetDetailsContent").html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Une erreur s'est produite lors du chargement des détails du projet.
                    </div>
                `);
        });
    });
});
