<!-- jQuery harus dimuat dulu -->
<script src="https://unpkg.com/feather-icons"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        let permissionsTable = $('#dataPermissions').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("permissions.table") }}",
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
        $('#permissionsModal').on('hidden.bs.modal', function() {
            console.log('Modal ditutup, reset form.');
            $('#permissionsForm')[0].reset();
            $('#permissionsModalLabel').text('ADD PERMISSIONS');
            $('#submitPermissions').text('Submit'); // Atau $('#submitUsers').html('Update');
        });

        $("#permissionsForm").submit(function(e) {
            e.preventDefault();

            let permissionsId = $("#permissionsId").val();
            let url = permissionsId ? "/apps/permissions/" + permissionsId + "/update" :
                "/apps/permissions/store";
            let type = permissionsId ? "PUT" : "POST";

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
                        title: 'Permissions berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#permissionsModal").modal("hide");
                    permissionsTable.ajax.reload();
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

        window.addPermissions = function() {
            $("#permissionsModalLabel").html("Tambah Permissions");
            $("#permissionsForm")[0].reset();
            $("#permissionsId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#permissionsModal").modal("show");
        };

        window.editPermissions = function(id) {
            const modal = $('#permissionsModal');
            modal.modal('show');

            $('#permissionsForm')[0].reset();
            $('#permissionsId').val(id); // Set ID user
            console.log('id', id);

            $.ajax({
                url: `/apps/permissions/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('dataRoles :', response);
                    $('#permissionsModalLabel').text('EDIT PERMISSIONS'); // Ubah judul
                    $('#submitPermissions').text(
                        'Update'); // Atau $('#submitUsers').html('Update');
                    $('#name').val(response.name)
                },
                error: function(xhr) {
                    console.error('Gagal mengambil data permissions', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch role data. Please try again.',
                    });
                    modal.modal('hide');
                }
            });
        };

        window.deletePermissions = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Permissions ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("permissions.delete", ":id") }}".replace(':id',
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
                                permissionsTable.ajax.reload(); // Reload DataTables
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

    });
</script>
