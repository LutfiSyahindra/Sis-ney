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
            <li class="breadcrumb-item active" aria-current="page">Report Aset Tabungan</li>
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
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="asetTabList" role="tablist">
                        {{-- Tab dinamis akan dimasukkan via JavaScript --}}
                    </ul>

                    <div class="tab-content border border-top-0 p-3" id="asetTabContent">
                        {{-- Konten tab dinamis akan dimasukkan via JavaScript --}}
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
    @include("apps.Report.AsetTabunganReport.js")
@endpush
