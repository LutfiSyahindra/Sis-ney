<?php

namespace App\Http\Controllers\apps\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\asetTabungan\asetTabunganService;
use App\Services\Budgets\DaftarBudgets\DaftarBudgetsService;
use App\Services\Budgets\KategoriBudgets\KategoriBudgetsService;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $kategoriTransaksiService,$DaftarBudgetsService,$KategoriBudgetsService,$transaksiService, $asetTabunganService;

    public function __construct(kategoriTransaksiService $kategoriTransaksiService, DaftarBudgetsService $DaftarBudgetsService, KategoriBudgetsService $KategoriBudgetsService, transaksiService $transaksiService, asetTabunganService $asetTabunganService)

    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
        $this->DaftarBudgetsService = $DaftarBudgetsService;
        $this->KategoriBudgetsService = $KategoriBudgetsService;
        $this->transaksiService = $transaksiService;
        $this->asetTabunganService = $asetTabunganService;
    }
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        return view('apps.Dashboard.appsDashboard', ['pageTitle' => 'Dashboard']); 
    }

    public function totalBudget(Request $request)
    {
        $start = $request->get('start'); // yyyy-mm-dd
        $end = $request->get('end');     // yyyy-mm-dd

        // Ambil data sesuai request
        $DataKategoriBudgets = $this->KategoriBudgetsService->getKategoriBudgetMY($start, $end);
        $jumlahBudgets = $DataKategoriBudgets->sum('jumlah');

        // Ambil data bulan lalu
        $startBulanLalu = Carbon::parse($start)->subMonth()->startOfMonth()->toDateString();
        $endBulanLalu = Carbon::parse($start)->subMonth()->endOfMonth()->toDateString();

        $DataBudgetBulanLalu = $this->KategoriBudgetsService->getKategoriBudgetMY($startBulanLalu, $endBulanLalu);
        $jumlahBudgetBulanLalu = $DataBudgetBulanLalu->sum('jumlah');

        // Hitung selisih
        $selisih = $jumlahBudgets - $jumlahBudgetBulanLalu;
        $statusSelisih = $selisih > 0 ? 'naik' : ($selisih < 0 ? 'turun' : 'tetap');

        Log::info('Selisih: ' . $selisih);

        // Kirim respon
        return response()->json([
            'start' => $start,
            'end' => $end,
            'jumlah_budget' => number_format($jumlahBudgets, 0, ',', '.'),
            'jumlah_budget_bulan_lalu' => $jumlahBudgetBulanLalu,
            'selisih' => number_format(abs($selisih), 0, ',', '.'),
            'status_selisih' => $statusSelisih,
            'chart' => [$jumlahBudgetBulanLalu, $jumlahBudgets],
            'labels' => ['Bulan Lalu', 'Bulan Ini']
            ]);
    }

    public function Pengeluaran(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $allTransaksi = $this->transaksiService->getAllTransaksi();

        // Filter pengeluaran di range yang dipilih
        $pengeluaranBulanIni = $allTransaksi
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('nominal');

        // Hitung range bulan lalu berdasarkan $start dan $end
        $startLastMonth = Carbon::parse($start)->subMonth()->startOfMonth();
        $endLastMonth = Carbon::parse($start)->subMonth()->endOfMonth();

        $pengeluaranBulanLalu = $allTransaksi
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$startLastMonth, $endLastMonth])
            ->sum('nominal');

        $selisih = $pengeluaranBulanIni - $pengeluaranBulanLalu;
        $statusSelisih = $selisih > 0 ? 'naik' : ($selisih < 0 ? 'turun' : 'tetap');

        return response()->json([
            'total' => number_format($pengeluaranBulanIni, 0, ',', '.'),
            'selisih' => number_format(abs($selisih), 0, ',', '.'),
            'status_selisih' => $statusSelisih,
            'chart' => [$pengeluaranBulanLalu, $pengeluaranBulanIni],
            'labels' => ['Bulan Lalu', 'Bulan Ini']
        ]);
    }

    public function Pemasukan(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $allTransaksi = $this->transaksiService->getAllTransaksi();

        // Filter pengeluaran di range yang dipilih
        $pemasukanBulanIni = $allTransaksi
            ->where('tipe', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('nominal');

        // Hitung range bulan lalu berdasarkan $start dan $end
        $startLastMonth = Carbon::parse($start)->subMonth()->startOfMonth();
        $endLastMonth = Carbon::parse($start)->subMonth()->endOfMonth();

        $pemasukanBulanLalu = $allTransaksi
            ->where('tipe', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$startLastMonth, $endLastMonth])
            ->sum('nominal');

        $selisih = $pemasukanBulanIni - $pemasukanBulanLalu;
        $statusSelisih = $selisih > 0 ? 'naik' : ($selisih < 0 ? 'turun' : 'tetap');

        return response()->json([
            'total' => number_format($pemasukanBulanIni, 0, ',', '.'),
            'selisih' => number_format(abs($selisih), 0, ',', '.'),
            'status_selisih' => $statusSelisih,
            'chart' => [$pemasukanBulanLalu, $pemasukanBulanIni],
            'labels' => ['Bulan Lalu', 'Bulan Ini']
        ]);
    }

    public function PengeluaranPerHari(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $allTransaksi = $this->transaksiService->getAllTransaksi();

        // Filter hanya pengeluaran dalam rentang tanggal
        $pengeluaran = $allTransaksi
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$start, $end]);

        // Group by tanggal
        $pengeluaranPerTanggal = $pengeluaran->groupBy('tanggal_transaksi');

        // Hitung total pengeluaran per tanggal
        $totalPerTanggal = $pengeluaranPerTanggal->map(function ($items) {
            return $items->sum(function ($item) {
                return (float) $item['nominal'];
            });
        });

        $chartData = [
            'categories' => $totalPerTanggal->keys()->toArray(),  // array tanggal
            'data' => $totalPerTanggal->values()->map(fn($val) => (float) $val)->toArray(),  // array nominal
        ];

        return response()->json([
            'start' => $start,
            'end' => $end,
            'chart' => $chartData, // ini yang dipakai untuk chart
            'grouped_pengeluaran' => $pengeluaranPerTanggal,
            'total_per_tanggal' => $totalPerTanggal,
        ]);
    }

    public function PengeluaranPerBulan(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfYear()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfYear()->toDateString();

        $allTransaksi = $this->transaksiService->getAllTransaksi();

        // Filter hanya pengeluaran dalam rentang tanggal
        $pengeluaran = $allTransaksi
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal_transaksi', [$start, $end]);

        // Group by bulan (contoh: '2025-01', '2025-02')
        $pengeluaranPerBulan = $pengeluaran->groupBy(function ($item) {
            return Carbon::parse($item['tanggal_transaksi'])->format('Y-m');
        });

        Log::info($pengeluaranPerBulan);

        // Hitung total pengeluaran per bulan
        $totalPerBulan = $pengeluaranPerBulan->map(function ($items) {
            return $items->sum(function ($item) {
                return (float) $item['nominal'];
            });
        });

        $chartData = [
            'categories' => $totalPerBulan->keys()->toArray(),  // array bulan
            'data' => $totalPerBulan->values()->map(fn($val) => (float) $val)->toArray(),
        ];

        return response()->json([
            'start' => $start,
            'end' => $end,
            'chart' => $chartData,
            'grouped_pengeluaran' => $pengeluaranPerBulan,
            'total_per_bulan' => $totalPerBulan,
        ]);
    }
    
    public function getAsetTabungan (){
        $tabungan = $this->asetTabunganService->getAllAsetTabungan();
        Log::info($tabungan);
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
