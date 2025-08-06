<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        let usersTable = $('#dataUsers').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("users.table") }}",
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
        $('#usersModal').on('hidden.bs.modal', function() {
            console.log('Modal ditutup, reset form.');
            $('#registerForm')[0].reset();
            $('#usersModalLabel').text('ADD USERS');
            $('#submitUsers').text('Submit'); // Atau $('#submitUsers').html('Update');
            $('#password').closest('.mb-3').show();
            $('#confirm_password').closest('.mb-3').show().css('display', 'block');
        });

        $("#registerForm").submit(function(e) {
            e.preventDefault();

            let userId = $("#userId").val();
            let url = userId ? "/apps/users/" + userId + "/update" : "/apps/users/store";
            let type = userId ? "PUT" : "POST";

            let formData = {
                _token: $("input[name=_token]").val(),
                name: $("#name").val(),
                email: $("#email").val(),
                password: $("#password").val(),
                password_confirmation: $("#confirm_password").val()
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
                        title: 'User berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#usersModal").modal("hide");
                    usersTable.ajax.reload();
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

        window.addUser = function() {
            $("#usersModalLabel").html("Tambah User");
            $("#registerForm")[0].reset();
            $("#userId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#usersModal").modal("show");
        };

        window.editUsers = function(id) {
            const modal = $('#usersModal');
            modal.modal('show');

            $('#registerForm')[0].reset();
            $('#password_confirmation').hide(); // Sembunyikan password pada edit
            $('#userId').val(id); // Set ID user
            // console.log('id', id);
            $('#confirm_password').closest('.mb-3').hide();
            $('#password').closest('.mb-3').hide();

            $.ajax({
                url: `/apps/users/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('dataUsers :', response);
                    $('#usersModalLabel').text('EDIT USER'); // Ubah judul
                    $('#submitUsers').text('Update'); // Atau $('#submitUsers').html('Update');
                    $('#name').val(response.name);
                    $('#email').val(response.email);
                },
                error: function(xhr) {
                    console.error('Gagal mengambil data user', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch user data. Please try again.',
                    });
                    modal.modal('hide');
                }
            });
        };

        window.deleteUsers = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'User ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("users.delete", ":id") }}".replace(':id',
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
                                usersTable.ajax.reload(); // Reload DataTables
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

        window.assignRole = function(id) {
            let userId = id;
            console.log(userId);
            $('#user_id').val(userId);
            $('#rolesFormContainer').show();

            // Load Permissions yang tersedia
            $.ajax({
                url: '{{ route("users.roles.list") }}',
                method: 'GET',
                success: function(data) {
                    let options = '';
                    data.roles.forEach(function(role) {
                        options +=
                            `<option value="${role.name}">${role.name}</option>`;
                    });
                    $('#roles').html(options).select2();
                }
            });

            // Load Permissions yang sudah dimiliki oleh Role
            $.ajax({
                url: `/apps/users/${id}/roles`,
                method: 'GET',
                success: function(data) {
                    $('#roles').val(data.assignedRoles).trigger('change');
                }
            });
        }

        $('#tutupRoles').on('click', function() {
            // Menyembunyikan div permissionFormContainer
            $('#rolesFormContainer').hide();

            // Mereset form
            $('#assignRolesForm')[0].reset();

            // Mereset select2
            $('#roles').val(null).trigger('change');
        });

        $('#assignRolesForm').on('submit', function(e) {
            e.preventDefault();

            let userId = $('#user_id').val();
            let selectedRoles = $('#roles').val();
            console.log(selectedRoles);

            $.ajax({
                url: `/apps/users/${userId}/rolesAttach`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    roles: selectedRoles
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

                    $('#rolesFormContainer').hide();
                }
            });
        });

    });
</script>
