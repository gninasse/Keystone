/**
 * Permissions Matrix AJAX Logic
 */
$(function () {
    $('.permission-toggle').change(function () {
        const $checkbox = $(this);
        const roleId = $checkbox.data('role-id');
        const permissionId = $checkbox.data('permission-id');
        const isAttached = $checkbox.is(':checked');

        // Optional: Disable while processing
        $checkbox.prop('disabled', true);

        $.ajax({
            url: route('cores.permissions.toggle'),
            method: 'POST',
            data: {
                role_id: roleId,
                permission_id: permissionId,
                attach: isAttached ? 1 : 0
            },
            success: (response) => {
                if (response.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                }
            },
            error: (xhr) => {
                // Revert check state on error
                $checkbox.prop('checked', !isAttached);

                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: xhr.responseJSON.message || 'Une erreur est survenue'
                });
            },
            complete: () => {
                $checkbox.prop('disabled', false);
            }
        });
    });
});
