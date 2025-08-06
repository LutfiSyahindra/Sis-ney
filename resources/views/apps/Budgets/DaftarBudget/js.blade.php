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

        $('#total_visible').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#total').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });

        let DaftarBudgetsTable = $('#dataBudgets').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("budgets.daftarBudgets.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user_id',
                    name: 'user_id',
                },
                {
                    data: 'bulan',
                    name: 'bulan'
                },
                {
                    data: 'tahun',
                    name: 'tahun'
                },
                {
                    data: 'total',
                    name: 'total',
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
                },
                {
                    data: 'sisa',
                    name: 'sisa',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $("#daftarBudgetForm").submit(function(e) {
            e.preventDefault();

            let daftarBudgetId = $("#daftarBudgetId").val();
            let url = daftarBudgetId ? "/apps/budgets/DaftarBudgets/" + daftarBudgetId + "/update" :
                "/apps/budgets/DaftarBudgets/store";
            let type = daftarBudgetId ? "PUT" : "POST";

            let formData = $('#daftarBudgetForm').serialize();

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
                        title: response.message || 'ADD Budget Bulanan berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#DaftarBudgetModal").modal("hide");
                    DaftarBudgetsTable.ajax.reload();
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

        window.addBudgets = function() {
            $("#DaftarBudgetModalLabel").html("Daftar Budgets");
            $('#submitDaftarBudget').text('Add Budget');
            $("#daftarBudgetForm")[0].reset();
            $("#daftarBudgetId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#DaftarBudgetModal").modal("show");
        }

        window.editDaftarBudgets = async function(id) {
            const modal = $('#DaftarBudgetModal');
            modal.modal('show');

            $('#daftarBudgetForm')[0].reset(); // Reset form
            $('#daftarBudgetId').val(id); // Set ID budget

            try {
                // Ambil data budget dari server
                const response = await $.get(`/apps/budgets/DaftarBudgets/${id}/edit`);

                console.log('dataBudget:', response);

                // Isi form
                $('#bulan').val(response.bulan);
                $('#tahun').val(response.tahun);

                // Nominal (total) → tampilkan dalam format rupiah & isi hidden value
                $('#total_visible').val(formatRupiah(response.total));
                $('#total').val(response.total);

                // Judul modal dan tombol
                $('#DaftarBudgetModalLabel').text('Edit Budget Bulanan');
                $('#submitDaftarBudget').text('Update');

            } catch (err) {
                console.error('Gagal mengambil data budget', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengambil data budget.',
                });
                modal.modal('hide');
            }
        }

        window.deleteDaftarBudgets = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Budget Bulanan ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("budgets.daftarBudgets.delete", ":id") }}".replace(
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
                                DaftarBudgetsTable.ajax.reload(); // Reload DataTables
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
                                'Terjadi kesalahan saat menghapus budget bulanan.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


    })
</script>
