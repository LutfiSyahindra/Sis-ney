<?php

namespace App\Http\Controllers\apps\Report;

use App\Http\Controllers\Controller;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TransaksiReportController extends Controller
{
    protected $kategoriTransaksiService;
    protected $transaksiService;

    public function __construct(kategoriTransaksiService $kategoriTransaksiService, transaksiService $transaksiService)
    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
        $this->transaksiService = $transaksiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function reportTransaksi()
    {
        return view('apps.Report.TransaksiReport.transaksiReport', ['pageTitle' => 'Report Transaksi']);
    }

    public function tablePengeluaran(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $transaksi = $this->transaksiService->getTransaksi($start, $end);

        $dataTransaksi = [];
        foreach ($transaksi as $u) {
            if ($u['tipe'] === 'pengeluaran') {
                $dataTransaksi[] = [
                    'id' => $u->id,
                    'user_id' => $u->user->name,
                    'tanggal_transaksi' => $u->tanggal_transaksi,
                    'kategori_id' => $u->kategori->nama_kategori ?? '-',
                    'aset_tabungan_id' => $u->aset->nama_tabungan ?? '-',
                    'nominal' => $u->nominal,
                    'tipe' => $u->tipe,
                    'ket' => $u->keterangan ?? '-',
                    'created_at' => $u->created_at,
                ];
            }
        }

        return DataTables::of($dataTransaksi)
            ->addIndexColumn()
            ->editColumn('tipe', function ($row) {
                if ($row['tipe'] === 'pemasukan') {
                    return '<span class="badge bg-success">Pemasukan</span>';
                } elseif ($row['tipe'] === 'pengeluaran') {
                    return '<span class="badge bg-danger">Pengeluaran</span>';
                }
                return $row['tipe'];
            })
            ->rawColumns(['tipe'])
            ->make(true);
    }

    public function tablePemasukan(Request $request){
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $transaksi = $this->transaksiService->getTransaksi($start, $end);    
        Log::info($transaksi);
        $dataTransaksi = [];
        foreach ($transaksi as $u) {
            if ($u['tipe'] === 'pemasukan') {
                $dataTransaksi[] = [
                'id' => $u->id,
                'user_id' => $u->user->name,
                'tanggal_transaksi' => $u->tanggal_transaksi,
                'kategori_id' => $u->kategori->nama_kategori ?? '-',      // akses relasi sebagai object
                'aset_tabungan_id' => $u->aset->nama_tabungan ?? '-',  // akses relasi sebagai object
                'nominal' => $u->nominal,
                'tipe' => $u->tipe,
                'tanggal_transaksi' => $u->tanggal_transaksi,
                'ket' => $u->keterangan ?? '-',
                'created_at' => $u->created_at,
            ];
            }
        }

        return DataTables::of($dataTransaksi)
        ->addIndexColumn()
        ->editColumn('tipe', function ($row) {
            if ($row['tipe'] === 'pemasukan') {
                return '<span class="badge bg-success">Pemasukan</span>';
            } elseif ($row['tipe'] === 'pengeluaran') {
                return '<span class="badge bg-danger">Pengeluaran</span>';
            }
            return $row['tipe'];
        })
        ->rawColumns(['tipe']) // ⬅️ tambahkan 'tipe' di sini!
        ->make(true);

    }

    public function tableAll(Request $request){
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $transaksi = $this->transaksiService->getTransaksi($start, $end);  
        Log::info($transaksi);
        $dataTransaksi = [];
        foreach ($transaksi as $u) {
                $dataTransaksi[] = [
                'id' => $u->id,
                'user_id' => $u->user->name,
                'tanggal_transaksi' => $u->tanggal_transaksi,
                'kategori_id' => $u->kategori->nama_kategori ?? '-',      // akses relasi sebagai object
                'aset_tabungan_id' => $u->aset->nama_tabungan ?? '-',  // akses relasi sebagai object
                'nominal' => $u->nominal,
                'tipe' => $u->tipe,
                'tipe_raw' => $u->tipe,
                'tanggal_transaksi' => $u->tanggal_transaksi,
                'ket' => $u->keterangan ?? '-',
                'created_at' => $u->created_at,
            ];
        }

        return DataTables::of($dataTransaksi)
        ->addIndexColumn()
                ->editColumn('tipe', function ($row) {
            if ($row['tipe'] === 'pemasukan') {
                return '<span class="badge bg-success">Pemasukan</span>';
            } elseif ($row['tipe'] === 'pengeluaran') {
                return '<span class="badge bg-danger">Pengeluaran</span>';
            }
            return $row['tipe'];
        })
        ->rawColumns(['tipe']) // ⬅️ tambahkan 'tipe' di sini!
        ->make(true);

    }

    public function widgetPengeluaran(Request $request)
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
        $persentase = $pengeluaranBulanLalu > 0
            ? round(($selisih / $pengeluaranBulanLalu) * 100, 1)
            : 0;

        return response()->json([
            'total' => number_format($pengeluaranBulanIni, 0, ',', '.'),
            'persen' => $persentase,
            'selisih' => number_format(abs($selisih), 0, ',', '.'),
            'naik' => $selisih >= 0
        ]);
    }


    public function chartPengeluaran(Request $request)
    {
        $start = $request->start;
        $end = $request->end;

        $transaksi = $this->transaksiService->getTransaksi($start, $end)
            ->where('tipe', 'pengeluaran');

        $grouped = $transaksi->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_transaksi)->format('Y-m-d');
        });

        $labels = [];
        $values = [];

        foreach ($grouped as $tanggal => $items) {
            $labels[] = Carbon::parse($tanggal)->format('M j'); // Misal: Jul 10
            $values[] = $items->sum('nominal');
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function widgetPemasukan(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $allTransaksi = $this->transaksiService->getAllTransaksi();

        // Filter pemasukan di range yang dipilih
        $pemasukanBulanIni = $allTransaksi
            ->where('tipe', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('nominal');

        // Range bulan lalu
        $startLastMonth = Carbon::parse($start)->subMonth()->startOfMonth();
        $endLastMonth = Carbon::parse($start)->subMonth()->endOfMonth();

        $pemasukanBulanLalu = $allTransaksi
            ->where('tipe', 'pemasukan')
            ->whereBetween('tanggal_transaksi', [$startLastMonth, $endLastMonth])
            ->sum('nominal');

        $selisih = $pemasukanBulanIni - $pemasukanBulanLalu;
        $persentase = $pemasukanBulanLalu > 0
            ? round(($selisih / $pemasukanBulanLalu) * 100, 1)
            : 0;

        return response()->json([
            'total' => number_format($pemasukanBulanIni, 0, ',', '.'),
            'persen' => $persentase,
            'selisih' => number_format(abs($selisih), 0, ',', '.'),
            'naik' => $selisih >= 0
        ]);
    }


    public function chartPemasukan(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();

        $transaksi = $this->transaksiService->getTransaksi($start, $end)
            ->where('tipe', 'pemasukan');

        $grouped = $transaksi->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_transaksi)->format('Y-m-d');
        });

        $labels = [];
        $values = [];

        foreach ($grouped as $tanggal => $items) {
            $labels[] = Carbon::parse($tanggal)->format('M j'); // Jul 10
            $values[] = $items->sum('nominal');
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function downloadPDF(Request $request)
    {
        $start = $request->start ?? Carbon::now()->startOfMonth()->toDateString();
        $end = $request->end ?? Carbon::now()->endOfMonth()->toDateString();


        $data = $this->transaksiService->getTransaksi($start, $end);

        $grouped = $data->groupBy(fn($trx) => strtolower($trx->tipe));

        $pengeluaran = $grouped['pengeluaran'] ?? collect();
        $pemasukan = $grouped['pemasukan'] ?? collect();

        $totalPengeluaran = $pengeluaran->sum('nominal');
        $totalPemasukan = $pemasukan->sum('nominal');

        $pdf = Pdf::loadView('apps.Report.TransaksiReport.transaksi_pdf', [
            'transaksis' => $data,
            'pengeluaran' => $pengeluaran,
            'pemasukan' => $pemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalPemasukan' => $totalPemasukan,
            'start' => $start,
            'end' => $end,
        ]);

        $filename = "Laporan_{$start}_sampai_{$end}_" . now()->format('Ymd_His') . ".pdf";

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
