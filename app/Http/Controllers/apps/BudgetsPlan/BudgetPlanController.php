<?php

namespace App\Http\Controllers\apps\BudgetsPlan;

use App\Http\Controllers\Controller;
use App\Services\asetTabungan\asetTabunganService;
use App\Services\Budgets\BudgetPlan\BudgetPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BudgetPlanController extends Controller
{

    protected $BudgetPlanService;
    protected $asetTabunganService;
    public function __construct(BudgetPlanService $BudgetPlanService, asetTabunganService $asetTabunganService)
    {
        $this->BudgetPlanService = $BudgetPlanService;
        $this->asetTabunganService = $asetTabunganService;
    }
    /**
     * Display a listing of the resource.
     */
    public function BudgetPlan()
    {
        return view('apps.BudgetPlan.BudgetPlan', ['pageTitle' => 'Budget Plan']);
    }

    public function table()
    {
        $BudgetPlan = $this->BudgetPlanService->getAllBudgetPlan();
    
        $dataBudgetPlan = [];
    
        foreach ($BudgetPlan as $u) {
            $dataBudgetPlan[] = [
                'id' => $u['id'],
                'user_id' => $u->user->name,
                'nama' => $u->nama,
                'target' => $u->target,
                'terkumpul' => $u->terkumpul,
                'progres' => $u->progres,
                'status' => $u->status,
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }
    
        return DataTables::of($dataBudgetPlan)
            ->addIndexColumn()
            ->addColumn('actions', function ($dataBudgetPlan) {
                return "
                    <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editBudgetPlan(" . $dataBudgetPlan['id'] . ")'>
                        <i class='ri-edit-2-line'></i>
                    </button> 
                    <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteBudgetPlan(" . $dataBudgetPlan['id'] . ")'>
                        <i class='ri-delete-bin-6-line'></i>
                    </button>
                    <button class='btn btn-sm btn-primary' style='padding: 1px 5px;' onclick='detailBudgetPlan(" . $dataBudgetPlan['id'] . ")'>
                        <i class='ri-eye-line'></i>
                    </button>
                ";
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            // Validasi manual (jika kamu ingin menangani error sendiri)
            $validator = Validator::make($request->all(), [
                'plan' => 'required|string',
                'targetValue' => 'required|numeric|min:0',
            ]);

    
            if ($validator->fails()) {
                // Jika validasi gagal
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422); // HTTP 422 Unprocessable Entity
            }
    
            // Simpan data
            $budget = $this->BudgetPlanService->createBudgetPlan([
                'user_id' => Auth::id(),
                'nama' => $request->plan,
                'target' => $request->targetValue,
            ]);
    
            return response()->json([
                'message' => 'Data budget Plan berhasil disimpan',
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

    public function edit(string $id)
    {
        $budgetPlan = $this->BudgetPlanService->findBudgetPlan($id);
        return response()->json($budgetPlan);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'plan' => 'required|string',
                'targetValue' => 'required|numeric|min:0',
            ]);

            $mappedData = [
                'nama' => $validated['plan'],
                'target' => $validated['targetValue'],
            ];
    
            // Panggil service
            $budget = $this->BudgetPlanService->updateBudgetPlan($id, $mappedData);
    
            return response()->json([
                'message' => 'Data budget plan berhasil diperbarui',
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

    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $budgetPlan = $this->BudgetPlanService->findBudgetPlan($id);
            // Hapus role
            $budgetPlan->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Budget Plan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBudgetPlan(){
        $budgetPlan = $this->BudgetPlanService->getAllBudgetPlan();
        return response()->json($budgetPlan);
    }

    public function deposit(Request $request)
    {
        Log::info($request->all());
        try {
            // Validasi manual (jika kamu ingin menangani error sendiri)
            $validator = Validator::make($request->all(), [
                'budget_plan_id' => 'required',
                'nominalValue' => 'required|numeric|min:0',
            ]);

    
            if ($validator->fails()) {
                // Jika validasi gagal
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422); // HTTP 422 Unprocessable Entity
            }

            // Pengecekan Aset tabungan
            $asetTabungan = $this->asetTabunganService->findTabungan($request->aset_tabungan_id);
            if ($asetTabungan->saldo < $request->nominalValue) {
                return response()->json([
                    'message' => 'Saldo tidak mencukupi'
                ], 422);
            }

            // Update Saldo
            $asetTabungan->update([
                'saldo' => $asetTabungan->saldo - $request->nominalValue
            ]);
    
            // Simpan data
            $budgetDetail = $this->BudgetPlanService->createBudgetPlanDetail([
                'budget_plan_id' => $request->budget_plan_id,
                'nominal' => $request->nominalValue,
                'aset_tabungan_id' => $request->aset_tabungan_id,
            ]);

            // Update Budget Plan
            $budgetPlan = $this->BudgetPlanService->findBudgetPlan($request->budget_plan_id);
            $terkumpul = $budgetPlan->terkumpul + $request->nominalValue;
            $progres = ($terkumpul / $budgetPlan->target) * 100; 
            $budgetPlan->update([
                'terkumpul' => $terkumpul,
                'progres' => $progres
            ]);
    
            return response()->json([
                'message' => 'Data budget Plan berhasil disimpan',
                'data' => $budgetDetail
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

    public function PlanDetail(Request $request){
        return view('apps.BudgetPlan.BudgetPlanDetail', ['pageTitle' => 'Budget Plan Detail']);
    }

    public function BudgetPlanDetail(Request $request){
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $BudgetPlanDetail = $this->BudgetPlanService->getBudgetPlanDetail($start, $end);

        Log::info($BudgetPlanDetail);

        $dataBudgetPlanDetail = [];
    
        foreach ($BudgetPlanDetail as $u) {
            $dataBudgetPlanDetail[] = [
                'id' => $u->id,
                'budget_plan' => $u->budgetPlan->nama,
                'nominal' => $u->nominal,
                'aset_tabungan' => $u->Aset->nama_tabungan,
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }
    
        return DataTables::of($dataBudgetPlanDetail)
            ->addIndexColumn()
            ->addColumn('actions', function ($dataBudgetPlanDetail) {
                return "
                    <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editBudgetPlanDetail(" . $dataBudgetPlanDetail['id'] . ")'>
                        <i class='ri-edit-2-line'></i>
                    </button> 
                    <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteBudgetPlanDetail(" . $dataBudgetPlanDetail['id'] . ")'>
                        <i class='ri-delete-bin-6-line'></i>
                    </button>
                ";
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function tableDetail($id){
        $BudgetPlanDetail = $this->BudgetPlanService->getBudgetPlanDetailById($id);
        Log::info($BudgetPlanDetail);
        $dataBudgetPlanDetail = [];
    
        foreach ($BudgetPlanDetail as $u) {
            $dataBudgetPlanDetail[] = [
                'id' => $u->id,
                'budget_plan' => $u->budgetPlan->nama,
                'nominal' => $u->nominal,
                'aset_tabungan' => $u->Aset->nama_tabungan,
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }
    
        return DataTables::of($dataBudgetPlanDetail)
            ->addIndexColumn()
            ->addColumn('actions', function ($dataBudgetPlanDetail) {
                return "
                    <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editBudgetPlanDetail(" . $dataBudgetPlanDetail['id'] . ")'>
                        <i class='ri-edit-2-line'></i>
                    </button> 
                    <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteBudgetPlanDetail(" . $dataBudgetPlanDetail['id'] . ")'>
                        <i class='ri-delete-bin-6-line'></i>
                    </button>
                ";
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function editDetail(string $id)
    {
        $BudgetPlanDetail = $this->BudgetPlanService->findBudgetPlanDetail($id);
        return response()->json($BudgetPlanDetail);
    }

    public function updateDetail(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'aset_tabungan_id' => 'required|exists:aset_tabungan,id',
            'budget_plan_id' => 'required|exists:budget_plan,id',
            'nominalValue' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Ambil data detail lama
            $oldDetail = $this->BudgetPlanService->findBudgetPlanDetail($id);
            $oldNominal = $oldDetail->nominal;
            $oldAsetId = $oldDetail->aset_tabungan_id;

            // Ambil aset lama dan kembalikan saldo lama
            $asetLama = $this->asetTabunganService->findTabungan($oldAsetId);
            $asetLama->saldo += $oldNominal;
            $asetLama->save();

            // Update Target
            $terkumpulLama = $this->BudgetPlanService->findBudgetPlan($oldDetail->budget_plan_id)->terkumpul;
            $terkumpulBaru = $terkumpulLama - $oldNominal + $request->input('nominalValue');
            $this->BudgetPlanService->updateBudgetPlan($oldDetail->budget_plan_id, ['terkumpul' => $terkumpulBaru]);
            // Siapkan data baru dari request
            $newAsetId = $request->input('aset_tabungan_id');
            $newNominal = $request->input('nominalValue');

            $data = [
                'aset_tabungan_id' => $newAsetId,
                'nominal' => $newNominal,
                'budget_plan_id' => $request->input('budget_plan_id'),
            ];

            // Cek apakah saldo cukup di aset baru
            $asetBaru = $this->asetTabunganService->findTabungan($newAsetId);

            if ($asetBaru->saldo < $newNominal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Saldo tidak mencukupi di aset tabungan yang dipilih.'
                ], 400);
            }

            // Update data budget plan detail
            $updatedDetail = $this->BudgetPlanService->updateBudgetPlanDetail($id, $data);

            // Potong saldo dari aset baru
            $asetBaru->saldo -= $newNominal;
            $asetBaru->save();

            $budgetPlan = $this->BudgetPlanService->findBudgetPlan($request->budget_plan_id);
            $terkumpul = $budgetPlan->terkumpul + $request->$newNominal;
            $progres = ($terkumpul / $budgetPlan->target) * 100; 
            $budgetPlan->update([
                'progres' => $progres
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Budget Plan Detail updated successfully',
                'data' => $updatedDetail,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyDetail($id)
    {
        try {
            $budgetDetail = $this->BudgetPlanService->findBudgetPlanDetail($id);

            $saldo = $budgetDetail->Aset;

            $saldo->saldo += $budgetDetail->nominal;
            $saldo->save();

            // update target dan progres
            $budgetPlan = $this->BudgetPlanService->findBudgetPlan($budgetDetail->budget_plan_id);
            $terkumpul = $budgetPlan->terkumpul - $budgetDetail->nominal;
            $progres = ($terkumpul / $budgetPlan->target) * 100; 
            $budgetPlan->update([
                'progres' => $progres,
                'terkumpul' => $terkumpul
            ]);

            $budgetDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Remove the specified resource from storage.
     */
}
