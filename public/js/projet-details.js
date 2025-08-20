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
                <h3 class="titre-principal mb-3">${data.nom}</h3>
                    <div class="description-container">
                        <h5 class="description-title">Description Projet</h5>
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
                                    <td>${data.client || "Non spécifié"}</td>
                                </tr>
                                <tr>
                                    <th>Date de début</th>
                                    <td>${data.date_debut_formatted}</td>
                                </tr>
                                <tr>
                                    <th>Date de fin prévue</th>
                                    <td>${data.date_fin_prevue_formatted}</td>
                                </tr>
                                <tr>
                                    <th>Budget</th>
                                    <td>${data.budget || "Non spécifié"}</td>
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
                                            <div class="progress-bar" role="progressbar" style="width: ${
                                                data.progression
                                            }%" 
                                                 aria-valuenow="${
                                                     data.progression
                                                 }" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">${
                                            data.progression
                                        }% complété</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <h5 class="section-title">Documents</h5>
            `;

            if (data.documents && data.documents.length > 0) {
                html += `<ul class="list-group mb-4">`;
                data.documents.forEach((doc) => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file me-2 text-muted"></i>
                                ${doc.nom}
                                <small class="text-muted ms-2">(${doc.type.toUpperCase()})</small>
                            </div>
                            <a href="${
                                doc.url
                            }" class="btn btn-sm btn-outline-primary" download>
                                <i class="fas fa-download"></i> Télécharger
                            </a>
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
                                        <th>Role</th>
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
                                <td colspan="3" class="text-center text-muted py-3">Aucun membre dans cette équipe</td>
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
