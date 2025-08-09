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

        $('#target').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#targetValue').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });

        let BudgetPlanTable = $('#tableBudgetPlan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("budgetPlan.budgetPlan.table") }}",
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
                    data: 'terkumpul',
                    name: 'terkumpul',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'progres',
                    name: 'progres',
                },
                {
                    data: 'target',
                    name: 'target',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $("#BudgetPlanForm").submit(function(e) {
            e.preventDefault();

            let BudgetPlanId = $("#BudgetPlanId").val();
            console.log(BudgetPlanId);
            let url = BudgetPlanId ? "/apps/budgetPlan/budgetPlan/" + BudgetPlanId + "/update" :
                "/apps/budgetPlan/budgetPlan/store";
            let type = BudgetPlanId ? "PUT" : "POST";

            let formData = $('#BudgetPlanForm').serialize();

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

                    $("#BudgetPlanModal").modal("hide");
                    BudgetPlanTable.ajax.reload();
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

        window.addPlan = function() {
            $("#BudgetPlanModalLabel").html("Daftar Budgets");
            $('#submitBudgetPlan').text('Add Budget Plan');
            $("#BudgetPlanForm")[0].reset();
            $("#BudgetPlanId").val(""); // Pastikan input ID kosong untuk mode tambah
            $("#BudgetPlanModal").modal("show");
        }

        window.editBudgetPlan = async function(id) {
            const modal = $('#BudgetPlanModal');
            modal.modal('show');

            $('#BudgetPlanForm')[0].reset(); // Reset form
            $('#BudgetPlanId').val(id); // Set ID budget

            try {
                // Ambil data budget dari server
                const response = await $.get(`/apps/budgetPlan/budgetPlan/${id}/edit`);

                console.log('dataBudgetPlan:', response);

                // Isi form
                $('#plan').val(response.nama);
                // Nominal (total) → tampilkan dalam format rupiah & isi hidden value
                $('#target').val(formatRupiah(response.target));
                $('#targetValue').val(response.target);

                // Judul modal dan tombol
                $('#BudgetPlanModalLabel').text('Edit Budget Bulanan');
                $('#submitBudgetPlan').text('Update');

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

        window.deleteBudgetPlan = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Budget Plan ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("budgetPlan.budgetPlan.destroy", ":id") }}".replace(
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
                                BudgetPlanTable.ajax.reload(); // Reload DataTables
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

        // Deposit
        $('#nominal').on('input', function() {
            let raw = cleanRupiah(this.value);
            $('#nominalValue').val(raw); // nilai bersih disiapkan untuk dikirim
            this.value = formatRupiah(raw); // tampil dalam format rupiah
        });

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
                        dropdownParent: $('#DepositModal'),
                        width: '100%'
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
                        dropdownParent: $('#DepositModal'),
                        width: '100%'
                    });
                },
                error: function() {
                    alert('Gagal memuat data aset!');
                }
            });
        }

        $("#DepositForm").submit(function(e) {
            e.preventDefault();

            let url = "/apps/budgetPlan/budgetPlan/deposit";
            let type = "POST";

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
                        title: response.message || 'ADD Deposit berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#DepositModal").modal("hide");
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

        window.addDeposit = function() {
            $("#DepositModalLabel").html("Deposit Budget Plan");
            $('#submitDeposit').text('Add Deposit Budget Plan');
            $("#DepositForm")[0].reset();
            $("#DepositId").val(""); // Pastikan input ID kosong untuk mode tambah
            loadBudgetPlan();
            loadAset();
            $("#DepositModal").modal("show");
        }

        // Detail Budget Plan

        function tableDetail(id = null) {
            // Cek dan destroy jika DataTable sudah pernah dibuat
            if ($.fn.DataTable.isDataTable('#tableDetail')) {
                $('#tableDetail').DataTable().destroy();
            }

            // Inisialisasi ulang
            $('#tableDetail').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route("budgetPlan.budgetPlanDetail.tableDetail", ":id") }}".replace(':id',
                        id),
                    type: "GET"
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
                        name: 'terkumpul',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'aset_tabungan',
                        name: 'aset_tabungan'
                    },
                ]
            });
        }

        window.detailBudgetPlan = function(id) {
            tableDetail(id);
            $("#DetailModalLabel").html("Detail Budget Plan");
            $("#DetailModal").modal("show");
        }

        window.editBudgetPlanDetail = function(id) {
            const modal = $('#DepositModal');
            modal.modal({
                dropdownParent: $('#DetailModal')
            });
            modal.modal('show');
        }

    })
</script>
