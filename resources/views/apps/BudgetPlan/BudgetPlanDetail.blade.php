@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push("modalDepositContent")
    <form id="DepositForm">
        @csrf
        <input type="text" name="BudgetPlanDetailId" id="BudgetPlanDetailId">

        <div class="mb-3">
            <label for="aset_tabungan_id" class="form-label">Aset Tabungan</label>
            <select class="form-control" id="aset_tabungan_id" name="aset_tabungan_id" required>
                <option value="">Pilih Aset Tabungan</option>
                {{-- Options diisi lewat JS --}}
            </select>
        </div>

        <div class="mb-3">
            <label for="budget_plan_id" class="form-label">Budget Plan</label>
            <select class="form-control" id="budget_plan_id" name="budget_plan_id" required>
                <option value="">Pilih Budget Plan</option>
                {{-- Options diisi lewat JS --}}
            </select>
        </div>

        <div class="mb-3">
            <label for="total" class="form-label">Nominal Setor</label>
            <input type="text" class="form-control" id="nominal" name="nominal" placeholder="Masukkan Nominal Setor"
                required>
            <input type="hidden" name="nominalValue" id="nominalValue">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="submitDeposit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.BudgetPlan.BudgetPlanModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Budget</a></li>
            <li class="breadcrumb-item active" aria-current="page">Budget Plan Detail</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
                        <div class="d-flex align-items-center flex-wrap text-nowrap">
                            <div class="input-group wd-300 me-2 mb-2 mb-md-0">
                                <span class="input-group-text bg-transparent border-primary">
                                    <i class="text-primary" data-feather="calendar"></i>
                                </span>
                                <input type="text" id="dateRangePicker" class="form-control" />
                            </div>
                            <button type="button" id="btnDownloadReport"
                                class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                                <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                                Download Report
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tableDetail" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Plan</th>
                                    <th>Nominal</th>
                                    <th>Dari Aset</th>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include("apps.BudgetPlan.jsDetail")
@endpush
