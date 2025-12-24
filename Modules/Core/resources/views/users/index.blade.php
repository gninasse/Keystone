@extends('core::layouts.master')

@section('header', 'Gestion des utilisateurs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Accueil</a></li>
    <li class="breadcrumb-item active" aria-current="page">Gestion des utilisateurs</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Liste des utilisateurs</h3>
    </div>
    <div class="card-body">
        <div id="toolbar">
            <button id="btn-add-user" class="btn btn-primary" data-bs-toggle="tooltip" title="Ajouter un utilisateur">
                <i class="fas fa-plus"></i>
            </button>
            <button id="btn-edit-user" class="btn btn-info" disabled data-bs-toggle="tooltip" title="Modifier">
                <i class="fas fa-edit"></i>
            </button>
            <button id="btn-delete-user" class="btn btn-danger" disabled data-bs-toggle="tooltip" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
            <button id="btn-reset-password" class="btn btn-warning" disabled data-bs-toggle="tooltip" title="Réinitialiser MDP">
                <i class="fas fa-key"></i>
            </button>
            <button id="btn-enable-user" class="btn btn-success" disabled data-bs-toggle="tooltip" title="Activer">
                <i class="fas fa-check"></i>
            </button>
            <button id="btn-disable-user" class="btn btn-secondary" disabled data-bs-toggle="tooltip" title="Désactiver">
                <i class="fas fa-ban"></i>
            </button>
        </div>
        <table id="users-table"
               data-toggle="table"
               data-url="{{ route('core.users.data') }}"
               data-pagination="true"
               data-side-pagination="server"
               data-search="true"
               data-show-refresh="true"
               data-show-columns="true"
               data-toolbar="#toolbar"
               data-click-to-select="true"
               data-single-select="true"
               data-id-field="id"
               data-page-list="[10, 25, 50, 100]">
            <thead>
                <tr>
                    <th data-field="state" data-radio="true"></th>
                    <th data-field="id" data-sortable="true">ID</th>
                    <th data-field="last_name" data-sortable="true">Nom</th>
                    <th data-field="name" data-sortable="true">Prénom</th>
                    <th data-field="user_name" data-sortable="true">Nom d'utilisateur</th>
                    <th data-field="email" data-sortable="true">Email</th>
                    <th data-field="service" data-sortable="true">Service</th>
                    <th data-field="is_active" data-sortable="true" data-formatter="statusFormatter">Statut</th>
                    <th data-field="created_at" data-sortable="true" data-formatter="dateFormatter">Date création</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('core::users._modal')

@stop

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-table/bootstrap-table.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('plugins/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-table/locale/bootstrap-table-fr-FR.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    // Formatter pour le statut
    function statusFormatter(value, row, index) {
        if (value) {
            return '<span class="badge bg-success">Actif</span>';
        } else {
            return '<span class="badge bg-danger">Inactif</span>';
        }
    }

    // Formatter pour la date
    function dateFormatter(value, row, index) {
        if (value) {
            return new Date(value).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        return '-';
    }

    $(document).ready(function() {
        const $table = $('#users-table');
        const $modal = $('#userModal');
        const $form = $('#userForm');
        
        // Boutons Toolbar
        const $btnAdd = $('#btn-add-user');
        const $btnEdit = $('#btn-edit-user');
        const $btnDelete = $('#btn-delete-user');
        const $btnReset = $('#btn-reset-password');
        const $btnEnable = $('#btn-enable-user');
        const $btnDisable = $('#btn-disable-user');

        console.log("init....bootstrap");

        // Configuration du token CSRF pour AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gestion de la sélection du tableau
        $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table load-success.bs.table', function () {
            const selections = $table.bootstrapTable('getSelections');
            const hasSelection = selections.length > 0;
            const isSingle = selections.length === 1;

            $btnEdit.prop('disabled', !isSingle);
            $btnDelete.prop('disabled', !isSingle); // Single select enforced by data-single-select
            $btnReset.prop('disabled', !isSingle);
            
            if (isSingle) {
                const row = selections[0];
                
                // Gestion des boutons Activer/Désactiver (Show/Hide)
                if (row.is_active == 1) {
                    $btnEnable.hide();
                    $btnDisable.show().prop('disabled', false);
                } else {
                    $btnEnable.show().prop('disabled', false);
                    $btnDisable.hide();
                }
            } else {
                // Si aucune sélection ou sélection multiple (non possible ici), cacher les deux
                $btnEnable.hide();
                $btnDisable.hide();
            }
        });

        // Bouton Ajouter
        $btnAdd.click(function() {
            resetForm();
            $('#modalTitle').text('Ajouter un utilisateur');
            $('#user_id').val('');
            $('#password').prop('required', true);
            $('#password_confirmation').prop('required', true);
            $('.password-group').show();
            $('#password-label').addClass('d-none'); // Cacher le label pour l'ajout car mot de passe obligatoire
            $modal.modal('show');
        });

        // Bouton Éditer
        $btnEdit.click(function() {
            const userId = getSelectedId();
            if (userId) editUser(userId);
        });

        // Bouton Supprimer
        $btnDelete.click(function() {
            const userId = getSelectedId();
            if (userId) deleteUser(userId);
        });

        // Bouton Reset Password
        $btnReset.click(function() {
            const userId = getSelectedId();
            if (userId) resetPassword(userId);
        });

        // Bouton Activer
        $btnEnable.click(function() {
            const userId = getSelectedId();
            if (userId) toggleStatus(userId, 'activer');
        });

        // Bouton Désactiver
        $btnDisable.click(function() {
            const userId = getSelectedId();
            if (userId) toggleStatus(userId, 'désactiver');
        });

        function getSelectedId() {
            const selections = $table.bootstrapTable('getSelections');
            if (selections.length === 1) {
                return selections[0].id;
            }
            return null;
        }

        // Soumettre le formulaire
        $form.submit(function(e) {
            e.preventDefault();
            
            // Validation côté client
            if (!validateForm()) {
                return false;
            }

            const userId = $('#user_id').val();
            const url = userId ? `/core/users/${userId}` : '{{ route("core.users.store") }}';
            const method = userId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $form.serialize(),
                beforeSend: function() {
                    $('#btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
                },
                success: function(response) {
                    if (response.success) {
                        $modal.modal('hide');
                        $table.bootstrapTable('refresh');
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            timer: 2000
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        displayErrors(xhr.responseJSON.errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON.message || 'Une erreur est survenue'
                        });
                    }
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer');
                }
            });
        });

        // Fonction pour éditer un utilisateur
        function editUser(userId) {
            $.ajax({
                url: `/core/users/${userId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const user = response.data;
                        $('#modalTitle').text('Modifier un utilisateur');
                        $('#user_id').val(user.id);
                        $('#name').val(user.name);
                        $('#last_name').val(user.last_name);
                        $('#user_name').val(user.user_name);
                        $('#email').val(user.email);
                        $('#service').val(user.service);
                        $('#password').prop('required', false);
                        $('#password_confirmation').prop('required', false);
                        $('.password-group').show();
                        $('#password-label').removeClass('d-none'); // Afficher le label pour l'édition
                        $modal.modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les données'
                    });
                }
            });
        }

        // Fonction pour supprimer un utilisateur
        function deleteUser(userId) {
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
                        url: `/core/users/${userId}`,
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                $table.bootstrapTable('refresh');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé',
                                    text: response.message,
                                    timer: 2000
                                });
                            }
                        },
                        error: function(xhr) {
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

        // Fonction Réinitialiser MDP
        function resetPassword(userId) {
             Swal.fire({
                title: 'Réinitialiser le mot de passe ?',
                text: "Le mot de passe sera réinitialisé par défaut.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, réinitialiser',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/core/users/${userId}/reset-password`,
                        method: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON.message || 'Erreur lors de la réinitialisation'
                            });
                        }
                    });
                }
            });
        }

        // Fonction Toggle Status
        function toggleStatus(userId, action) {
             Swal.fire({
                title: `Voulez-vous ${action} cet utilisateur ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Oui, ${action}`,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/core/users/${userId}/toggle-status`,
                        method: 'POST',
                        success: function(response) {
                            if (response.success) {
                                $table.bootstrapTable('refresh');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: response.message,
                                    timer: 2000
                                });
                            }
                        },
                        error: function(xhr) {
                           Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON.message || 'Erreur changement de statut'
                            });
                        }
                    });
                }
            });
        }

        // Validation côté client
        function validateForm() {
            clearErrors();
            let isValid = true;
            const errors = {};

            // Prénom
            if ($('#name').val().trim() === '') {
                errors.name = ['Le prénom est obligatoire'];
                isValid = false;
            }

            // Nom
            if ($('#last_name').val().trim() === '') {
                errors.last_name = ['Le nom est obligatoire'];
                isValid = false;
            }

            // Nom d'utilisateur
            if ($('#user_name').val().trim() === '') {
                errors.user_name = ["Le nom d'utilisateur est obligatoire"];
                isValid = false;
            }

            // Email
            const email = $('#email').val().trim();
            if (email === '') {
                errors.email = ["L'email est obligatoire"];
                isValid = false;
            } else if (!isValidEmail(email)) {
                errors.email = ["L'email doit être valide"];
                isValid = false;
            }

            // Mot de passe (si création)
            if ($('#user_id').val() === '' || $('#password').val() !== '') {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();

                if ($('#user_id').val() === '' && password === '') {
                    errors.password = ['Le mot de passe est obligatoire'];
                    isValid = false;
                } else if (password !== '' && password.length < 8) {
                    errors.password = ['Le mot de passe doit contenir au moins 8 caractères'];
                    isValid = false;
                } else if (password !== passwordConfirm) {
                    errors.password_confirmation = ['Les mots de passe ne correspondent pas'];
                    isValid = false;
                }
            }

            if (!isValid) {
                displayErrors(errors);
            }

            return isValid;
        }

        // Valider un email
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Afficher les erreurs
        function displayErrors(errors) {
            clearErrors();
            $.each(errors, function(field, messages) {
                const $field = $(`#${field}`);
                $field.addClass('is-invalid');
                $field.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
            });
        }

        // Effacer les erreurs
        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        // Réinitialiser le formulaire
        function resetForm() {
            $form[0].reset();
            clearErrors();
        }

        // Effacer les erreurs lors de la saisie
        $('input', $form).on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    });
</script>
@endpush