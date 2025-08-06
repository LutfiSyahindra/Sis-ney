@extends("template.partials.app")

@push("style")
    @include("template.plugin.dataTables")
    @include("template.plugin.sweetAlert2")
    @include("template.plugin.select2")
@endpush

@push("modalContent")
    <form class="form" id="registerForm">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Username</label>
            <input type="text" class="form-control" id="name" autocomplete="off" placeholder="Username">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" autocomplete="off" placeholder="Password">
        </div>
        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input id="confirm_password" class="form-control" name="password_confirmation" type="password">
        </div>
        <input id="userId" class="form-control" name="userId" type="hidden">
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="submitUsers" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
@endpush

@section("content")
    @include("apps.Setting.users.modalUsers")

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Users</li>
        </ol>
    </nav>

    @include("apps.Setting.users.assignRoles")

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Users</h6>
                    <div class="text-end mb-3">
                        <button class="btn btn-primary" onclick="addUser()"><i class="ri-user-add-fill"></i> Tambah
                            User</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataUsers" class="table">
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
    @include("apps.Setting.users.js")
@endpush
