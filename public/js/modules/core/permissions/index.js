/**
 * Permissions Matrix AJAX Logic
 */
$(function () {
    // Search Functionality
    $('#permission-search').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $("#permissions-table tbody tr.permission-row").filter(function () {
            // Search in the permission-name cell
            $(this).toggle($(this).find('.permission-name').text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Toggle Change Event (Bootstrap Toggle uses 'change')
    $('.permission-toggle').change(function () {
        const $checkbox = $(this);
        const roleId = $checkbox.data('role-id');
        const permissionId = $checkbox.data('permission-id');
        const isAttached = $checkbox.prop('checked'); // Bootstrap toggle updates underlying checkbox

        // Bootstrap Toggle disables the input, but we can visually disable the toggle if needed
        // For now, we rely on the AJAX speed or add a loading overlay if strictly necessary

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
                // For Bootstrap Toggle, we need to programmatically toggle it back
                $checkbox.bootstrapToggle(isAttached ? 'off' : 'on', true); // true = silent (no event req)

                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: xhr.responseJSON.message || 'Une erreur est survenue'
                });
            }
        });
    });
});
