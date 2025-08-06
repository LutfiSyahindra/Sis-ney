<script>
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

    function tableDetail(start = null, end = null) {
        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            $('#tableDetail').DataTable().destroy();
        }

        $('#tableDetail').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("budgetPlan.BudgetPlanDetail.tableDetail") }}",
                type: "GET",
                data: function(d) {
                    d.start = start;
                    d.end = end;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'budget_plan',
                    name: 'budget_plan'
                },
                {
                    data: 'nominal',
                    name: 'nominal',
                    render: function(data) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'aset_tabungan',
                    name: 'aset_tabungan'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    function loadBudgetPlan(selectedId = null) {
        return $.ajax({
            url: '{{ route("budgetPlan.budgetPlan.getBudgetPlan") }}',
            type: 'GET',
            success: function(response) {
                console.log(response);
                const asetSelect = $('#budget_plan_id');
                asetSelect.empty().append('<option value="">Pilih Budget Plan</option>');

                response.forEach(function(plan) {
                    const isSelected = (plan.id == selectedId) ? 'selected' : '';
                    asetSelect.append(
                        `<option value="${plan.id}" ${isSelected}>${plan.nama}</option>`
                    );
                });

                // Re-init select2
                asetSelect.select2({
                    dropdownParent: $('#DepositModal')
                });
            },
            error: function() {
                alert('Gagal memuat data aset!');
            }
        });
    }

    function loadAset(selectedId = null) {
        return $.ajax({
            url: '{{ route("transfer.aset") }}',
            type: 'GET',
            success: function(response) {
                const asetSelect = $('#aset_tabungan_id');
                asetSelect.empty()
                    .append('<option value="">Pilih Aset Tabungan</option>');

                response.forEach(function(aset) {
                    const isSelected = (aset.id == selectedId) ? 'selected' : '';
                    asetSelect.append(
                        `<option value="${aset.id}" ${isSelected}>${aset.nama_tabungan}</option>`
                    );
                });
                // Re-init select2
                asetSelect.select2({
                    dropdownParent: $('#DepositModal')
                });
            },
            error: function() {
                alert('Gagal memuat data aset!');
            }
        });
    }

    $(document).ready(function() {
        let startDate = moment().startOf('month');
        let endDate = moment().endOf('month');

        feather.replace();

        $('#dateRangePicker').daterangepicker({
            startDate: startDate,
            endDate: endDate,
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' s/d ',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                fromLabel: 'Dari',
                toLabel: 'Sampai',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ],
                firstDay: 1
            },
            ranges: {
                'Hari Ini': [moment(), moment()],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, function(start, end) {
            window.selectedStartDate = start.format('YYYY-MM-DD');
            window.selectedEndDate = end.format('YYYY-MM-DD');
            tableDetail(window.selectedStartDate, window.selectedEndDate);
        });

        // Panggil tableDetail saat pertama kali halaman dimuat
        window.selectedStartDate = startDate.format('YYYY-MM-DD');
        window.selectedEndDate = endDate.format('YYYY-MM-DD');
        tableDetail(window.selectedStartDate, window.selectedEndDate);

        $('#nominal').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#nominalValue').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });

        $("#DepositForm").submit(function(e) {
            e.preventDefault();

            let BudgetPlanDetailId = $("#BudgetPlanDetailId").val();
            let url = "/apps/budgetPlan/BudgetPlanDetail/" + BudgetPlanDetailId + "/updateDetail";
            let type = "PUT";

            let formData = $('#DepositForm').serialize();

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
                        title: response.message || 'Update Deposit berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#DepositModal").modal("hide");

                    // Optional: reload DataTable
                    $('#tableDetail').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    let errorMessage = "Terjadi kesalahan!";

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Validasi dari Laravel
                            let errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).map(msg => msg.join(
                                '<br>')).join('<br>');
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

        window.editBudgetPlanDetail = async function(id) {
            const modal = $('#DepositModal');

            $('#DepositForm')[0].reset(); // Reset form
            $('#BudgetPlanDetailId').val(id); // Set ID transaksi
            modal.modal('show');

            try {
                // Ambil data transaksi dari server
                const response = await $.get(`/apps/budgetPlan/budgetPlanDetail/${id}/edit`);

                console.log('dataDetail:', response);

                // Isi select Aset dan Kategori terlebih dahulu
                await loadBudgetPlan(response.budget_plan_id);
                await loadAset(response.aset_tabungan_id);

                $('#nominal').val(formatRupiah(response.nominal));
                $('#nominalValue').val(response.nominal);


                // Judul dan tombol
                $('#DepositModalLabel').text('Edit Setoran Budget Plan');
                $('#submitDeposit').text('Update');

            } catch (err) {
                console.error('Gagal mengambil data transaksi', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengambil data transaksi.',
                });
                modal.modal('hide');
            }

        }

        window.deleteBudgetPlanDetail = async function(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Riwayat Detail Plan ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("budgetPlan.BudgetPlanDetail.deleteDetail", ":id") }}"
                            .replace(':id',
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
                                $('#tableDetail').DataTable().ajax.reload(null,
                                    false); // Reload DataTables
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
    });
</script>
