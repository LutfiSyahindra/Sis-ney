<?php

namespace App\Http\Controllers\apps\Budgets;

use App\Http\Controllers\Controller;
use App\Services\Budgets\DaftarBudgets\DaftarBudgetsService;
use App\Services\Budgets\KategoriBudgets\KategoriBudgetsService;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KategoriBudgetsController extends Controller
{
    
    protected $kategoriTransaksiService,$DaftarBudgetsService,$KategoriBudgetsService,$transaksiService;

    public function __construct(kategoriTransaksiService $kategoriTransaksiService, DaftarBudgetsService $DaftarBudgetsService, KategoriBudgetsService $KategoriBudgetsService, transaksiService $transaksiService)
    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
        $this->DaftarBudgetsService = $DaftarBudgetsService;
        $this->KategoriBudgetsService = $KategoriBudgetsService;
        $this->transaksiService = $transaksiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function KategoriBudgets()
    {
        return view('apps.Budgets.KategoriBudget.KategoriBudget', ['pageTitle' => 'Kategori Budgets']);
    }

    public function table()
    {
        $DataKategoriBudgets = $this->KategoriBudgetsService->getAllKategoriBudget();
        $allTransaksi = $this->transaksiService->getAllTransaksi(); // return Collection
        $dataKategoriBudgets = [];
    
        foreach ($DataKategoriBudgets as $u) {
            $budget = $u->budgets;
            $kategori = $u->kategori_transaksi;
    
            $bulan = (int) $budget->bulan;
            $tahun = (int) $budget->tahun;
    
            // Hitung terpakai dari transaksi pengeluaran
            $terpakai = $allTransaksi
                ->filter(function ($t) use ($kategori, $bulan, $tahun) {
                    return $t->kategori_id == $kategori->id &&
                    $t->tipe == 'pengeluaran' &&
                    Carbon::parse($t->tanggal_transaksi)->month == $bulan &&
                    Carbon::parse($t->tanggal_transaksi)->year == $tahun;
                })
                ->sum('nominal');
    
            $jumlahBudget = (float) $u->jumlah;
            $sisa = $jumlahBudget - $terpakai;

            $status = $terpakai > $jumlahBudget ? 'Over Budget' : 'On Budget';

            $dataKategoriBudgets[] = [
                'id' => $u->id,
                'budget_id' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y'),
                'kategori_id' => $kategori->nama_kategori,
                'jumlah' => $u->jumlah,      // Dikirim sebagai angka (bukan string format)
                'terpakai' => $terpakai,
                'sisa' => $sisa,
                'status' => $status,
                'created_at' => $u->created_at,
                'updated_at' => $u->updated_at,
            ];
        }

        Log::info($DataKategoriBudgets);
    
        return DataTables::of($dataKategoriBudgets)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $badgeClass = $row['status'] === 'Over Budget' ? 'danger' : 'success';
                return "<span class='badge bg-{$badgeClass}'>" . $row['status'] . "</span>";
            })
            ->addColumn('actions', function ($dataKategoriBudgets) {
                return "
                    <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editKategoriBudgets(" . $dataKategoriBudgets['id'] . ")'>
                        <i class='ri-edit-2-line'></i>
                    </button> 
                    <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteKategoriBudgets(" . $dataKategoriBudgets['id'] . ")'>
                        <i class='ri-delete-bin-6-line'></i>
                    </button>
                ";
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function getKategoriTransaksi()
    {
        $kategori = $this->kategoriTransaksiService->getKategoriPengeluaran();
        Log::info($kategori);
        return response()->json($kategori);
    }

    public function getKategoriBudgets(){
        $DataBudgets = $this->DaftarBudgetsService->getAllDaftarBudget();
        return response()->json($DataBudgets);
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
        $data = $request->validate([
            'KategoriBudget' => 'required|array|min:1',
            'KategoriBudget.*.budget_id' => 'required|exists:budgets,id',
            'KategoriBudget.*.kategori_transaksi' => 'required|exists:kategori_transaksi,id',
            'KategoriBudget.*.jumlah' => 'required|numeric|min:1',
            'force_update' => 'nullable|boolean',
        ]);
    
        try {
            // Ambil budget_id (anggap semua item punya budget_id yang sama)
            $budgetId = $data['KategoriBudget'][0]['budget_id'];
            $totalBudget = $this->DaftarBudgetsService->findDaftarBudget($budgetId)->total;
            $forceUpdate = filter_var($request->input('force_update'), FILTER_VALIDATE_BOOLEAN);
    
            // Hitung total jumlah dari request
            $totalInputJumlah = collect($data['KategoriBudget'])->sum('jumlah');
    
            // 🔍 Ambil total jumlah dari data kategori budget yang sudah tersimpan
            $totalExistingJumlah = $this->KategoriBudgetsService->getTotalJumlahByBudgetId($budgetId);
    
            $combinedTotal = $totalExistingJumlah + $totalInputJumlah;
    
            // Log::info('Total Budget: ' . $totalBudget);
            // Log::info('Total Existing Kategori Budget: ' . $totalExistingJumlah);
            // Log::info('Total Input (baru): ' . $totalInputJumlah);
            // Log::info('Total Gabungan (existing + input): ' . $combinedTotal);
            // Log::info('Force Update: ' . ($forceUpdate ? 'Yes' : 'No'));
    
            // Jika total gabungan melebihi dan tidak ada flag force update
            if ($combinedTotal > $totalBudget && !$forceUpdate) {
                return response()->json([
                    'code' => 'budget_over',
                    'message' => 'Total kategori budget (Rp. ' . number_format($combinedTotal, 0, ',', '.') . ') melebihi total budget utama (Rp. ' . number_format($totalBudget, 0, ',', '.') . '). Apakah Anda ingin memperbarui total budget?',
                ], 422);
            }
    
            // Jika force update, maka update total budget ke nilai total gabungan
            if ($forceUpdate) {
                // Log::info('⚡ Controller: updateTotalBudget ke: ' . $combinedTotal);
                $this->DaftarBudgetsService->updateTotalBudget($budgetId, $combinedTotal);
            }
    
            // Simpan data input
            foreach ($data['KategoriBudget'] as $row) {
                $this->KategoriBudgetsService->createKategoriBudget([
                    'budget_id' => $row['budget_id'],
                    'kategori_id' => $row['kategori_transaksi'],
                    'jumlah' => $row['jumlah'],
                ]);
            }
    
            return response()->json([
                'message' => 'Kategori Budget berhasil disimpan!'
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Gagal simpan kategori budget: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.'
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
    public function edit(string $id)
    {
        $KategoriBudget = $this->KategoriBudgetsService->findKategoriBudget($id);
        return response()->json($KategoriBudget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategoriBudget = $request->input('KategoriBudget.0');
    
        // Validasi
        $validated = Validator::make($kategoriBudget, [
            'budget_id' => 'required|exists:budgets,id',
            'kategori_transaksi' => 'required|exists:kategori_transaksi,id',
            'jumlah' => 'required|numeric|min:1',
        ])->validate();
    
        $budgetId = $kategoriBudget['budget_id'];
        $inputJumlah = (int) $kategoriBudget['jumlah'];
        $forceUpdate = filter_var($request->input('force_update'), FILTER_VALIDATE_BOOLEAN);
    
        // 🔍 Ambil total dari kategori_budget lain dengan budget_id yang sama
        $totalExisting = $this->KategoriBudgetsService->getTotalJumlahByBudgetId($budgetId, $excludeId = $id);
        $totalBudget = $this->DaftarBudgetsService->findDaftarBudget($budgetId)->total;
    
        $combinedTotal = $totalExisting + $inputJumlah;
    
        if ($combinedTotal > $totalBudget && !$forceUpdate) {
            return response()->json([
                'code' => 'budget_over',
                'message' => 'Total kategori budget (Rp. ' . number_format($combinedTotal, 0, ',', '.') .
                ') melebihi total budget utama (Rp. ' . number_format($totalBudget, 0, ',', '.') . '). Apakah Anda ingin memperbarui total budget?',
            ], 422);
        }
    
        if ($forceUpdate) {
            $this->DaftarBudgetsService->updateTotalBudget($budgetId, $combinedTotal);
        }
    
        // Data siap update
        $updateData = [
            'budget_id' => $kategoriBudget['budget_id'],
            'kategori_id' => $kategoriBudget['kategori_transaksi'],
            'jumlah' => $kategoriBudget['jumlah'],
        ];
    
        $result = $this->KategoriBudgetsService->updateKategoriBudget($id, $updateData);
    
        return response()->json([
            'message' => 'Kategori Budget berhasil diperbarui!',
            'data' => $result
        ]);
    }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $KategoriBudgets = $this->KategoriBudgetsService->findKategoriBudget($id);
            // Hapus role
            $KategoriBudgets->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Kategori Budget Bulanan berhasil dihapus.'
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
