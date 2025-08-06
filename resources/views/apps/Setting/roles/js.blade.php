<!-- jQuery harus dimuat dulu -->
<script src="https://unpkg.com/feather-icons"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        let rolesTable = $('#dataRoles').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("roles.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Modal Add Users (Hide) 
        $('#rolesModal').on('hidden.bs.modal', function() {
            console.log('Modal ditutup, reset form.');
            $('#rolesForm')[0].reset();
            $('#rolesModalLabel').text('ADD ROLES');
            $('#submitRoles').text('Submit'); // Atau $('#submitUsers').html('Update');
        });

        $("#rolesForm").submit(function(e) {
            e.preventDefault();

            let roleId = $("#roleId").val();
            let url = roleId ? "/apps/roles/" + roleId + "/update" : "/apps/roles/store";
            let type = roleId ? "PUT" : "POST";

            let formData = {
                _token: $("input[name=_token]").val(),
                name: $("#name").val(),
            };

            console.log(formData);

            $.ajax({
                url: url,
                type: type,
                data: formData,
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Role berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#rolesModal").modal("hide");
                    rolesTable.ajax.reload();
                },
                error: function(xhr) {
                    let errorMessage = "Terjadi kesalahan!";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).map(msg => msg.join('<br>'))
                            .join('<br>');
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMessage,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        });

        window.addRoles = function() {
            $("#rolesModalLabel").html("Tambah Role");
            $("#rolesForm")[0].reset();
            $("#roleId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#rolesModal").modal("show");
        };

        window.editRoles = function(id) {
            const modal = $('#rolesModal');
            modal.modal('show');

            $('#rolesForm')[0].reset();
            $('#roleId').val(id); // Set ID user
            console.log('id', id);

            $.ajax({
                url: `/apps/roles/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('dataRoles :', response);
                    $('#rolesModalLabel').text('EDIT USER'); // Ubah judul
                    $('#submitRoles').text('Update'); // Atau $('#submitUsers').html('Update');
                    $('#name').val(response.name)
                },
                error: function(xhr) {
                    console.error('Gagal mengambil data role', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch role data. Please try again.',
                    });
                    modal.modal('hide');
                }
            });
        };

        window.deleteRoles = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Roles ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("roles.delete", ":id") }}".replace(':id',
                            id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Dihapus!',
                                    response.message,
                                    'success'
                                );
                                rolesTable.ajax.reload(); // Reload DataTables
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus user.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        window.assignPermissions = function(id) {
            let roleId = id;
            console.log(roleId);
            $('#role_id').val(roleId);

            // Menampilkan div permissionFormContainer
            $('#permissionFormContainer').show();

            // Load Permissions yang tersedia
            $.ajax({
                url: '/apps/roles/permissions/list',
                method: 'GET',
                success: function(data) {
                    let options = '';
                    data.permissions.forEach(function(permission) {
                        options +=
                            `<option value="${permission.name}">${permission.name}</option>`;
                    });
                    $('#permissions').html(options).select2();
                }
            });

            // Load Permissions yang sudah dimiliki oleh Role
            $.ajax({
                url: `/apps/roles/${roleId}/permissions`,
                method: 'GET',
                success: function(data) {
                    $('#permissions').val(data.assignedPermissions).trigger('change');
                }
            });
        };

        // Event listener untuk tombol Tutup
        $('#tutupPermissions').on('click', function() {
            // Menyembunyikan div permissionFormContainer
            $('#permissionFormContainer').hide();

            // Mereset form
            $('#assignPermissionsForm')[0].reset();

            // Mereset select2
            $('#permissions').val(null).trigger('change');
        });


        $('#assignPermissionsForm').on('submit', function(e) {
            e.preventDefault();

            let roleId = $('#role_id').val();
            let selectedPermissions = $('#permissions').val();
            console.log(selectedPermissions);

            $.ajax({
                url: `/apps/roles/${roleId}/permissionsAttach`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    permissions: selectedPermissions
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });

                    $('#permissionFormContainer').hide();
                }
            });
        });
    });
</script>
