<script>
    $(document).ready(function() {

        // dynamic inputs
        let inputIndex = 1;

        document.getElementById('add-input').addEventListener('click', function() {
            let newInput = `
            <div class="row g-2 align-items-center kategori-row">
                <div class="col-md-5">
                    <input type="text" name="kategori[${inputIndex}][nama_kategori]" class="form-control" placeholder="Nama Kategori" required>
                </div>
                <div class="col-md-4">
                    <select name="kategori[${inputIndex}][tipe]" class="form-select" required>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-danger remove-input">Hapus</button>
                </div>
            </div>
        `;
            inputIndex++;
            document.getElementById('kategori-inputs').insertAdjacentHTML('beforeend', newInput);
        });

        document.getElementById('kategori-inputs').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-input')) {
                e.target.closest('.kategori-row').remove();
            }
        });

        // Inisialisasi DataTable
        let kategoriTable = $('#dataKategori').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("kategoriTransaksi.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'tipe',
                    name: 'tipe'
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
        $('#kategoriModal').on('hidden.bs.modal', function() {
            console.log('Modal ditutup, reset form.');
            $('#kategoriForm')[0].reset();
            $('#kategori-inputs').html('');
            $('#kategoriModalLabel').text('ADD KATEGORI');
            $('#submitKategori').text('Submit'); // Atau $('#submitUsers').html('Update');
        });

        $("#kategoriForm").submit(function(e) {
            e.preventDefault();

            let kategoriId = $("#kategoriId").val();
            let url = kategoriId ? "/apps/kategoriTransaksi/" + kategoriId + "/update" :
                "/apps/kategoriTransaksi/store";
            let type = kategoriId ? "PUT" : "POST";

            let formData = $('#kategoriForm').serialize();

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

                    $("#kategoriModal").modal("hide");
                    kategoriTable.ajax.reload();
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

        window.addKategori = function() {
            $("#kategoriModalLabel").html("Tambah Kategori");
            $("#kategoriForm")[0].reset();
            $("#kategoriId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#kategoriModal").modal("show");
        };

        window.editKategori = function(id) {
            const modal = $('#kategoriModal');
            modal.modal('show');

            $('#kategoriForm')[0].reset();
            $('#kategoriId').val(id); // Set ID user

            $.ajax({
                url: `/apps/kategoriTransaksi/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('dataKategori :', response);
                    $('#usersModalLabel').text('EDIT KATEGORI'); // Ubah judul
                    $('#submitKategori').text(
                        'Update'); // Atau $('#submitUsers').html('Update');

                    // Kosongkan input lama
                    $('#kategori-inputs').html('');

                    // Isi dengan data yang didapat
                    let html = `
                        <div class="row g-2 align-items-center kategori-row">
                            <div class="col-md-5">
                                <input type="text" name="kategori[0][nama_kategori]" class="form-control"
                                    value="${response.nama_kategori}" required>
                            </div>
                            <div class="col-md-4">
                                <select name="kategori[0][tipe]" class="form-select" required>
                                    <option value="pemasukan" ${response.tipe === 'pemasukan' ? 'selected' : ''}>Pemasukan</option>
                                    <option value="pengeluaran" ${response.tipe === 'pengeluaran' ? 'selected' : ''}>Pengeluaran</option>
                                </select>
                            </div>
                            <input type="hidden" name="kategoriId" id="kategoriId" value="${response.id}">
                            <div class="col-md-3 text-end">
                                <button type="button" class="btn btn-danger remove-input">Hapus</button>
                            </div>
                        </div>`;

                    $('#kategori-inputs').append(html);
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

        window.deleteKategori = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Kategori ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("kategoriTransaksi.delete", ":id") }}".replace(':id',
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
                                kategoriTable.ajax.reload(); // Reload DataTables
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
                                'Terjadi kesalahan saat menghapus kategori.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

    });
</script>
