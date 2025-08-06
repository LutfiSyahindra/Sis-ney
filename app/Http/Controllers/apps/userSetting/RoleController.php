<?php

namespace App\Http\Controllers\apps\userSetting;

use App\Http\Controllers\Controller;
use App\Services\Users\PermissionsService;
use App\Services\Users\rolesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    protected $rolesService;
    protected $permissionsService;

    public function __construct(rolesService $rolesService, PermissionsService $permissionsService)
    {
        $this->rolesService = $rolesService;
        $this->permissionsService = $permissionsService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('apps.Setting.roles.roles', ['pageTitle' => 'Roles']);
    }

    public function table()
    {
        $roles = $this->rolesService->getRolesData();
        $dataRoles = [];
        foreach ($roles as $r) {
            $dataRoles[] = [
                'id' => $r['id'],
                'name' => $r['name'],
                'created_at' => $r['created_at'],
                'updated_at' => $r['updated_at'],
            ];
        }

        return DataTables::of($dataRoles)
        ->addIndexColumn()
        ->addColumn('actions', function ($dataRoles) {
            return '
                <button class="btn btn-sm btn-success" onclick="editRoles(' . $dataRoles['id'] . ')"> <i class=" ri-edit-2-fill "></i></button> 
                <button class="btn btn-sm btn-danger" onclick="deleteRoles(' . $dataRoles['id'] . ')">  <i class=" ri-delete-bin-fill"></i></button>
                <button class="btn btn-sm btn-primary" onclick="assignPermissions(' . $dataRoles['id'] . ')">  <i class=" ri-user-settings-fill"></i></button>
            ';
        })
        ->rawColumns(['actions'])
        ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Simpan data ke database dengan password yang di-hash
        $this->rolesService->createRoles(
            $request->name,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Roles Created successful!',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = $this->rolesService->findRoles($id);
        return response()->json($roles);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roles = $this->rolesService->findRoles($id);
        $roles->update(['name' => $request->name]);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully!',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $roles = $this->rolesService->findRoles($id);
            // Hapus role
            $roles->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'role berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listPermissions()
    {
        $permissions = $this->permissionsService->getPermissionsData();
        return response()->json(['permissions' => $permissions]);
    }

    public function getRolePermissions($id)
    {
        $role = $this->rolesService->findRoles($id);
        
        // Ambil nama permission, bukan ID
        $assignedPermissions = $role->permissions()->pluck('name')->toArray();
        
        Log::info($assignedPermissions); // Debugging

        return response()->json(['assignedPermissions' => $assignedPermissions]);
    }
    
    public function attachPermissions(Request $request, $roleId)
    {
        $role = $this->rolesService->findRoles($roleId);

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role tidak ditemukan'
            ], 404);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions berhasil diperbarui!'
        ]);
    }

}
