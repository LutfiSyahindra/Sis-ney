<div class="table-responsive">
    <table id="dataAll" class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>User</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Tipe</th>
                <th>Aset</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Total Pengeluaran:</th>
                <th id="totalPengeluaran"></th>
                <th colspan="3"></th>
            </tr>
            <tr>
                <th colspan="4" style="text-align:right">Total Pemasukan:</th>
                <th id="totalPemasukan"></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</div>

@push("js")
    <script>
        let allTableInitialized = false;

        function formatRupiah(angka) {
            return Number(angka).toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).replace(/^Rp\s?/, 'Rp ');
        }

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('href');

            if (target === '#all' && !allTableInitialized) {
                allTableInitialized = true;

                $('#dataAll').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route("TransaksiReport.tableAll") }}",
                        type: "GET",
                        data: function(d) {
                            d.start = window.selectedStartDate || '';
                            d.end = window.selectedEndDate || '';
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal_transaksi',
                            name: 'tanggal_transaksi'
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
                            render: function(data) {
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
                        }
                    ],
                    footerCallback: function(row, data, start, end, display) {
                        let totalPemasukan = 0;
                        let totalPengeluaran = 0;

                        data.forEach(function(rowData) {
                            let nominal = rowData.nominal;
                            let tipe = rowData.tipe_raw; // ✅ gunakan tipe_raw

                            if (typeof nominal === 'string') {
                                nominal = parseFloat(nominal.replace(/[^\d.-]/g, ''));
                            }

                            if (!isNaN(nominal)) {
                                if (tipe === 'pemasukan') {
                                    totalPemasukan += nominal;
                                } else if (tipe === 'pengeluaran') {
                                    totalPengeluaran += nominal;
                                }
                            }
                        });

                        $('#totalPemasukan').html(formatRupiah(totalPemasukan));
                        $('#totalPengeluaran').html(formatRupiah(totalPengeluaran));
                    }
                });
            }
        });
    </script>
@endpush
