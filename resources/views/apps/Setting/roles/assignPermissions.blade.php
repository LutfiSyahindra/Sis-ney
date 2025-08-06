<div id="permissionFormContainer" style="display: none;">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="assignPermissionsForm">
                    @csrf
                    <input type="hidden" id="role_id" name="role_id">
                    <label for="permissions">Pilih Permissions:</label>
                    <select id="permissions" name="permissions[]" class="form-control select2" multiple>
                        <!-- Permissions akan dimuat dengan AJAX -->
                    </select>
                    <br>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="tutupPermissions">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="savePermissions">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
