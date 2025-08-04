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
    // Dans la section scripts
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

        // Chargement des données via AJAX
        $.get(`/chef-equipe/equipes/${equipeId}/details`, function (data) {
            let html = `
                <div class="mb-4">
                    <h6>${data.nom}</h6>
                    <p class="mb-1"><strong>Niveau:</strong> ${data.niveau}</p>
                    ${
                        data.parent
                            ? `<p class="mb-1"><strong>Équipe parente:</strong> ${data.parent.nom} (Niveau ${data.parent.niveau})</p>`
                            : ""
                    }
                </div>
                
                <div class="mb-4">
                    <h6>Membres (${data.membres.length})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
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
                        <td>${
                            membre.role === "chef_equipe"
                                ? '<span class="badge bg-warning">Chef d\'équipe</span>'
                                : "Membre"
                        }</td>
                        <td>${membre.email}</td>
                    </tr>
                `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                
                ${
                    data.sous_equipes.length > 0
                        ? `
                <div class="mb-3">
                    <h6>Sous-équipes (${data.sous_equipes.length})</h6>
                    <ul class="list-group">
                `
                        : ""
                }
            `;

            data.sous_equipes.forEach((equipe) => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${equipe.nom}
                        <span class="badge bg-primary rounded-pill">${equipe.membres_count} membres</span>
                    </li>
                `;
            });

            if (data.sous_equipes.length > 0) {
                html += `</ul></div>`;
            }

            $("#equipeDetailsContent").html(html);
            $("#equipeDetailsModal").modal("show");
        });
    });
});
