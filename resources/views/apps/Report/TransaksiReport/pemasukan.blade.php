<div class="table-responsive">
    <table id="dataPemasukan" class="table">
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
                <th colspan="4" style="text-align:right">Grand Total:</th>
                <th id="grandTotal"></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</div>

@push("js")
    <script>
        let pemasukanTableInitialized = false;

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

            if (target === '#pemasukan' && !pemasukanTableInitialized) {
                pemasukanTableInitialized = true;

                $('#dataPemasukan').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route("TransaksiReport.tablePemasukan") }}",
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
                        let api = this.api();

                        let total = api.column(4, {
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);

                        $(api.column(4).footer()).html(formatRupiah(total));
                    }
                });
            }
        });
    </script>
@endpush
