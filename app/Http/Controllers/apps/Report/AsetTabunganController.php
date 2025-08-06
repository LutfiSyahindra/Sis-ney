<?php

namespace App\Http\Controllers\apps\Report;

use App\Http\Controllers\Controller;
use App\Services\Budgets\BudgetPlan\BudgetPlanService;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsetTabunganController extends Controller
{

    protected $kategoriTransaksiService;
    protected $transaksiService;
    protected $BudgetPlanService;

    public function __construct(kategoriTransaksiService $kategoriTransaksiService, transaksiService $transaksiService, BudgetPlanService $BudgetPlanService)
    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
        $this->transaksiService = $transaksiService;
        $this->BudgetPlanService = $BudgetPlanService;
    }
    /**
     * Display a listing of the resource.
     */
    public function asetTabunganReport()
    {
        return view('apps.Report.AsetTabunganReport.asetTabunganReport', ['pageTitle' => 'Report Aset Tabungan']);
    }

    public function getData(Request $request) {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $transaksi = $this->transaksiService->getTransaksi($start, $end);
        Log::info($transaksi);

        return response()->json($transaksi);
    }
    
    public function downloadPDF(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $data = $this->transaksiService->getTransaksi($start, $end);

        // Ambil data Detail setoran Budget Plan Sesuai Aset Tabungan
        $budgetPlan = $this->BudgetPlanService->getBudgetPlanDetail($start, $end);
        
        // Group by aset_tabungan_id
        $groupedByAset = $data->groupBy('aset_tabungan_id');
        $groupedBudgetPlan = collect($budgetPlan)->groupBy('aset_tabungan_id');
        Log::info($groupedBudgetPlan);

        // Siapkan data terstruktur
        $groupedData = $groupedByAset->map(function ($items, $asetId) use ($groupedBudgetPlan)  {
            $grouped = $items->groupBy(fn($trx) => strtolower($trx->tipe));

            return [
                'aset_tabungan_id' => $asetId,
                'nama_aset' => optional($items->first()->aset)->nama_tabungan, // jika relasi ada
                'total_aset'=>optional($items->first()->aset)->saldo,
                'pemasukan' => $grouped['pemasukan'] ?? collect(),
                'pengeluaran' => $grouped['pengeluaran'] ?? collect(),
                'totalPemasukan' => ($grouped['pemasukan'] ?? collect())->sum('nominal'),
                'totalPengeluaran' => ($grouped['pengeluaran'] ?? collect())->sum('nominal'),
                'budgetPlans' => $groupedBudgetPlan->get($asetId, collect()),
                'totalBudgetPlan' => $groupedBudgetPlan->get($asetId, collect())->sum('nominal'),
            ];
        });

        $pdf = Pdf::loadView('apps.Report.AsetTabunganReport.asetTabunganPdf', [
            'groupedData' => $groupedData,
            'start' => $start,
            'end' => $end,
        ]);

        $filename = "Laporan_Per_Aset_{$start}_sampai_{$end}_" . now()->format('Ymd_His') . ".pdf";

        return $pdf->stream($filename);
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
        //
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
        //
    }

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
    public function destroy(string $id)
    {
        //
    }
}
