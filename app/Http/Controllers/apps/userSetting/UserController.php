<?php

namespace App\Http\Controllers\apps\userSetting;

use App\Http\Controllers\Controller;
use App\Services\Users\rolesService;
use App\Services\Users\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    protected $usersService;
    protected $rolesService;
    public function __construct(UsersService $usersService, rolesService $rolesService)
    {
        $this->rolesService = $rolesService;
        $this->usersService = $usersService;
    }
    /**
     * Display a listing of the resource.
     */
    public function users()
    {
        return view('apps.Setting.users.user', ['pageTitle' => 'Users']);
    }

    public function table(){    
        $users = $this->usersService->getUsersData();
        Log::info($users);
        $dataUsers = [];
        foreach ($users as $u) {
            $dataUsers[] = [
                'id' => $u['id'],
                'name' => $u['name'],
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }

        return DataTables::of($dataUsers)
        ->addIndexColumn()
        ->addColumn('actions', function ($dataUsers) {
            return "
                <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editUsers(" . $dataUsers['id'] . ")'>
                    <i class='ri-edit-2-line'></i>
                </button> 
                <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteUsers(" . $dataUsers['id'] . ")'>
                    <i class='ri-delete-bin-6-line'></i>
                </button>
                <button class='btn btn-sm btn-primary' style='padding: 1px 5px;' onclick='assignRole(" . $dataUsers['id'] . ")'>
                    <i class='ri-user-settings-line'></i>
                </button>
            ";
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Simpan data ke database dengan password yang di-hash
        $this->usersService->createUsers(
            $request->name,
            $request->email,
            $request->password,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Signup successful!',
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
        $users = $this->usersService->findUser($id);
        Log::info($users);
        return response()->json($users);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $users = $this->usersService->findUser($id);
        $users->update($request->only(['name', 'email']));

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
            $user = $this->usersService->findUser($id);
            // Hapus role
            $user->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'user berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listRoles()
    {
        $roles = $this->rolesService->getRolesData();
        return response()->json(['roles' => $roles]);
    }


    public function getUserRoles($id)
    {
        $user = $this->usersService->findUser($id);
        
        // Ambil nama permission, bukan ID
        $assignedRoles = $user->roles()->pluck('name')->toArray();
        
        Log::info($assignedRoles); // Debugging

        return response()->json(['assignedRoles' => $assignedRoles]);
    }
    
    public function attachRoles(Request $request, $userId)
    {
        $user = $this->usersService->findUser($userId);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role tidak ditemukan'
            ], 404);
        }

        $user->syncRoles($request->roles);

        return response()->json([
            'status' => 'success',
            'message' => 'Roles berhasil diperbarui!'
        ]);
    }
}
