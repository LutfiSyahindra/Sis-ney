@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalContent")
    <form class="form" id="rolesForm">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Role</label>
            <input type="text" class="form-control" id="name" autocomplete="off" placeholder="Username">
        </div>
        <input id="roleId" class="form-control" name="roleId" type="hidden">
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="submitRoles" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Setting.roles.modalRoles")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Roles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Roles</li>
        </ol>
    </nav>

    @include("apps.Setting.roles.assignPermissions")
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Roles</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addRoles()"><i class="ri-user-add-fill"></i> Tambah
                            Role</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataRoles" class="table">
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
    @include("apps.Setting.roles.js")
@endpush
