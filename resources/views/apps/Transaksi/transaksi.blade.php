@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalTransaksiContent")
    <form id="transaksiForm">
        @csrf
        <input type="hidden" name="transaksiId" id="transaksiId">

        <div class="mb-3">
            <label for="aset_tabungan_id" class="form-label">Aset Tabungan</label>
            <select class="form-control" id="aset_tabungan_id" name="aset_tabungan_id" required>
                <option value="">Pilih Aset Tabungan</option>
                {{-- Options diisi lewat JS --}}
            </select>
        </div>

        <div class="mb-3">
            <label for="kategori_id" class="form-label">Kategori Transaksi</label>
            <select class="form-control" id="kategori_id" name="kategori_id" required>
                <option value="">Pilih Kategori</option>
                {{-- Options diisi lewat JS --}}
            </select>
        </div>

        <div class="mb-3" hidden>
            <label for="tipe_visible" class="form-label">Tipe Transaksi</label>
            <select class="form-control" id="tipe_visible" disabled>
                <option value="">Pilih Tipe</option>
                <option value="pemasukan">Pemasukan</option>
                <option value="pengeluaran">Pengeluaran</option>
            </select>
            <input type="hidden" name="tipe" id="tipe">
        </div>

        <div class="mb-3">
            <label for="tanggal_transaksi" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi"
                value="{{ date("Y-m-d") }}" required>
        </div>

        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="text" class="form-control" id="nominal_visible" placeholder="Masukkan nominal" required>
            <input type="hidden" name="nominal" id="nominal">

        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" class="form-control" id="keterangan" name="keterangan"
                placeholder="Keterangan (opsional)">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="submitTransaksi" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Transaksi.transaksiModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Transaksi</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addTransaksi()"><i class="ri-user-add-fill"></i>
                            Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataTransaksi" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Tipe</th>
                                    <th>Aset</th>
                                    <th>Ket</th>
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
    @include("apps.Transaksi.js")
@endpush
