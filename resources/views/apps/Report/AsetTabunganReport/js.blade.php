<script>
    let startDate = moment().startOf('month');
    let endDate = moment().endOf('month');

    function renderLineChartPerTanggal(elementId, dataArray, label) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerHTML = "";

        const sorted = dataArray.sort((a, b) => new Date(a.tanggal_transaksi) - new Date(b.tanggal_transaksi));
        const labels = sorted.map(item => item.tanggal_transaksi);
        const values = sorted.map(item => parseFloat(item.nominal));

        const chart = new ApexCharts(el, {
            chart: {
                type: "line",
                height: 80,
                sparkline: {
                    enabled: true
                }
            },
            series: [{
                name: label,
                data: values
            }],
            xaxis: {
                categories: labels,
                type: 'category'
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            markers: {
                size: 0
            },
            colors: [label === 'Pemasukan' ? '#28a745' : '#dc3545'],
            tooltip: {
                y: {
                    formatter: val => 'Rp ' + val.toLocaleString('id-ID')
                }
            }
        });

        chart.render();
    }

    function loadDataWithRange(start, end) {
        fetch(`/apps/report/asetTabunganReport/getData?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                const tabList = document.getElementById('asetTabList');
                const tabContent = document.getElementById('asetTabContent');

                tabList.innerHTML = '';
                tabContent.innerHTML = '';

                const grouped = data.reduce((acc, item) => {
                    const asetId = item.aset.id;
                    if (!acc[asetId]) {
                        acc[asetId] = {
                            aset: item.aset,
                            data: []
                        };
                    }
                    acc[asetId].data.push(item);
                    return acc;
                }, {});

                let isFirst = true;

                for (const [asetId, group] of Object.entries(grouped)) {
                    const namaTabungan = group.aset.nama_tabungan;

                    // === Tab Header ===
                    const li = document.createElement('li');
                    li.className = 'nav-item';
                    li.innerHTML = `
                    <a class="nav-link ${isFirst ? 'active' : ''}" id="aset-tab-${asetId}" data-bs-toggle="tab"
                        href="#aset-content-${asetId}" role="tab"
                        aria-controls="aset-content-${asetId}" aria-selected="${isFirst ? 'true' : 'false'}">
                        ${namaTabungan}
                    </a>`;
                    tabList.appendChild(li);

                    // === Tab Content ===
                    const div = document.createElement('div');
                    div.className = `tab-pane fade ${isFirst ? 'show active' : ''}`;
                    div.id = `aset-content-${asetId}`;
                    div.role = 'tabpanel';
                    div.setAttribute('aria-labelledby', `aset-tab-${asetId}`);

                    // Total Pengeluaran & Pemasukan
                    const totalPengeluaran = group.data
                        .filter(item => item.tipe === 'pengeluaran')
                        .reduce((acc, item) => acc + parseFloat(item.nominal), 0);

                    const totalPemasukan = group.data
                        .filter(item => item.tipe === 'pemasukan')
                        .reduce((acc, item) => acc + parseFloat(item.nominal), 0);

                    const chartHTML = `
                <div class="col-12 col-xl-12 stretch-card">
                    <div class="row flex-grow-1">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title mb-0">Pengeluaran</h6>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h4 class="mb-2">Rp ${totalPengeluaran.toLocaleString('id-ID')}</h4>
                                            <div class="d-flex align-items-baseline" id="pengeluaranPersen-${asetId}"></div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div id="chart-pengeluaran-${asetId}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title mb-0">Pemasukan</h6>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h4 class="mb-2">Rp ${totalPemasukan.toLocaleString('id-ID')}</h4>
                                            <div class="d-flex align-items-baseline" id="pemasukanPersen-${asetId}"></div>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <div id="chart-pemasukan-${asetId}" class="mt-md-3 mt-xl-0"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

                    let tableHTML = `
                    <div class="table-responsive mt-3">
                        <table id="tabel-${asetId}" class="table table-bordered display nowrap w-100">
                            <thead>
                                <tr><th>Tanggal</th><th>Kategori</th><th>Nominal</th><th>Tipe</th></tr>
                            </thead>
                            <tbody>`;


                    group.data.forEach(row => {
                        tableHTML += `<tr>
                        <td>${row.tanggal_transaksi}</td>
                        <td>${row.kategori?.nama_kategori || '-'}</td>
                        <td>Rp ${parseFloat(row.nominal).toLocaleString('id-ID')}</td>
                        <td>${row.tipe}</td>
                    </tr>`;
                    });

                    tableHTML += '</tbody></table>';

                    div.innerHTML = chartHTML + tableHTML;
                    tabContent.appendChild(div);

                    // === Render Chart (gunakan ApexCharts, Chart.js, dll) ===
                    const pengeluaranData = group.data.filter(item => item.tipe === 'pengeluaran');
                    const pemasukanData = group.data.filter(item => item.tipe === 'pemasukan');

                    renderLineChartPerTanggal(`chart-pengeluaran-${asetId}`, pengeluaranData, 'Pengeluaran');
                    renderLineChartPerTanggal(`chart-pemasukan-${asetId}`, pemasukanData, 'Pemasukan');

                    setTimeout(() => {
                        $(`#tabel-${asetId}`).DataTable({
                            pageLength: 5,
                            responsive: true,
                            lengthMenu: [5, 10, 25, 50],
                            language: {
                                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                            }
                        });
                    }, 0);

                    isFirst = false;
                }

                if (Object.keys(grouped).length === 0) {
                    tabContent.innerHTML =
                        `<div class="alert alert-info">Tidak ada transaksi pada rentang tanggal ini.</div>`;
                }
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
            loadDataWithRange(window.selectedStartDate, window.selectedEndDate); // ← ini penting
        });
        // Load data pertama kali saat halaman dimuat
        loadDataWithRange(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        $('#btnDownloadReport').on('click', function() {
            let start = window.selectedStartDate || moment().startOf('month').format('YYYY-MM-DD');
            let end = window.selectedEndDate || moment().endOf('month').format('YYYY-MM-DD');

            const url = "{{ route("TransaksiReport.asetTabunganReport.downloadPDF") }}" +
                `?start=${start}&end=${end}`;

            console.log(url);
            window.open(url, '_blank');
        });
    });
</script>
