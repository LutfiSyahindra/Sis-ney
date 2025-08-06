@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section("content")
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Report</a></li>
            <li class="breadcrumb-item active" aria-current="page">Report Transaksi</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group wd-300 me-2 mb-2 mb-md-0">
                <span class="input-group-text bg-transparent border-primary">
                    <i class="text-primary" data-feather="calendar"></i>
                </span>
                <input type="text" id="dateRangePicker" class="form-control" />
            </div>
            <button type="button" id="btnDownloadReport" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                Download Report
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Pengeluaran</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h4 class="mb-2" id="pengeluaranTotal">0</h4>
                                    <div class="d-flex align-items-baseline" id="pengeluaranPersen">
                                        <!-- Akan diisi oleh JS -->
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="customersChartt" height="80"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Pemasukan</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h4 class="mb-2" id="pemasukanTotal">0</h4>
                                    <div class="d-flex align-items-baseline" id="pemasukanPersen">
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="orderssChart" class="mt-md-3 mt-xl-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pengeluaran-tab" data-bs-toggle="tab" href="#pengeluaran"
                                role="tab" aria-controls="pengeluaran" aria-selected="true">Pengeluaran</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pemasukan-tab" data-bs-toggle="tab" href="#pemasukan" role="tab"
                                aria-controls="pemasukan" aria-selected="false">Pemasukan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="all-tab" data-bs-toggle="tab" href="#all" role="tab"
                                aria-controls="all" aria-selected="false">ALL</a>
                        </li>
                    </ul>
                    <div class="tab-content border border-top-0 p-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="pengeluaran" role="tabpanel"
                            aria-labelledby="pengeluaran-tab">
                            @include("apps.Report.TransaksiReport.pengeluaran")
                        </div>
                        <div class="tab-pane fade " id="pemasukan" role="tabpanel" aria-labelledby="pemasukan-tab">
                            @include("apps.Report.TransaksiReport.pemasukan")</div>
                        <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                            @include("apps.Report.TransaksiReport.all")</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push("js")
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include("apps.Report.TransaksiReport.js")
@endpush
