<script>
    let startDate = moment().startOf('month');
    let endDate = moment().endOf('month');

    function loadDataWithRange(start, end) {
        // Widget Pengeluaran
        fetch(`/apps/report/widgetPengeluaran?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('pengeluaranTotal').innerText = data.total;
                const persenEl = document.getElementById('pengeluaranPersen');
                persenEl.innerHTML = `
                    <p class="${data.naik ? 'text-danger' : 'text-success'}">
                        <span>${data.naik ? '+' : '-'}${data.selisih}</span>
                        <i data-feather="${data.naik ? 'arrow-up' : 'arrow-down'}" class="icon-sm mb-1"></i>
                    </p>`;
                feather.replace();
            });

        // Chart Pengeluaran
        fetch(`/apps/report/chartPengeluaran?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                const chartEl = document.querySelector("#customersChartt");
                chartEl.innerHTML = "";
                const chart = new ApexCharts(chartEl, {
                    chart: {
                        type: "line",
                        height: 60,
                        sparkline: {
                            enabled: true
                        }
                    },
                    series: [{
                        name: "Pengeluaran",
                        data: data.values
                    }],
                    xaxis: {
                        categories: data.labels
                    },
                    stroke: {
                        width: 2,
                        curve: "smooth"
                    },
                    markers: {
                        size: 0
                    },
                    colors: ['#3b76ef'],
                    tooltip: {
                        y: {
                            formatter: val => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    }
                });
                chart.render();
            });

        // Widget Pemasukan
        fetch(`/apps/report/widgetPemasukan?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('pemasukanTotal').innerText = data.total;
                const persenEl = document.getElementById('pemasukanPersen');
                persenEl.innerHTML = `
                    <p class="${data.naik ? 'text-success' : 'text-danger'}">
                        <span>${data.naik ? '+' : '-'}${data.selisih}</span>
                        <i data-feather="${data.naik ? 'arrow-up' : 'arrow-down'}" class="icon-sm mb-1"></i>
                    </p>`;
                feather.replace();
            });

        // Chart Pemasukan
        fetch(`/apps/report/chartPemasukan?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                const chartEl = document.querySelector("#orderssChart");
                chartEl.innerHTML = "";
                const chart = new ApexCharts(chartEl, {
                    chart: {
                        type: "bar",
                        height: 60,
                        sparkline: {
                            enabled: true
                        }
                    },
                    series: [{
                        name: 'Pemasukan',
                        data: data.values
                    }],
                    xaxis: {
                        categories: data.labels
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 2,
                            columnWidth: "60%"
                        }
                    },
                    colors: ['#28a745'],
                    tooltip: {
                        y: {
                            formatter: val => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    }
                });
                chart.render();
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();

        // Inisialisasi Date Range Picker
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
            loadDataWithRange(window.selectedStartDate, window.selectedEndDate);

            // Reload semua DataTable jika sudah ada
            if ($.fn.DataTable.isDataTable('#dataPemasukan')) $('#dataPemasukan').DataTable().ajax
                .reload();
            if ($.fn.DataTable.isDataTable('#dataPengeluaran')) $('#dataPengeluaran').DataTable().ajax
                .reload();
            if ($.fn.DataTable.isDataTable('#dataAll')) $('#dataAll').DataTable().ajax.reload();
        });

        // Set default tanggal bulan ini
        window.selectedStartDate = startDate.format('YYYY-MM-DD');
        window.selectedEndDate = endDate.format('YYYY-MM-DD');
        loadDataWithRange(window.selectedStartDate, window.selectedEndDate);

        $('#btnDownloadReport').on('click', function() {
            let start = window.selectedStartDate || moment().startOf('month').format('YYYY-MM-DD');
            let end = window.selectedEndDate || moment().endOf('month').format('YYYY-MM-DD');

            const url = "{{ route("TransaksiReport.downloadPdf") }}" + `?start=${start}&end=${end}`;

            console.log(url);
            window.open(url, '_blank');
        });
    });
</script>
