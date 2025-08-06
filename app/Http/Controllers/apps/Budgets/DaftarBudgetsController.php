<?php

namespace App\Http\Controllers\apps\Budgets;

use App\Http\Controllers\Controller;
use App\Services\Budgets\DaftarBudgets\DaftarBudgetsService;
use App\Services\Budgets\KategoriBudgets\KategoriBudgetsService;
use App\Services\Transaksi\transaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DaftarBudgetsController extends Controller
{
    protected $DaftarBudgetsService, $KategoriBudgetsService, $transaksiService;

    public function __construct(DaftarBudgetsService $DaftarBudgetsService, KategoriBudgetsService $KategoriBudgetsService, transaksiService $transaksiService)
    {
        $this->DaftarBudgetsService = $DaftarBudgetsService;
        $this->KategoriBudgetsService = $KategoriBudgetsService;
        $this->transaksiService = $transaksiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function budgets()
    {
        return view('apps.Budgets.DaftarBudget.DaftarBudget', ['pageTitle' => 'Daftar Budgets']);
    }

    public function table()
    {
        $DataBudgets = $this->DaftarBudgetsService->getAllDaftarBudget();
        $allTransaksi = $this->transaksiService->getAllTransaksi(); // semua transaksi
    
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    
        $dataDaftarBudgets = [];
    
        foreach ($DataBudgets as $u) {
            $bulan = (int) $u['bulan'];
            $tahun = (int) $u['tahun'];
            $total = (float) $u['total'];
    
            // Hitung total pengeluaran dari transaksi di bulan & tahun yang sama
            $terpakai = $allTransaksi
                ->filter(function ($t) use ($bulan, $tahun) {
                    return $t->tipe === 'pengeluaran' &&
                        Carbon::parse($t->tanggal_transaksi)->month == $bulan &&
                        Carbon::parse($t->tanggal_transaksi)->year == $tahun;
                })
                ->sum('nominal');
    
            $sisa = $total - $terpakai;
    
            $dataDaftarBudgets[] = [
                'id' => $u['id'],
                'user_id' => $u->user->name,
                'bulan' => $namaBulan[$bulan] ?? 'Tidak Diketahui',
                'tahun' => $tahun,
                'total' => $total,
                'terpakai' => $terpakai,
                'sisa' => $sisa,
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }
    
        return DataTables::of($dataDaftarBudgets)
            ->addIndexColumn()
            ->addColumn('actions', function ($dataDaftarBudgets) {
                return "
                    <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editDaftarBudgets(" . $dataDaftarBudgets['id'] . ")'>
                        <i class='ri-edit-2-line'></i>
                    </button> 
                    <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteDaftarBudgets(" . $dataDaftarBudgets['id'] . ")'>
                        <i class='ri-delete-bin-6-line'></i>
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
        try {
            // Validasi manual (jika kamu ingin menangani error sendiri)
            $validator = Validator::make($request->all(), [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020',
                'total' => 'required|numeric|min:0',
            ]);
    
            if ($validator->fails()) {
                // Jika validasi gagal
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422); // HTTP 422 Unprocessable Entity
            }
    
            // Simpan data
            $budget = $this->DaftarBudgetsService->createDaftarBudget([
                'user_id' => Auth::id(),
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'total' => $request->total,
            ]);
    
            return response()->json([
                'message' => 'Data budget berhasil disimpan',
                'data' => $budget
            ], 201); // HTTP 201 Created
    
        } catch (\Exception $e) {
            // Tangani error sistem/exception
            Log::error('Gagal menyimpan budget: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage(),
            ], 500); // HTTP 500 Internal Server Error
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
    public function edit(string $id)
    {
        $budget = $this->DaftarBudgetsService->findDaftarBudget($id);
        return response()->json($budget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020',
                'total' => 'required|numeric|min:0',
            ]);
    
            // Panggil service
            $budget = $this->DaftarBudgetsService->updateDaftarBudget($id, $validated);
    
            return response()->json([
                'message' => 'Data budget berhasil diperbarui',
                'data' => $budget
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gagal update budget: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $DaftarBudgets = $this->DaftarBudgetsService->findDaftarBudget($id);
            // Hapus role
            $DaftarBudgets->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Budget Bulanan berhasil dihapus.'
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
