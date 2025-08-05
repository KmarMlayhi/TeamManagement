$(document).ready(function () {
    // Activation des tooltips
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: "hover",
        });
    });

    // Confirmation avant suppression
    $('form[method="POST"]').on("submit", function (e) {
        if (
            $(this).find('input[name="_method"][value="DELETE"]').length &&
            !confirm("Êtes-vous sûr de vouloir supprimer cette équipe ?")
        ) {
            e.preventDefault();
        }
    });

    $(".select2-single").select2({
        templateResult: formatEquipe,
        templateSelection: formatEquipe,
    });

    function formatEquipe(equipe) {
        if (!equipe.id) return equipe.text;
        const niveau = $(equipe.element).data("niveau");
        return $("<span>").html(
            '<span style="margin-left:' +
                niveau * 15 +
                'px">' +
                (niveau > 1 ? "└ " : "") +
                equipe.text +
                "</span>"
        );
    }

    // Gestion du clic sur le bouton "Voir"
    $(".view-equipe").click(function (e) {
        e.preventDefault();
        const equipeId = $(this).data("id");

        $("#equipeDetailsContent").html(`
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Chargement des détails de l'équipe...</p>
            </div>
        `);

        $("#equipeDetailsModal").modal("show");

        // Chargement des données via AJAX
        $.get(`/chef-equipe/equipes/${equipeId}/details`, function (data) {
            let html = `
                <div class="mb-4">
                     <h3 class="titre-principal mb-3">${data.nom}</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th>Niveau</th>
                                        <td>${data.niveau}</td>
                                    </tr>
                                    ${
                                        data.parent
                                            ? `<tr>
                                                <th>Équipe parente</th>
                                                <td>${data.parent.nom} (Niveau ${data.parent.niveau})</td>
                                            </tr>`
                                            : ""
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <h5 class="section-title">Membres (${data.membres.length})</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Fonction</th>
                                <th>Rôle</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.membres.forEach((membre) => {
                html += `
                    <tr>
                        <td>${membre.name}</td>
                        <td>${membre.fonction}</td>
                        <td>
                            ${
                                membre.role === "chef_equipe"
                                    ? '<span class="badge bg-warning text-dark">Chef d\'équipe</span>'
                                    : '<span class="badge bg-secondary">Membre</span>'
                            }
                        </td>
                        <td>${membre.email}</td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            if (data.sous_equipes.length > 0) {
                html += `
                    <h5 class="section-title">Sous-équipes (${data.sous_equipes.length})</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Niveau</th>
                                    <th>Membres</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.sous_equipes.forEach((equipe) => {
                    html += `
                        <tr>
                            <td>${equipe.nom}</td>
                            <td>${equipe.niveau}</td>
                            <td>${equipe.membres_count}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            }

            $("#equipeDetailsContent").html(html);
        }).fail(function () {
            $("#equipeDetailsContent").html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Une erreur s'est produite lors du chargement des détails de l'équipe.
                </div>
            `);
        });
    });
});
