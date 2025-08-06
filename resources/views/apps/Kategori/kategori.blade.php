@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
@endpush

@push("modalContent")
    <form id="kategoriForm">
        @csrf
        <div id="kategori-inputs" class="d-flex flex-column gap-3">
            <div class="row g-2 align-items-center kategori-row">
                <div class="col-md-5">
                    <input type="text" name="kategori[0][nama_kategori]" class="form-control" placeholder="Nama Kategori"
                        required>
                </div>
                <div class="col-md-4">
                    <select name="kategori[0][tipe]" class="form-select" required>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <input type="hidden" name="kategoriId" id="kategoriId">
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-danger remove-input">Hapus</button>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-primary" id="add-input">+ Tambah Kategori</button>
            <button type="submit" id="submitKategori" class="btn btn-success">💾 Simpan Semua</button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Kategori.modalKategori")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Kategori</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kategori Transaksi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Kategori Transaksi</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addKategori()"><i class="ri-user-add-fill"></i> Tambah
                            Kategori</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataKategori" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Tipe</th>
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
    @include("apps.Kategori.js")
@endpush
