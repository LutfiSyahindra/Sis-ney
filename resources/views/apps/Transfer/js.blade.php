<script>
    $(document).ready(function() {

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

        // Khusus input jumlah
        $('#jumlah_visible').on('input', function() {
            const raw = cleanRupiah(this.value);
            const formatted = formatRupiah(raw);
            this.value = formatted;
            $('#jumlah').val(raw); // nilai bersih untuk dikirim ke server
        });

        // table
        let transferTable = $('#dataTransfer').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route("transfer.table") }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal_transfer',
                    name: 'tanggal_transfer',
                },
                {
                    data: 'asal',
                    name: 'asal'
                },
                {
                    data: 'tujuan',
                    name: 'tujuan'
                },
                {
                    data: 'jumlah',
                    name: 'jumlah',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
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

        function loadAsetOptions() {
            $.ajax({
                url: '{{ route("transfer.aset") }}', // Buat route ini di Laravel
                type: 'GET',
                success: function(response) {
                    $('#asal').empty().append('<option value="">Pilih Asal Aset</option>');
                    $('#tujuan').empty().append('<option value="">Pilih Tujuan Aset</option>');

                    response.forEach(function(aset) {
                        $('#asal').append(
                            `<option value="${aset.id}">${aset.nama_tabungan}</option>`
                        );
                        $('#tujuan').append(
                            `<option value="${aset.id}">${aset.nama_tabungan}</option>`
                        );
                    });
                },
                error: function() {
                    alert('Gagal memuat data aset!');
                }
            });
        }

        window.addTransfer = function() {
            $("#transferModalLabel").html("Transfer Tabungan");
            $("#transferForm")[0].reset();
            $("#transferId").val(""); // Pastikan input ID kosong untuk mode tambah
            loadAsetOptions();
            $("#transferModal").modal("show");
        }

        $("#transferForm").submit(function(e) {
            e.preventDefault();

            let transferId = $("#transferId").val();
            let url = transferId ? "/apps/kategoriTransaksi/" + transferId + "/update" :
                "/apps/transfer/store";
            let type = transferId ? "PUT" : "POST";

            let formData = $('#transferForm').serialize();

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
                        title: response.message || 'Transfer berhasil!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    $("#transferModal").modal("hide");
                    transferTable.ajax.reload();
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

        window.deleteTransfer = function(id) {
            // Tampilkan konfirmasi hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Riwayat Transfer ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request DELETE menggunakan AJAX
                    $.ajax({
                        url: "{{ route("transfer.destroy", ":id") }}".replace(':id',
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
                                transferTable.ajax.reload(); // Reload DataTables
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
                                'Terjadi kesalahan saat menghapus transfer.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    })
</script>
