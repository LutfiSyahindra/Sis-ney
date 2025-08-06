<div id="rolesFormContainer" style="display: none;">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="assignRolesForm">
                    <input type="hidden" id="user_id" name="user_id">
                    <label for="roles">Pilih Role:</label>
                    <select id="roles" name="roles[]" class="form-control select2" multiple>
                        <!-- Permissions akan dimuat dengan AJAX -->
                    </select>
                    <br>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="tutupRoles">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveRoles">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
