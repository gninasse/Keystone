/**
 * RoleActions.js
 * Handles Edit and Delete actions for Roles.
 */
export class RoleActions {
    constructor(tableInstance, formInstance) {
        this.table = tableInstance;
        this.form = formInstance;
        this.initButtons();
    }

    initButtons() {
        $('#btn-add-role').click(() => {
            this.form.openForAdd();
        });

        $('#btn-edit-role').click(() => {
            const roleId = this.table.getSelectedId();
            if (roleId) this.editRole(roleId);
        });

        $('#btn-delete-role').click(() => {
            const roleId = this.table.getSelectedId();
            if (roleId) this.deleteRole(roleId);
        });
    }

    editRole(roleId) {
        $.ajax({
            url: route('cores.roles.show', roleId),
            method: 'GET',
            success: (response) => {
                if (response.success) {
                    this.form.openForEdit(roleId, response.data);
                }
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les données'
                });
            }
        });
    }

    deleteRole(roleId) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: route('cores.roles.destroy', roleId),
                    method: 'DELETE',
                    success: (response) => {
                        if (response.success) {
                            this.table.refresh();
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé',
                                text: response.message,
                                timer: 2000
                            });
                        }
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON.message || 'Erreur lors de la suppression'
                        });
                    }
                });
            }
        });
    }
}
