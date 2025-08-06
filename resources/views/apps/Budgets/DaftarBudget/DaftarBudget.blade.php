@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalDaftarBudgetContent")
    <form id="daftarBudgetForm">
        @csrf
        <input type="hidden" name="daftarBudgetId" id="daftarBudgetId">

        <div class="mb-3">
            <label for="bulan" class="form-label">Bulan</label>
            <select class="form-control" id="bulan" name="bulan" required>
                <option value="">Pilih Bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">
                        {{ \Carbon\Carbon::create()->month($i)->locale("id")->translatedFormat("F") }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <select class="form-control" id="tahun" name="tahun" required>
                <option value="">Pilih Tahun</option>
                @for ($i = now()->year; $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label for="total" class="form-label">Total Budget</label>
            <input type="text" class="form-control" id="total_visible" name="total_visible" placeholder="Masukkan total"
                required>
            <input type="hidden" name="total" id="total">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="submitDaftarBudget" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Budgets.DaftarBudget.DaftarBudgetModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Budgets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Budget</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Budgets</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addBudgets()"><i class="ri-user-add-fill"></i>
                            Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataBudgets" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Jumlah Budget</th>
                                    <th>Terpakai</th>
                                    <th>Sisa</th>
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
    @include("apps.Budgets.DaftarBudget.js")
@endpush
