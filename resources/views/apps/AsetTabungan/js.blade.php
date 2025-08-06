<script>
    $(document).ready(function() {

        // dynamic inputs
        let inputIndex = 1;
        let startDate = null;
        let endDate = null;

        $(function() {
            $('#dateRange').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' s/d ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                    monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep',
                        'Okt', 'Nov', 'Des'
                    ],
                    firstDay: 1
                },
                autoUpdateInput: false
            }, function(start, end) {
                startDate = start.format('YYYY-MM-DD');
                endDate = end.format('YYYY-MM-DD');
                $('#dateRange').val(startDate + ' s/d ' + endDate);

                // Refresh data setelah pilih tanggal
                if (window.mutasiTabunganId) {
                    mutasiTabungan(window.mutasiTabunganId);
                }
            });
        });

        function formatRupiah(angka) {
            return Number(angka).toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).replace(/^Rp\s?/, 'Rp ');
        }

        function cleanRupiah(rp) {
            return rp.replace(/[^0-9]/g, '');
        }

        // Event global untuk semua input saldo yang bertipe visible
        $(document).on('input', '.input-rupiah-visible', function() {
            const raw = cleanRupiah(this.value);
            const formatted = formatRupiah(raw);
            this.value = formatted;
            $(this).closest('.col-md-3').find('.input-rupiah-hidden').val(raw);
        });

        document.getElementById('add-input').addEventListener('click', function() {
            let newInput = `
            <div class="row g-2 align-items-center kategori-row">
                <div class="col-md-3">
                    <input type="text" name="tabungan[${inputIndex}][nama_tabungan]" class="form-control" placeholder="Nama Tabungan" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control input-rupiah-visible" placeholder="Saldo Tabungan" required>
                    <input type="hidden" name="tabungan[${inputIndex}][saldo]" class="input-rupiah-hidden">
                </div>
                <div class="col-md-3">
                    <select name="tabungan[${inputIndex}][jenis_tabungan]" class="form-select" required>
                        <option value="Tabungan">Tabungan</option>
                        <option value="Uang Tunai">Uang Tunai</option>
                        <option value="Lain-Lain">Lain-Lain</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger remove-input">Hapus</button>
                </div>
            </div>
        `;
            inputIndex++;
            document.getElementById('tabungan-inputs').insertAdjacentHTML('beforeend', newInput);
        });

        document.getElementById('tabungan-inputs').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-input')) {
                e.target.closest('.kategori-row').remove();
            }
        });

        // table
        let tabunganTable = $('#tableMasukTabungan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("asetTabungan.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_tabungan',
                    name: 'nama_tabungan',
                },
                {
                    data: 'saldo',
                    name: 'saldo',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'jenis_tabungan',
                    name: 'jenis_tabungan'
                },
                {
                    data: 'tanggal_pembukaan',
                    name: 'tanggal_pembukaan'
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
        $('#asetTabunganModal').on('hidden.bs.modal', function() {
            console.log('Modal ditutup, reset form.');
            $('#asetTabunganForm')[0].reset();
            $('#tabungan-inputs').html('');
            $('#asetTabunganModalLabel').text('ADD TABUNGAN');
            $('#submitTabungan').text('Submit'); // Atau $('#submitUsers').html('Update');
        });

        $("#asetTabunganForm").submit(function(e) {
            e.preventDefault();

            let tabunganId = $("#tabunganId").val();
            let url = tabunganId ? "/apps/asetTabungan/" + tabunganId + "/update" :
                "/apps/asetTabungan/store";
            let type = tabunganId ? "PUT" : "POST";

            let formData = $('#asetTabunganForm').serialize();

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
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#asetTabunganModal").modal("hide");
                    tabunganTable.ajax.reload();
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

        window.addTabungan = function() {
            $("#asetTabunganModalLabel").html("Tambah Tabungan");
            $("#asetTabunganForm")[0].reset();
            $("#asetTabunganId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#asetTabunganModal").modal("show");
        };

        window.editTabungan = function(id) {
            const modal = $('#asetTabunganModal');
            modal.modal('show');

            $('#asetTabunganForm')[0].reset();
            $('#tabunganId').val(id); // Set ID user

            $.ajax({
                url: `/apps/asetTabungan/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('dataTabungan :', response);
                    $('#asetTabunganModalLabel').text('EDIT TABUNGAN'); // Ubah judul
                    $('#submitTabungan').text(
                        'Update'); // Atau $('#submitUsers').html('Update');

                    // Kosongkan input lama
                    $('#tabungan-inputs').html('');

                    // Isi dengan data yang didapat
                    let html = `
                        <div class="row g-2 align-items-center kategori-row">
                        <div class="col-md-3">
                            <input type="text" name="tabungan[0][nama_tabungan]" class="form-control"  value="${response.nama_tabungan}" placeholder="Nama Tabungan" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="tabungan[0][saldo]" class="form-control" value="${response.saldo}" placeholder="Saldo Tabungan"
                                required>
                        </div>
                        <div class="col-md-3">
                            <select name="tabungan[0][jenis_tabungan]" class="form-select" required>
                                <option value="Tabungan" ${response.jenis_tabungan === 'Tabungan' ? 'selected' : ''}>Tabungan</option>
                                <option value="Uang Tunai" ${response.jenis_tabungan === 'Uang Tunai' ? 'selected' : ''}>Uang Tunai</option>
                                <option value="Lain-Lain" ${response.jenis_tabungan === 'Lain-Lain' ? 'selected' : ''}>Lain-Lain</option>
                            </select>
                        </div>
                        <input type="hidden" name="tabunganId" id="tabunganId" value="${response.id}">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger remove-input">Hapus</button>
                        </div>
                    </div>`;

                    $('#tabungan-inputs').append(html);
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

        window.deleteTabungan = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Tabungan ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("asetTabungan.delete", ":id") }}".replace(':id',
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
                                tabunganTable.ajax.reload(); // Reload DataTables
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

        window.mutasiTabunganId = null;
        window.mutasiTabungan = function(id) {
            window.mutasiTabunganId = id;
            $("#mutasiModalLabel").html("Mutasi Tabungan");

            $.ajax({
                url: `/apps/asetTabungan/mutasiMasuk`,
                method: 'GET',
                data: {
                    id: id,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    const tbodyMasuk = $("#tableMutasiMasuk tbody");
                    tbodyMasuk.empty();

                    if (response.masuk.length === 0) {
                        tbodyMasuk.append(
                            `<tr><td colspan="4" class="text-center">Tidak ada mutasi masuk</td></tr>`
                        );
                    } else {
                        response.masuk.forEach((item, index) => {
                            const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.tanggal_transfer}</td>
                                <td>${item.asal?.nama_tabungan ?? '-'}</td>
                                <td>${formatRupiah(item.jumlah)}</td>
                            </tr>`;
                            tbodyMasuk.append(row);
                        });
                    }

                    const tbodyKeluar = $("#tableMutasiKeluar tbody");
                    tbodyKeluar.empty();

                    if (response.keluar.length === 0) {
                        tbodyKeluar.append(
                            `<tr><td colspan="4" class="text-center">Tidak ada mutasi keluar</td></tr>`
                        );
                    } else {
                        response.keluar.forEach((item, index) => {
                            const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.tanggal_transfer}</td>
                                <td>${item.tujuan?.nama_tabungan ?? '-'}</td>
                                <td>${formatRupiah(item.jumlah)}</td>
                            </tr>`;
                            tbodyKeluar.append(row);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Gagal memuat data mutasi:', xhr);
                }
            });

            $("#mutasiModal").modal("show");
        }

    });
</script>
