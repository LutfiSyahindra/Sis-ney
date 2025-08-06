@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
@endpush

@push("modalContent")
    <form id="asetTabunganForm">
        @csrf
        <div id="tabungan-inputs" class="d-flex flex-column gap-3">
            <div class="row g-2 align-items-center kategori-row">
                <div class="col-md-3">
                    <input type="text" name="tabungan[0][nama_tabungan]" class="form-control" placeholder="Nama Tabungan"
                        required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control input-rupiah-visible" placeholder="Saldo Tabungan" required>
                    <input type="hidden" name="tabungan[0][saldo]" class="input-rupiah-hidden">
                </div>
                <div class="col-md-3">
                    <select name="tabungan[0][jenis_tabungan]" class="form-select" required>
                        <option value="Tabungan">Tabungan</option>
                        <option value="Uang Tunai">Uang Tunai</option>
                        <option value="Lain-Lain">Lain-Lain</option>
                    </select>
                </div>
                <input type="hidden" name="tabunganId" id="tabunganId">
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger remove-input">Hapus</button>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-primary" id="add-input">+ Tambah</button>
            <button type="submit" id="submitTabungan" class="btn btn-success">💾 Simpan Semua</button>
        </div>
    </form>
@endpush

@push("modalMutasiContent")
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Aset Tabungan</h6>

                    {{-- Date Range Picker --}}
                    <div class="row mt-3 mb-2">
                        <div class="col-md-4">
                            <input type="text" id="dateRange" class="form-control" placeholder="Pilih rentang tanggal">
                        </div>
                    </div>

                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs" id="mutasiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk"
                                type="button" role="tab">
                                Mutasi Masuk
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar"
                                type="button" role="tab">
                                Mutasi Keluar
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content mt-3" id="mutasiTabsContent">
                        {{-- Mutasi Masuk --}}
                        <div class="tab-pane fade show active" id="masuk" role="tabpanel">
                            <div class="table-responsive">
                                <table id="tableMutasiMasuk" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Mutasi</th>
                                            <th>Asal Tabungan</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data akan diisi lewat JavaScript atau server --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Mutasi Keluar --}}
                        <div class="tab-pane fade" id="keluar" role="tabpanel">
                            <div class="table-responsive">
                                <table id="tableMutasiKeluar" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Mutasi</th>
                                            <th>Tujuan Tabungan</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data akan diisi lewat JavaScript atau server --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endpush

@section("content")
    @include("apps.AsetTabungan.modal")
    @include("apps.AsetTabungan.modalTransfer")
    @include("apps.AsetTabungan.modalMutasiAsetTabungan")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Aset Tabungan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Aset Tabungan</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Aset Tabungan</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-success" onclick="addTabungan()">
                            <i class="ri-add-fill"></i> Tambah
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tableMasukTabungan" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tabungan</th>
                                    <th>Saldo</th>
                                    <th>Janis Tabungan</th>
                                    <th>Tanggal Tabungan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("js")
    @include("apps.AsetTabungan.js")

    <!-- Moment.js (wajib untuk daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
