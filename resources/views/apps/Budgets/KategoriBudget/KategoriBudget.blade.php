@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalKategoriBudgetContent")
    <form id="KategoriBudgetForm">
        @csrf
        <div id="kategori-budget-wrapper">
            <div class="kategori-budget-row row g-2 align-items-end mb-2">
                <div class="col-md-4">
                    <label class="form-label">Budget</label>
                    <select class="form-control budget-select" name="KategoriBudget[0][budget_id]" required>
                        <option value="">Pilih Budget</option>
                        {{-- opsi budget --}}
                    </select>
                </div>

                <input type="hidden" name="KategoriBudgetId" id="KategoriBudgetId">

                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control kategori-select" name="KategoriBudget[0][kategori_transaksi]" required>
                        <option value="">Pilih Kategori</option>
                        {{-- opsi kategori --}}
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jumlah</label>
                    <input type="text" class="form-control jumlah-visible" name="KategoriBudget[0][jumlah_visible]"
                        placeholder="0" required>
                    <input type="hidden" class="jumlah-hidden" name="KategoriBudget[0][jumlah]">
                </div>

                <div class="col-md-2 d-grid">
                    <button type="button" class="btn btn-danger btn-remove" style="display: none;">Hapus</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" id="addKategoriBudgetRow">+ Tambah Baris</button>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="submitKategoriBudget" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Budgets.KategoriBudget.KategoriBudgetModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Kategori Budgets</a></li>
            <li class="breadcrumb-item active" aria-current="page">KategoriBudget</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Kategori Budgets</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addKategoriBudgets()"><i class="ri-user-add-fill"></i>
                            Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataKategoriBudgets" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Periode Budget</th>
                                    <th>Kategori</th>
                                    <th>Jumlah Budget</th>
                                    <th>Terpakai</th>
                                    <th>Sisa</th>
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
    @include("apps.Budgets.KategoriBudget.js")
@endpush
