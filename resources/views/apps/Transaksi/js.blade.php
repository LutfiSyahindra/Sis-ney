<script>
    $(document).ready(function() {

        function loadAsetOptions(selectedId = null) {
            return $.ajax({
                url: '{{ route("transfer.aset") }}',
                type: 'GET',
                success: function(response) {
                    const asetSelect = $('#aset_tabungan_id');
                    asetSelect.empty().append('<option value="">Pilih Aset Tabungan</option>');

                    response.forEach(function(aset) {
                        const isSelected = (aset.id == selectedId) ? 'selected' : '';
                        asetSelect.append(
                            `<option value="${aset.id}" ${isSelected}>${aset.nama_tabungan}</option>`
                        );
                    });
                    // Re-init select2
                    asetSelect.select2({
                        dropdownParent: $('#transaksiModal')
                    });
                },
                error: function() {
                    alert('Gagal memuat data aset!');
                }
            });
        }

        function loadKategoriOptions(selectedId = null) {
            $.ajax({
                url: '{{ route("transaksi.getKategoriTransaksi") }}',
                type: 'GET',
                success: function(response) {
                    const kategoriSelect = $('#kategori_id');
                    kategoriSelect.empty();

                    const grouped = {
                        pemasukan: [],
                        pengeluaran: []
                    };

                    response.forEach(kat => {
                        if (grouped[kat.tipe]) {
                            grouped[kat.tipe].push(kat);
                        }
                    });

                    for (const tipe in grouped) {
                        if (grouped[tipe].length > 0) {
                            const optgroup = $('<optgroup>', {
                                label: tipe.toUpperCase()
                            });
                            grouped[tipe].forEach(kat => {
                                optgroup.append(
                                    `<option value="${kat.id}" data-tipe="${kat.tipe}">${kat.nama_kategori}</option>`
                                );
                            });
                            kategoriSelect.append(optgroup);
                        }
                    }

                    kategoriSelect.select2({
                        dropdownParent: $('#transaksiModal')
                    });

                    // 👇 Tambahkan baris ini agar kategori terpilih saat edit
                    if (selectedId) {
                        kategoriSelect.val(selectedId).trigger('change');
                    }
                },
                error: function() {
                    alert('Gagal memuat data kategori!');
                }
            });
        }

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

        $('#nominal_visible').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#nominal').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });


        let transaksiTable = $('#dataTransaksi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("transaksi.tabel") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal_transaksi',
                    name: 'tanggal_transaksi',
                },
                {
                    data: 'user_id',
                    name: 'user_id'
                },
                {
                    data: 'kategori_id',
                    name: 'kategori_id'
                },
                {
                    data: 'nominal',
                    name: 'nominal',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'tipe',
                    name: 'tipe'
                },
                {
                    data: 'aset_tabungan_id',
                    name: 'aset_tabungan_id'
                },
                {
                    data: 'ket',
                    name: 'ket'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#kategori_id').on('change', function() {
            const tipe = $(this).find('option:selected').data('tipe');
            $('#tipe').val(tipe); // akan ikut terkirim ke server
            $('#tipe_visible').val(tipe); // tampil di UI
        });

        window.addTransaksi = function() {
            $("#transaksiModalLabel").html("Transaksi Tabungan");
            $('#submitTransaksi').text('Add Transaksi');
            $("#transaksiForm")[0].reset();
            $("#transaksiId").val(""); // Pastikan input ID kosong untuk mode tambah
            loadAsetOptions();
            loadKategoriOptions();
            $("#transaksiModal").modal("show");
        }

        $("#transaksiForm").submit(function(e) {
            e.preventDefault();

            let transaksiId = $("#transaksiId").val();
            let url = transaksiId ? "/apps/transaksi/" + transaksiId + "/update" :
                "/apps/transaksi/store";
            let type = transaksiId ? "PUT" : "POST";

            let formData = $('#transaksiForm').serialize();

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
                        title: response.message || 'Transaksi berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#transaksiModal").modal("hide");
                    transaksiTable.ajax.reload();
                },
                error: function(xhr) {
                    let errorMessage = "Terjadi kesalahan!";

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Validasi bawaan Laravel (array)
                            let errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).map(msg => msg.join(
                                '<br>')).join('<br>');
                        } else if (xhr.responseJSON.message) {
                            // Pesan custom manual seperti saldo tidak cukup
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

        window.editTransaksi = async function(id) {
            const modal = $('#transaksiModal');
            modal.modal('show');

            $('#transaksiForm')[0].reset(); // Reset form
            $('#transaksiId').val(id); // Set ID transaksi

            try {
                // Ambil data transaksi dari server
                const response = await $.get(`/apps/transaksi/${id}/edit`);

                console.log('dataTransaksi:', response);

                // Isi select Aset dan Kategori terlebih dahulu
                await loadAsetOptions(response.aset_tabungan_id);
                await loadKategoriOptions(response.kategori_id);

                // Tipe (readonly)
                $('#tipe_visible').val(response.tipe);
                $('#tipe').val(response.tipe);

                // Tanggal
                $('#tanggal_transaksi').val(response.tanggal_transaksi);

                // Nominal (format terlihat + real value)
                $('#nominal_visible').val(formatRupiah(response.nominal));
                $('#nominal').val(response.nominal);

                // Keterangan
                $('#keterangan').val(response.keterangan);

                // Judul dan tombol
                $('#transaksiModalLabel').text('Edit Transaksi');
                $('#submitTransaksi').text('Update');

            } catch (err) {
                console.error('Gagal mengambil data transaksi', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengambil data transaksi.',
                });
                modal.modal('hide');
            }
        };

        window.deleteTransaksi = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Riwayat Transaksi ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("transaksi.destroy", ":id") }}".replace(':id',
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
                                transaksiTable.ajax.reload(); // Reload DataTables
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
                                'Terjadi kesalahan saat menghapus transaksi.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

    })
</script>
