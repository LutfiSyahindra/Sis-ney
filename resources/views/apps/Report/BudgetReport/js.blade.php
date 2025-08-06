<script>
    let startDate = moment().startOf('month');
    let endDate = moment().endOf('month');

    function loadDataWithRange(startDateStr, endDateStr) {
        const start = moment(startDateStr);
        const bulan = start.month() + 1;
        const tahun = start.year();

        const tabList = document.getElementById('asetTabList');
        const tabContent = document.getElementById('asetTabContent');

        tabList.innerHTML = '';
        tabContent.innerHTML =
            `<div class="text-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

        fetch(`/apps/report/budgetReport/table?start=${startDateStr}&end=${endDateStr}`)
            .then(res => res.json())
            .then(response => {
                const data = response.data;
                console.log("Data dari Laravel:", data);

                tabList.innerHTML = '';
                tabContent.innerHTML = '';

                if (data.length === 0) {
                    tabContent.innerHTML =
                        `<div class="alert alert-info">Tidak ada data kategori budget untuk bulan ini.</div>`;
                    return;
                }

                // Grouping by budget_id
                const grouped = {};
                const bulanKeyMap = {}; // untuk menyimpan mapping bulan ke total
                data.forEach(item => {
                    const key = item.budget_id;
                    const bulanKey = item.bulan_tahun_key;
                    const belumTeralokasi = response.totalBelumTeralokasiPerBulan || {};
                    const terpakai = response.totalTerpakaiPerBulan || {};
                    const sisa = response.totalSisaPerBulan || {};

                    if (!grouped[key]) {
                        grouped[key] = [];
                    }
                    grouped[key].push(item);

                    // Simpan info total budget dan total alokasi budget
                    if (!bulanKeyMap[bulanKey]) {
                        bulanKeyMap[bulanKey] = {
                            label: key,
                            totalBudget: 0,
                            totalAlokasi: 0,
                            totalBelumTeralokasi: 0,
                            totalTerpakai: 0,
                            totalSisa: 0
                        };
                    }

                    if (item.total_budget_per_bulan && item.total_budget_per_bulan[bulanKey]) {
                        bulanKeyMap[bulanKey].totalBudget = item.total_budget_per_bulan[bulanKey];
                    }

                    if (item.total_alokasi_budget) {
                        bulanKeyMap[bulanKey].total_alokasi_budget = item.total_alokasi_budget || 0;
                    }

                    if (belumTeralokasi[bulanKey] !== undefined) {
                        bulanKeyMap[bulanKey].totalBelumTeralokasi = belumTeralokasi[bulanKey];
                    }

                    if (terpakai[bulanKey] !== undefined) {
                        bulanKeyMap[bulanKey].totalTerpakai = terpakai[bulanKey];
                    }

                    if (sisa[bulanKey] !== undefined) {
                        bulanKeyMap[bulanKey].totalSisa = sisa[bulanKey];
                    }
                });


                let isFirst = true;
                const tabContentsHTML = [];

                Object.entries(grouped).forEach(([key, items], index) => {
                    const tabId = `tab-${index}`;
                    const contentId = `content-${index}`;
                    const tableId = `table-${index}`;
                    const bulanKey = items[0].bulan_tahun_key;

                    // Tab Header
                    const li = document.createElement('li');
                    li.className = 'nav-item';
                    li.innerHTML = `
                    <a class="nav-link ${isFirst ? 'active' : ''}" id="${tabId}" data-bs-toggle="tab"
                    href="#${contentId}" role="tab" aria-controls="${contentId}" aria-selected="${isFirst}"
                    data-bulan-key="${bulanKey}">${key}</a>`;
                    tabList.appendChild(li);

                    // Table Rows
                    const tableRows = items.map((item, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.kategori_id}</td>
                        <td>Rp ${parseFloat(item.jumlah).toLocaleString('id-ID')}</td>
                        <td>Rp ${parseFloat(item.terpakai).toLocaleString('id-ID')}</td>
                        <td>Rp ${parseFloat(item.sisa).toLocaleString('id-ID')}</td>
                        <td>
                            <span class="badge bg-${item.status === 'Over Budget' ? 'danger' : 'success'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>`).join('');

                    tabContentsHTML.push(`
                    <div class="tab-pane fade ${isFirst ? 'show active' : ''}" id="${contentId}" role="tabpanel" aria-labelledby="${tabId}">
                        <div class="table-responsive mt-3">
                            <table id="${tableId}" class="table table-bordered display nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Jumlah Budget</th>
                                        <th>Terpakai</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>${tableRows}</tbody>
                            </table>
                        </div>
                    </div>`);
                    isFirst = false;
                });

                // Container untuk chart di atas tab-content
                const chartContainer = document.createElement('div');
                chartContainer.id = 'chart-budget-container';
                chartContainer.className = 'mb-4';
                tabContent.appendChild(chartContainer);

                // Tab contents
                const tabContentsWrapper = document.createElement('div');
                tabContentsWrapper.className = 'tab-content';
                tabContentsWrapper.innerHTML = tabContentsHTML.join('');
                tabContent.appendChild(tabContentsWrapper);

                // Fungsi render chart
                function renderChart(bulanKey) {
                    const data = bulanKeyMap[bulanKey];
                    if (!data) return;

                    chartContainer.innerHTML = `
                    <div class="col-12 col-xl-12 stretch-card">
                        <div class="row flex-grow-1">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Total Budget ${data.label}</span>
                                            <i data-feather="dollar-sign" class="text-primary" style="font-size: 1.5rem;"></i>
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">Rp ${parseFloat(data.totalBudget.total).toLocaleString('id-ID')}</h6>
                                                <div class="d-flex align-items-baseline" id="pengeluaranPersen-${bulanKey}-1"></div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="chart-pengeluaran-${bulanKey}-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Total Alokasi Budget ${data.label}</span>
                                            <i data-feather="layers" class="text-success" style="font-size: 1.5rem;"></i>
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">Rp ${parseFloat(data.total_alokasi_budget).toLocaleString('id-ID')}</h6>
                                                <div class="d-flex align-items-baseline" id="pengeluaranPersen-${bulanKey}-2"></div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="chart-pengeluaran-${bulanKey}-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Total Belum Teralokasi ${data.label}</span>
                                            <i data-feather="alert-circle" class="text-warning" style="font-size: 1.5rem;"></i>
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">Rp ${parseFloat(data.totalBelumTeralokasi).toLocaleString('id-ID')}</h6>
                                                <div class="d-flex align-items-baseline" id="pengeluaranPersen-${bulanKey}-3"></div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="chart-pengeluaran-${bulanKey}-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Budget Terpakai -->
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Total Budget Terpakai ${data.label}</span>
                                            <i data-feather="layers" class="text-danger" style="width: 24px; height: 24px;"></i>
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">Rp ${parseFloat(data.totalTerpakai).toLocaleString('id-ID')}</h6>
                                                <div class="d-flex align-items-baseline" id="pengeluaranPersen-${bulanKey}-2"></div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="chart-pengeluaran-${bulanKey}-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Total Budget Sisa ${data.label}</span>
                                            <i data-feather="pocket" class="text-success" style="width: 24px; height: 24px;"></i>
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">Rp ${parseFloat(data.totalSisa).toLocaleString('id-ID')}</h6>
                                                <div class="d-flex align-items-baseline" id="pengeluaranPersen-${bulanKey}-3"></div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="chart-pengeluaran-${bulanKey}-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    // Render ulang ikon feather
                    feather.replace();
                }


                // Set chart pertama kali
                const defaultKey = Object.values(grouped)[0][0].bulan_tahun_key;
                renderChart(defaultKey);

                // Inisialisasi DataTable dan setup tab event
                Object.entries(grouped).forEach(([key, items], index) => {
                    const tableId = `#table-${index}`;
                    const tabElement = document.querySelector(`#tab-${index}`);

                    if (tabElement) {
                        tabElement.addEventListener('shown.bs.tab', function(e) {
                            const bulanKey = e.target.getAttribute('data-bulan-key');
                            renderChart(bulanKey);

                            if (!$.fn.DataTable.isDataTable(tableId)) {
                                $(tableId).DataTable({
                                    responsive: true,
                                    scrollX: true
                                });
                            }
                        });
                    }

                    // Inisialisasi DataTable untuk tab aktif pertama
                    if (index === 0) {
                        setTimeout(() => {
                            if (!$.fn.DataTable.isDataTable(tableId)) {
                                $(tableId).DataTable({
                                    responsive: true,
                                    scrollX: true
                                });
                            }
                        }, 10);
                    }
                });
            })
            .catch(err => {
                tabContent.innerHTML = `<div class="alert alert-danger">Gagal memuat data budget.</div>`;
                console.error(err);
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
        });
        // Load data pertama kali saat halaman dimuat
        loadDataWithRange(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));

        // Auto adjust DataTable saat tab dibuka
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const targetTab = event.target.getAttribute('href'); // "#content-0", dll
                const table = document.querySelector(`${targetTab} table`);
                if ($.fn.DataTable.isDataTable(table)) {
                    setTimeout(() => {
                        $(table).DataTable().columns.adjust().draw();
                    }, 200);
                }
            });
        });
        $('#btnDownloadReport').on('click', function() {
            let start = window.selectedStartDate || moment().startOf('month').format('YYYY-MM-DD');
            let end = window.selectedEndDate || moment().endOf('month').format('YYYY-MM-DD');

            const url = "{{ route("budgetReport.BudgetPdf") }}" +
                `?start=${start}&end=${end}`;

            console.log(url);
            window.open(url, '_blank');
        });


    });
</script>
