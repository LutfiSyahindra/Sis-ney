<script>
    let startDate = moment().startOf('month');
    let endDate = moment().endOf('month');

    let pengeluaranPerHariChartInstance = null;
    let pengeluaranPerBulanChartInstance = null;

    function loadDataWithRange(start, end) {
        const colors = {
            bodyColor: '#333',
            cardBg: '#fff',
            primary: '#0d6efd',
            gridBorder: '#e0e0e0',
            muted: '#888'
        };
        const fontFamily = 'Arial, sans-serif';
        // Widget Pengeluaran
        fetch(`/apps/dashboard/pengeluaran?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                console.log(data);
                document.getElementById('pengeluaranTotal').innerText = data.total;
                const naik = data.status_selisih === 'naik';
                const turun = data.status_selisih === 'turun';
                const persenEl = document.getElementById('pengeluaranPersen');
                persenEl.innerHTML = `
                    <p class="${naik ? 'text-danger' : (turun ? 'text-success' : 'text-secondary')}">
                        <span>${naik ? '+' : (turun ? '-' : '')}${data.selisih}</span>
                        <i data-feather="${naik ? 'arrow-up' : (turun ? 'arrow-down' : 'minus')}" class="icon-sm mb-1"></i>
                    </p>
                `;
                feather.replace();
            });


        // Chart Pengeluaran
        fetch(`/apps/dashboard/pengeluaran?start=${start}&end=${end}`)
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
                        data: data.chart
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
        fetch(`/apps/dashboard/pemasukan?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                console.log('pemasukan = ', data.selisih)
                document.getElementById('pemasukanTotal').innerText = data.total;
                const naik = data.status_selisih === 'naik';
                const turun = data.status_selisih === 'turun';
                const persenEl = document.getElementById('pemasukanPersen');
                persenEl.innerHTML = `
                    <p class="${naik ? 'text-success' : (turun ? 'text-danger' : 'text-secondary')}">
                        <span>${naik ? '+' : (turun ? '-' : '')}${data.selisih}</span>
                        <i data-feather="${naik ? 'arrow-up' : (turun ? 'arrow-down' : 'minus')}" class="icon-sm mb-1"></i>
                    </p>
                `;
                feather.replace();
            });

        // Chart Pemasukan
        fetch(`/apps/dashboard/pemasukan?start=${start}&end=${end}`)
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
                        data: data.chart
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


        // Widget Budgets
        fetch(`/apps/dashboard/budgets?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                console.log(data);

                // Tampilkan total budget
                document.getElementById('budgetsTotal').innerText = data.jumlah_budget;

                // Cek apakah naik atau turun
                const naik = data.status_selisih === 'naik';
                const turun = data.status_selisih === 'turun';

                // Update elemen persentase/selisih
                const persenEl = document.getElementById('budgetsPersen');
                persenEl.innerHTML = `
                    <p class="${naik ? 'text-danger' : (turun ? 'text-success' : 'text-secondary')}">
                        <span>${naik ? '+' : (turun ? '-' : '')}${data.selisih}</span>
                        <i data-feather="${naik ? 'arrow-up' : (turun ? 'arrow-down' : 'minus')}" class="icon-sm mb-1"></i>
                    </p>
                `;

                feather.replace();
            });

        fetch(`/apps/dashboard/budgets?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                const chartEl = document.querySelector("#budgetChart");
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
                        name: 'Budgets',
                        data: data.chart
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
                    colors: ['#ffc107'],
                    tooltip: {
                        y: {
                            formatter: val => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    }
                });

                chart.render();
            });

        // pengeluaranPerhari chart
        fetch(`/apps/dashboard/peengeluaranPerHari?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                if (pengeluaranPerHariChartInstance) {
                    pengeluaranPerHariChartInstance.destroy();
                }
                var charts = {
                    chart: {
                        type: 'bar',
                        height: '318',
                        parentHeightOffset: 0,
                        foreColor: colors.bodyColor,
                        background: colors.cardBg,
                        toolbar: {
                            show: false
                        },
                    },
                    theme: {
                        mode: 'light'
                    },
                    tooltip: {
                        theme: 'light'
                    },
                    colors: [colors.primary],
                    fill: {
                        opacity: .9
                    },
                    grid: {
                        padding: {
                            bottom: -4
                        },
                        borderColor: colors.gridBorder,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    series: [{
                        name: 'Pengeluaran',
                        data: data.chart.data
                    }],
                    xaxis: {
                        type: 'category',
                        categories: data.chart.categories,
                        axisBorder: {
                            color: colors.gridBorder,
                        },
                        axisTicks: {
                            color: colors.gridBorder,
                        },
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Pengeluaran',
                            style: {
                                size: 9,
                                color: colors.muted
                            }
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: 'center',
                        fontFamily: fontFamily,
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0
                        },
                    },
                    stroke: {
                        width: 0
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '10px',
                            fontFamily: fontFamily,
                        },
                        offsetY: -27
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "50%",
                            borderRadius: 4,
                            dataLabels: {
                                position: 'top',
                                orientation: 'vertical',
                            }
                        },
                    },
                };

                pengeluaranPerHariChartInstance = new ApexCharts(
                    document.querySelector("#PengeluaranPerHariChart"),
                    charts
                );
                pengeluaranPerHariChartInstance.render();
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
        });

        // Set default tanggal bulan ini
        window.selectedStartDate = startDate.format('YYYY-MM-DD');
        window.selectedEndDate = endDate.format('YYYY-MM-DD');
        loadDataWithRange(window.selectedStartDate, window.selectedEndDate);
    });
</script>
