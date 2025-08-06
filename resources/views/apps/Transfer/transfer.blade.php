@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
@endpush

@push("modalTransferContent")
    <form id="transferForm">
        @csrf
        <input type="hidden" name="transferId" id="transferId">
        <div class="mb-3">
            <label for="asal" class="form-label">Asal Aset</label>
            <select class="form-control" id="asal" name="asal">
                <option value="">Pilih Asal Aset</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tujuan" class="form-label">Tujuan Aset</label>
            <select class="form-control" id="tujuan" name="tujuan">
                <option value="">Pilih Tujuan Aset</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah_visible" class="form-label">Jumlah</label>
            <input type="text" class="form-control input-rupiah-visible" id="jumlah_visible" placeholder="Jumlah">
            <input type="hidden" name="jumlah" id="jumlah">
        </div>
        <div class="mb-3">
            <label for="ket" class="form-label">Keterangan</label>
            <input type="text" class="form-control" id="ket" name="ket" autocomplete="off"
                placeholder="Keterangan">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="submitTransfer" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Transfer.transferModal")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Transfer</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transfer Aset</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Transfer Aset</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addTransfer()"><i class="ri-user-add-fill"></i>
                            Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataTransfer" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Asal Aset</th>
                                    <th>Tujuan Aset</th>
                                    <th>Jumlah</th>
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
    @include("apps.Transfer.js")
@endpush
