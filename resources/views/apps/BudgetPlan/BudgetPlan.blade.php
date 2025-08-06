@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalBudgetPlanContent")
    <form id="BudgetPlanForm">
        @csrf
        <input type="hidden" name="BudgetPlanId" id="BudgetPlanId">

        <div class="mb-3">
            <label for="total" class="form-label">Plan</label>
            <input type="text" class="form-control" id="plan" name="plan" placeholder="Masukkan Plan" required>
        </div>

        <div class="mb-3">
            <label for="total" class="form-label">Target</label>
            <input type="text" class="form-control" id="target" name="target" placeholder="Masukkan Nominal Target"
                required>
            <input type="hidden" name="targetValue" id="targetValue">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="submitBudgetPlan" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endpush

@push("modalDepositContent")
    <form id="DepositForm">
        @csrf
        <input type="hidden" name="BudgetPlanDetailId" id="BudgetPlanDetailId">

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

@push("modalDetailContent")
    <div class="table-responsive">
        <table id="tableDetail" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Plan</th>
                    <th>Nominal</th>
                    <th>Dari Aset</th>
                </tr>
            </thead>
        </table>
    </div>
@endpush

@section("content")
    @include("apps.BudgetPlan.BudgetPlanModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Budget</a></li>
            <li class="breadcrumb-item active" aria-current="page">Budget Plan</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Budget Plan</h6>
                    <div class="text-end mb-3">
                        <!-- Tombol Tambah Budget Plan -->
                        <button class="btn btn-success" onclick="addPlan()">
                            <i class="ri-add-circle-line me-1"></i> Tambah
                        </button>

                        <!-- Tombol Setor Uang ke Budget -->
                        <button class="btn btn-primary" onclick="addDeposit()">
                            <i class="ri-wallet-3-line me-1"></i> Setor
                        </button>

                    </div>
                    <div class="table-responsive">
                        <table id="tableBudgetPlan" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Plan</th>
                                    <th>Saldo</th>
                                    <th>Progress</th>
                                    <th>Target</th>
                                    <th>Status</th>
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
    @include("apps.BudgetPlan.js")
@endpush
