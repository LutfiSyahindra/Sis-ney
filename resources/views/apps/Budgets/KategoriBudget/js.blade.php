<script>
    $(document).ready(function() {

        function formatRupiah(angka) {
            return Number(angka).toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0, // hilangkan .00
                maximumFractionDigits: 0 // hilangkan .00
            }).replace(/^Rp\s?/, 'Rp ');
        }

        function cleanRupiah(rp) {
            return rp.replace(/[^0-9]/g, '');
        }

        $('#jumlah_visible').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#jumlah').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });

        let kategoriBudgetIndex = 1;

        function loadKategoriTransaksi(selectElement, selectedId = null) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("KategoriBudgets.KategoriBudgets.getKategoriTransaksi") }}',
                    type: 'GET',
                    success: function(response) {
                        selectElement.empty().append(
                            '<option value="">Pilih Kategori</option>');

                        response.forEach(function(item) {
                            const isSelected = (item.id == selectedId) ?
                                'selected' : '';
                            selectElement.append(
                                `<option value="${item.id}" ${isSelected}>${item.nama_kategori}</option>`
                            );
                        });

                        selectElement.select2({
                            dropdownParent: $('#KategoriBudgetModal'),
                            placeholder: 'Pilih Kategori',
                            allowClear: true,
                            width: '100%'
                        });

                        resolve();
                    },
                    error: function() {
                        alert('Gagal memuat data kategori!');
                        reject();
                    }
                });
            });
        }

        function loadBudget(selectElement, selectedId = null) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("KategoriBudgets.KategoriBudgets.getKategoriBudgets") }}',
                    type: 'GET',
                    success: function(response) {
                        selectElement.empty().append(
                            '<option value="">Pilih Budget</option>'
                        );

                        const namaBulan = [
                            '', // agar index mulai dari 1
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November',
                            'Desember'
                        ];

                        response.forEach(function(item) {
                            const bulan = parseInt(item.bulan); // pastikan integer
                            const tahun = item.tahun;
                            const namaBulanTahun = `${namaBulan[bulan]} ${tahun}`;

                            const isSelected = (item.id == selectedId) ?
                                'selected' : '';
                            selectElement.append(
                                `<option value="${item.id}" ${isSelected}>${namaBulanTahun}</option>`
                            );
                        });

                        selectElement.select2({
                            dropdownParent: $('#KategoriBudgetModal'),
                            placeholder: 'Pilih Budget',
                            allowClear: true,
                            width: '100%'
                        });

                        resolve();
                    },
                    error: function() {
                        alert('Gagal memuat data budget!');
                        reject();
                    }
                });
            });
        }


        // table
        let KategoriBudgetsTable = $('#dataKategoriBudgets').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("KategoriBudgets.KategoriBudgets.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'budget_id',
                    name: 'budget_id',
                },
                {
                    data: 'kategori_id',
                    name: 'kategori_id'
                },
                {
                    data: 'jumlah',
                    name: 'jumlah',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'terpakai',
                    name: 'terpakai',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                }, // Total pengeluaran
                {
                    data: 'sisa',
                    name: 'sisa',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Tambah baris baru
        $('#addKategoriBudgetRow').on('click', function() {
            let newRow = $('.kategori-budget-row').first().clone();

            newRow.find('select, input').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, `[${kategoriBudgetIndex}]`);
                    $(this).attr('name', newName);
                }

                $(this).val('').trigger('change'); // Kosongkan select & input
            });

            // Bersihkan Select2 dari clone sebelumnya agar tidak crash
            newRow.find('.kategori-select').next('.select2').remove();
            newRow.find('.budget-select').next('.select2').remove();

            newRow.find('.btn-remove').show();

            $('#kategori-budget-wrapper').append(newRow);

            loadKategoriTransaksi(newRow.find('.kategori-select'));
            loadBudget(newRow.find('.budget-select'));

            kategoriBudgetIndex++;
        });


        // Hapus baris
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.kategori-budget-row').remove();
        });

        // Format Rp saat input jumlah
        $(document).on('input', '.jumlah-visible', function() {
            const raw = cleanRupiah(this.value);
            const formatted = formatRupiah(raw);
            $(this).val(formatted);

            $(this).closest('.kategori-budget-row').find('.jumlah-hidden').val(raw);
        });

        $("#KategoriBudgetForm").submit(function(e) {
            e.preventDefault();

            let KategoriBudgetId = $("#KategoriBudgetId").val();
            let url = KategoriBudgetId ? "/apps/budgets/KategoriBudgets/" + KategoriBudgetId +
                "/update" :
                "/apps/budgets/KategoriBudgets/store";
            let type = KategoriBudgetId ? "PUT" : "POST";

            let formData = $('#KategoriBudgetForm').serialize();

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
                        title: response.message ||
                            'ADD Kategori Budget Bulanan berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#KategoriBudgetModal").modal("hide");
                    KategoriBudgetsTable.ajax.reload();
                },
                error: function(xhr) {
                    let errorMessage = "Terjadi kesalahan!";

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.code === "budget_over") {
                            // Jika kategori budget melebihi budget utama
                            Swal.fire({
                                title: 'Total melebihi budget utama!',
                                text: xhr.responseJSON.message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, update budget',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Tambahkan force_update=true dan kirim ulang form
                                    let form = $('#KategoriBudgetForm');

                                    // Tambahkan field hidden jika belum ada
                                    if (form.find('input[name="force_update"]')
                                        .length === 0) {
                                        form.append(
                                            '<input type="hidden" name="force_update" value="1">'
                                        );
                                    } else {
                                        form.find('input[name="force_update"]').val(
                                            "1");
                                    }

                                    form.submit(); // submit ulang
                                }
                            });
                            return; // jangan tampilkan swal error biasa
                        }

                        if (xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors)
                                .map(msg => msg.join('<br>'))
                                .join('<br>');
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
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

        window.addKategoriBudgets = function() {
            $("#KategoriBudgetModalLabel").html("Kategori Budgets");
            $('#submitKategoriBudget').text('Add Budget');
            $("#KategoriBudgetForm")[0].reset();
            $("#KategoriBudgetId").val(""); // Pastikan input ID kosong untuk mode tambah
            loadKategoriTransaksi($('.kategori-select').first());
            loadBudget($('.budget-select').first());
            $("#KategoriBudgetModal").modal("show");
        }

        window.editKategoriBudgets = async function(id) {
            const modal = $('#KategoriBudgetModal');

            $('#KategoriBudgetForm')[0].reset();
            $('#submitKategoriBudget').text('Update');
            $('#KategoriBudgetForm').find('#KategoriBudgetId').val(id);

            // ⏩ Tampilkan modal duluan supaya terasa cepat
            modal.modal('show');

            try {
                const item = await $.get(`/apps/budgets/KategoriBudgets/${id}/edit`);
                console.log('Kategori Budget:', item);

                const wrapper = $('#kategori-budget-wrapper .kategori-budget-row').first();

                // Load budget & kategori TANPA menunggu satu sama lain
                loadBudget(wrapper.find('.budget-select'), item.budget_id);
                loadKategoriTransaksi(wrapper.find('.kategori-select'), item.kategori_id);

                wrapper.find('.jumlah-visible').val(formatRupiah(item.jumlah));
                wrapper.find('.jumlah-hidden').val(item.jumlah);
                wrapper.find('.btn-remove').hide();

            } catch (err) {
                console.error('Gagal mengambil data kategori budget', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat mengambil data kategori budget.',
                });
                modal.modal('hide');
            }
        };

        window.deleteKategoriBudgets = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Kategori Budget Bulanan ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("KategoriBudgets.KategoriBudgets.destroy", ":id") }}"
                            .replace(
                                ':id',
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
                                KategoriBudgetsTable.ajax.reload(); // Reload DataTables
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
                                'Terjadi kesalahan saat menghapus kategori budget bulanan.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


    })
</script>
