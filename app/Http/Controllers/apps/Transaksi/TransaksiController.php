<?php

namespace App\Http\Controllers\apps\Transaksi;

use App\Http\Controllers\Controller;
use App\Services\asetTabungan\asetTabunganService;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use App\Services\Transaksi\transaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    protected $kategoriTransaksiService;
    protected $transaksiService;
    protected $asetTabunganService;

    public function __construct(kategoriTransaksiService $kategoriTransaksiService, transaksiService $transaksiService, asetTabunganService $asetTabunganService)
    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
        $this->transaksiService = $transaksiService;
        $this->asetTabunganService = $asetTabunganService;
    }
    /**
     * Display a listing of the resource.
     */
    public function transaksi()
    {
        return view('apps.Transaksi.transaksi', ['pageTitle' => 'Transaksi']);
    }

    public function tabel(){    
        $transaksi = $this->transaksiService->getAllTransaksi();
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
        ->addColumn('actions', function ($dataTransaksi) {
            return "
                <button class='btn btn-sm btn-warning' style='padding: 1px 5px;' onclick='editTransaksi(" . $dataTransaksi['id'] . ")'>
                    <i class='ri-edit-2-line'></i>
                </button>
                <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteTransaksi(" . $dataTransaksi['id'] . ")'>
                    <i class='ri-delete-bin-6-line'></i>
                </button>
            ";
        })
        ->rawColumns(['tipe', 'actions']) // ⬅️ tambahkan 'tipe' di sini!
        ->make(true);

    }

    public function getKategoriTransaksi(){
        $kategori = $this->kategoriTransaksiService->getKategori();
        Log::info($kategori);
        return response()->json($kategori);
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
        $validator = Validator::make($request->all(), [
            'aset_tabungan_id'   => 'required|exists:aset_tabungan,id',
            'kategori_id'        => 'required|exists:kategori_transaksi,id',
            'tipe'               => 'required|in:pemasukan,pengeluaran',
            'tanggal_transaksi' => 'required|date',
            'nominal'            => 'required|numeric|min:0',
            'keterangan'         => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaksi = $this->transaksiService->createTransaksi([
            'user_id'            => Auth::id(), // ← ini wajib
            'aset_tabungan_id'   => $request->aset_tabungan_id,
            'kategori_id'        => $request->kategori_id,
            'tipe'               => $request->tipe,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'nominal'            => $request->nominal,
            'keterangan'         => $request->keterangan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil disimpan!',
            'data' => $transaksi,
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
        $transaksi = $this->transaksiService->findTransaksi($id);
        return response()->json($transaksi);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'aset_tabungan_id' => 'required|exists:aset_tabungan,id',
            'kategori_id' => 'required|exists:kategori_transaksi,id',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'tanggal_transaksi' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Ambil data transaksi lama
            $old = $this->transaksiService->findTransaksi($id);

            // Hitung pengembalian saldo berdasarkan data lama
            $saldoLama = $old->nominal;
            $asetIdLama = $old->aset_tabungan_id;
            $tipeLama = $old->tipe;

            // Ambil data aset tabungan lama
            $asetLama = $this->asetTabunganService->findTabungan($asetIdLama);

            // Kembalikan saldo lama
            if ($tipeLama === 'pemasukan') {
                $asetLama->saldo -= $saldoLama;
            } else {
                $asetLama->saldo += $saldoLama;
            }
            $asetLama->save();

            // Update transaksi
            $transaksi = $this->transaksiService->updateTransaksi($id, $request->all());

            // Sesuaikan saldo berdasarkan data baru
            $asetBaru = $this->asetTabunganService->findTabungan($request->aset_tabungan_id);
            $nominalBaru = $request->nominal;
            $tipeBaru = $request->tipe;

            if ($tipeBaru === 'pemasukan') {
                $asetBaru->saldo += $nominalBaru;
            } else {
                $asetBaru->saldo -= $nominalBaru;
            }
            $asetBaru->save();

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => $transaksi,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui transaksi.',
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
            $transaksi = $this->transaksiService->findTransaksi($id);

            $saldo = $transaksi->aset;
            if ($transaksi->tipe === 'pemasukan') {
                $saldo->saldo -= $transaksi->nominal;
            } else {
                $saldo->saldo += $transaksi->nominal;
            }
            $saldo->save();

            $transaksi->delete();

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

}
