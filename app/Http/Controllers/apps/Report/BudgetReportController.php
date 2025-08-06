<?php

namespace App\Http\Controllers\apps\Report;

use App\Http\Controllers\Controller;
use App\Services\Budgets\DaftarBudgets\DaftarBudgetsService;
use App\Services\Budgets\KategoriBudgets\KategoriBudgetsService;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetReportController extends Controller
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
    public function budgetReport()
    {
        return view('apps.Report.BudgetReport.BudgetReport', ['pageTitle' => 'Report Budget']);
    }

    
    public function table(Request $request)
    {
        $start = $request->get('start'); // yyyy-mm-dd
        $end = $request->get('end');     // yyyy-mm-dd

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $DataKategoriBudgets = $this->KategoriBudgetsService->getAllKategoriBudget();
        $allTransaksi = $this->transaksiService->getAllTransaksi(); // return Collection

        $dataKategoriBudgets = [];
        $totalBudgetPerBulan = [];
        $totalAlokasiPerBulan = [];
        $totalBelumTeralokasiPerBulan = [];
        $totalTerpakaiPerBulan = [];
        $totalSisaPerBulan = [];

        $processedBudgets = [];

        foreach ($DataKategoriBudgets as $u) {
            $budget = $u->budgets;
            $kategori = $u->kategori_transaksi;

            $budgetDate = Carbon::createFromDate($budget->tahun, $budget->bulan, 1);

            if ($budgetDate->lt($startDate) || $budgetDate->gt($endDate)) {
                continue;
            }

            $monthYearKey = $budgetDate->format('Y-m');
            $budgetLabel = $budgetDate->locale('id')->translatedFormat('F Y');

            if (!isset($processedBudgets[$budget->id])) {
                $totalBudgetPerBulan[$monthYearKey] = [
                    'label' => $budgetLabel,
                    'total' => (float) $budget->total,
                ];
                $processedBudgets[$budget->id] = true;
            }

            if (!isset($totalAlokasiPerBulan[$monthYearKey])) {
                $totalAlokasiPerBulan[$monthYearKey] = 0;
            }
            $totalAlokasiPerBulan[$monthYearKey] += (float) $u->jumlah;

            $terpakai = $allTransaksi
                ->filter(function ($t) use ($kategori, $budget) {
                    return $t->kategori_id == $kategori->id &&
                        $t->tipe == 'pengeluaran' &&
                        Carbon::parse($t->tanggal_transaksi)->month == (int) $budget->bulan &&
                        Carbon::parse($t->tanggal_transaksi)->year == (int) $budget->tahun;
                })
                ->sum('nominal');


            $jumlahBudget = (float) $u->jumlah;
            $sisa = $jumlahBudget - $terpakai;
            $status = $terpakai > $jumlahBudget ? 'Over Budget' : 'On Budget';

            $dataKategoriBudgets[] = [
                'id' => $u->id,
                'budget_id' => $budgetLabel,
                'bulan_tahun_key' => $monthYearKey,
                'kategori_id' => $kategori->nama_kategori,
                'jumlah' => $jumlahBudget,
                'terpakai' => $terpakai,
                'sisa' => $sisa,
                'total_budget_per_bulan' => $totalBudgetPerBulan,
                'status' => $status,
            ];

            // Log::info($dataKategoriBudgets);

        }

        // Tambahkan total_alokasi_budget ke setiap item
        foreach ($dataKategoriBudgets as &$item) {
            $key = $item['bulan_tahun_key'];
            $item['total_alokasi_budget'] = $totalAlokasiPerBulan[$key] ?? 0;
        }

        // Hitung total belum teralokasi per bulan
        foreach ($totalBudgetPerBulan as $key => &$item) {
            $totalBudget = $item['total'];
            $totalAlokasi = $totalAlokasiPerBulan[$key] ?? 0;
            $totalBelumTeralokasiPerBulan[$key] = $totalBudget - $totalAlokasi;
        }

        foreach ($dataKategoriBudgets as $detail) {
            $bulanTahun = $detail['bulan_tahun_key'];
            $terpakai = $detail['terpakai'] ?? 0;

            if (!isset($totalTerpakaiPerBulan[$bulanTahun])) {
                $totalTerpakaiPerBulan[$bulanTahun] = 0;
            }

            $totalTerpakaiPerBulan[$bulanTahun] += $terpakai;
        }

        foreach ($dataKategoriBudgets as $detail) {
            $bulanTahun = $detail['bulan_tahun_key'];
            $sisa = $detail['sisa'] ?? 0;

            if (!isset($totalSisaPerBulan[$bulanTahun])) {
                $totalSisaPerBulan[$bulanTahun] = 0;
            }

            $totalSisaPerBulan[$bulanTahun] += $sisa;
        }
        Log::info($totalSisaPerBulan);


        return response()->json([
            'data' => $dataKategoriBudgets,
            'totalBelumTeralokasiPerBulan' => $totalBelumTeralokasiPerBulan,
            'totalTerpakaiPerBulan' => $totalTerpakaiPerBulan,
            'totalSisaPerBulan' => $totalSisaPerBulan,
        ]);
    }

    public function BudgetPdf(Request $request)
    {
        $start = $request->get('start'); // yyyy-mm-dd
        $end = $request->get('end');     // yyyy-mm-dd

        $DataKategoriBudgets = $this->KategoriBudgetsService->getKategoriBudgetMY($start, $end);
        $allTransaksi = $this->transaksiService->getAllTransaksi(); // return Collection

        $groupedData = []; // ← DATA AKAN DIGRUP DI SINI
        $total_alokasi = [];
        $total_budget = [];
        $total_belum_alokasi = [];

        foreach ($DataKategoriBudgets as $u) {
            $bulanTahun = $u->budgets->bulan . ' ' . $u->budgets->tahun;
            $jumlah = (float) $u->jumlah;
            $total = (float) $u->budgets->total;

            $budget = $u->budgets;
            $kategori = $u->kategori_transaksi;

            // Ambil Total Pengeluaran Berdasarkan Budget
            $terpakai = $allTransaksi
                ->filter(function ($t) use ($kategori, $budget) {
                    return $t->kategori_id == $kategori->id &&
                        $t->tipe == 'pengeluaran' &&
                        Carbon::parse($t->tanggal_transaksi)->month == (int) $budget->bulan &&
                        Carbon::parse($t->tanggal_transaksi)->year == (int) $budget->tahun;
                })
                ->sum('nominal');

            $sisa = $jumlah - $terpakai;
            $status = $terpakai > $jumlah ? 'Over Budget' : 'On Budget';

            $item = [
                'kategori' => $kategori->nama_kategori,
                'jumlah' => $jumlah,
                'terpakai' => $terpakai,
                'sisa' => $sisa,
                'status' => $status,
            ];

            // Grouping data berdasarkan bulan tahun
            $groupedData[$bulanTahun]['items'][] = $item;

            // Set total alokasi dan total budget per bulan tahun
            if (!isset($total_alokasi[$bulanTahun])) {
                $total_alokasi[$bulanTahun] = 0;
                $total_budget[$bulanTahun] = $total;
            }

            $total_alokasi[$bulanTahun] += $jumlah;
        }

        // Hitung belum dialokasikan
        foreach ($total_budget as $bulanTahun => $total) {
            $total_belum_alokasi[$bulanTahun] = $total - $total_alokasi[$bulanTahun];
            $groupedData[$bulanTahun]['total_budget'] = $total;
            $groupedData[$bulanTahun]['total_alokasi'] = $total_alokasi[$bulanTahun];
            $groupedData[$bulanTahun]['total_belum_alokasi'] = $total_belum_alokasi[$bulanTahun];
        }

        $pdf = Pdf::loadView('apps.Report.BudgetReport.BudgetPdf', [
            'groupedData' => $groupedData
        ]);

        $filename = "Laporan_Budget_{$start}_sampai_{$end}_" . now()->format('Ym') . ".pdf";
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
