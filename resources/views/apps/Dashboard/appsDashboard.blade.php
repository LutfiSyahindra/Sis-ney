@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@section("content")
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Sisney</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group wd-300 me-2 mb-2 mb-md-0 ">
                <span class="input-group-text bg-transparent border-primary">
                    <i class="text-primary" data-feather="calendar"></i>
                </span>
                <input type="text" id="dateRangePicker" class="form-control" />
            </div>
            {{-- <button type="button" class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="printer"></i>
                Print
            </button>
            <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                Download Report
            </button> --}}
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-4 grid-margin stretch-card">
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

                <div class="col-md-4 grid-margin stretch-card">
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

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Budgets</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h4 class="mb-2" id="budgetsTotal">0</h4>
                                    <div class="d-flex align-items-baseline" id="budgetsPersen">
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="budgetChart" class="mt-md-3 mt-xl-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Aset Tabungan</h6>

                    <!-- Slider container -->
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper" id="asetTabunganContainer">
                            <!-- Kartu aset akan dimasukkan via AJAX -->
                        </div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12 col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Pengeluaran Perhari</h6>
                        <div class="dropdown mb-2">
                            <a type="button" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                            </a>
                        </div>
                    </div>
                    <div id="PengeluaranPerHariChart"></div>
                </div>
            </div>
        </div>
    </div> <!-- row -->
@endsection

@push("js")
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @include("apps.Dashboard.js")
@endpush
