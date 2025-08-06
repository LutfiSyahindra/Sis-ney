<?php

namespace App\Http\Controllers\apps\asetTabungan;

use App\Http\Controllers\Controller;
use App\Services\asetTabungan\asetTabunganService;
use App\Services\Transfer\transferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class asetTabunganController extends Controller
{
    protected $asetTabunganService;
    protected $TransferService;

    public function __construct(asetTabunganService $asetTabunganService, transferService $TransferService)
    {
        $this->asetTabunganService = $asetTabunganService;
        $this->TransferService = $TransferService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function view()
    {
        return view('apps.AsetTabungan.asetTabungan', ['pageTitle' => 'Aset Tabungan']);
    }

    public function table(){    
        $tabungan = $this->asetTabunganService->getAllAsetTabungan();
        $dataTabungan = [];
        foreach ($tabungan as $u) {
            $dataTabungan[] = [
                'id' => $u['id'],
                'nama_tabungan' => $u['nama_tabungan'],
                'saldo' => $u['saldo'],
                'jenis_tabungan' => $u['jenis_tabungan'],
                'tanggal_pembukaan' => $u['tanggal_pembukaan'],
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }

        return DataTables::of($dataTabungan)
        ->addIndexColumn()
        ->addColumn('jenis_tabungan', function ($row) {
            $badgeClass = '';

            switch (strtolower($row['jenis_tabungan'])) {
                case 'tabungan':
                    $badgeClass = 'badge bg-primary';
                    break;
                case 'uang tunai':
                    $badgeClass = 'badge bg-success';
                    break;
                default:
                    $badgeClass = 'badge bg-secondary';
                    break;
            }

            return '<span class="' . $badgeClass . '">' . $row['jenis_tabungan'] . '</span>';
        })
        ->addColumn('actions', function ($dataTabungan) {
            return "
                <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editTabungan(" . $dataTabungan['id'] . ")'>
                    <i class='ri-edit-2-line'></i>
                </button> 
                <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteTabungan(" . $dataTabungan['id'] . ")'>
                    <i class='ri-delete-bin-6-line'></i>
                </button>
                <button class='btn btn-sm btn-warning' style='padding: 1px 5px;' onclick='mutasiTabungan(" . $dataTabungan['id'] . ")'>
                    <i class='ri-file-list-3-line'></i>
                </button>
            ";
        })
        ->rawColumns(['actions', 'jenis_tabungan'])
        ->make(true);
    }

    public function getAllAset(){
        return response()->json($this->asetTabunganService->getAllAsetTabungan());
    }

    public function mutasiMasuk(Request $request)
    {
        $id = $request->input('id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
    
        $result = $this->TransferService->getMutasiByAset($id, $start, $end);
    
        return response()->json($result);
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
        $data = $request->input('tabungan');

        Log::info($data);
        foreach ($data as $tabungan) {
            $this->asetTabunganService->createTabungan([
                'nama_tabungan' => $tabungan['nama_tabungan'],
                'user_id' => Auth::id(),
                'saldo' => $tabungan['saldo'],
                'jenis_tabungan' => $tabungan['jenis_tabungan'],
                'keterangan' => 'Masuk',
                'tanggal_pembukaan' => Carbon::now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tabungan Added Successful!',
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
        $asetTabungan = $this->asetTabunganService->findTabungan($id);
        return response()->json($asetTabungan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        Log::info($request);
        $tabungan = $this->asetTabunganService->findTabungan($id);

        $tabungan->update([
            'nama_tabungan' => $request['tabungan'][0]['nama_tabungan'],
            'saldo' => $request['tabungan'][0]['saldo'],
            'jenis_tabungan' => $request['tabungan'][0]['jenis_tabungan'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tabungan updated successfully!',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $user = $this->asetTabunganService->findTabungan($id);
            // Hapus role
            $user->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Aset tabungan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
