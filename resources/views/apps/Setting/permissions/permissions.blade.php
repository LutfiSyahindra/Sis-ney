@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
@endpush

@push("modalContent")
    <form class="form" id="permissionsForm">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Permissions</label>
            <input type="text" class="form-control" id="name" autocomplete="off" placeholder="Username">
        </div>
        <input id="permissionsId" class="form-control" name="permissionsId" type="hidden">
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="submitPermissions" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Setting.permissions.modalPermissions")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Permissions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Permissions</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Permissions</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addPermissions()"><i class="ri-user-add-fill"></i> Tambah
                            Permissions</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataPermissions" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
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
    @include("apps.Setting.permissions.js")
@endpush
