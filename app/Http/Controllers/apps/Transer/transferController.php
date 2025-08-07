<?php

namespace App\Http\Controllers\apps\Transer;

use App\Http\Controllers\Controller;
use App\Services\asetTabungan\asetTabunganService;
use App\Services\Transfer\transferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class transferController extends Controller
{
    protected $transferService, $asetTabunganService;

    public function __construct(transferService $transferService, asetTabunganService $asetTabunganService)
    {
        $this->transferService = $transferService;
        $this->asetTabunganService = $asetTabunganService;
    }
    /**
     * Display a listing of the resource.
     */
    public function transfer()
    {
        return view('apps.Transfer.transfer', ['pageTitle' => 'Transfer']);
    }

    public function table(){    
        $transfer = $this->transferService->getAllTransfer();
        $dataTransfer = [];
        foreach ($transfer as $u) {
            $dataTransfer[] = [
                'id' => $u->id,
                'tanggal_transfer' => $u->tanggal_transfer,
                'asal' => $u->asal->nama_tabungan ?? '-',      // akses relasi sebagai object
                'tujuan' => $u->tujuan->nama_tabungan ?? '-',  // akses relasi sebagai object
                'jumlah' => $u->jumlah,
                'ket' => $u->catatan,
                'created_at' => $u->created_at,
            ];
        }

        return DataTables::of($dataTransfer)
        ->addIndexColumn()
        ->addColumn('actions', function ($dataTransfer) {
            return "
                <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteTransfer(" . $dataTransfer['id'] . ")'>
                    <i class='ri-delete-bin-6-line'></i>
                </button>
            ";
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function asetTabunganGetData(Request $request)
    {
        $tabungan = $this->asetTabunganService->getAllAsetTabungan();
        return response()->json($tabungan);
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
        $data = $request->all();
    
        // Validasi input
        $request->validate([
            'asal' => 'required|different:tujuan',
            'tujuan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'ket' => 'nullable|string|max:255',
        ], [
            'asal.different' => 'Asal Aset dan Tujuan Aset tidak boleh sama.',
            'jumlah.required' => 'Jumlah harus diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);
    
        // Ambil aset asal & tujuan
        $asal = $this->asetTabunganService->findTabungan($data['asal']);
        $tujuan = $this->asetTabunganService->findTabungan($data['tujuan']);
    
        if (!$asal || !$tujuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset asal atau tujuan tidak ditemukan.',
            ], 404);
        }
    
        // Cek saldo cukup
        if ($asal->saldo < $data['jumlah']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo aset asal tidak mencukupi untuk melakukan transfer.',
            ], 422);
        }
    
        // Proses pengurangan & penambahan saldo
        DB::beginTransaction();
        try {
            // Simpan transfer
            $this->transferService->createTransfer([
                'user_id' => Auth::id(),
                'asal_aset_id' => $data['asal'],
                'tujuan_aset_id' => $data['tujuan'],
                'jumlah' => $data['jumlah'],
                'tanggal_transfer' => Carbon::now(),
                'catatan' => $data['ket'],
            ]);
    
            // Update saldo
            $asal->saldo -= $data['jumlah'];
            $asal->save();
    
            $tujuan->saldo += $data['jumlah'];
            $tujuan->save();
    
            DB::commit();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Transfer Successful!',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
    
            Log::error('Transfer gagal: ' . $e->getMessage());
    
            return response()->json([
                'status' => 'error',
                'message' => 'Transfer gagal. Silakan coba lagi.',
            ], 500);
        }
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            // Kembalikan saldo yang dihapus ke aset tabungan
            $transfer = $this->transferService->findTransfer($id);
            $asal = $this->asetTabunganService->findTabungan($transfer->asal_aset_id);
            $tujuan = $this->asetTabunganService->findTabungan($transfer->tujuan_aset_id);
            $asal->saldo += $transfer->jumlah;
            $asal->save();
            $tujuan->saldo -= $transfer->jumlah;
            $tujuan->save();

            // Cari role berdasarkan ID
            $user = $this->transferService->findTransfer($id);
            // Hapus role
            $user->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'transfer berhasil dihapus.'
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
