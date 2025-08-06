<?php

namespace App\Http\Controllers\apps\kategoriTransaksi;

use App\Http\Controllers\Controller;
use App\Services\kategoriTransaksi\kategoriTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class kategoriTransaksiController extends Controller
{

    protected $kategoriTransaksiService;

    public function __construct(KategoriTransaksiService $kategoriTransaksiService)
    {
        $this->kategoriTransaksiService = $kategoriTransaksiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function kategori()
    {
        return view('apps.Kategori.kategori', ['pageTitle' => 'Kategori Transaksi']);
    }

    public function table(){    
        $kategori = $this->kategoriTransaksiService->getAllKategoriTransaksi();
        Log::info($kategori);
        $dataKategori = [];
        foreach ($kategori as $u) {
            $dataKategori[] = [
                'id' => $u['id'],
                'nama' => $u['nama_kategori'],
                'tipe' => $u['tipe'],
                'created_at' => $u['created_at'],
                'updated_at' => $u['updated_at'],
            ];
        }

        return DataTables::of($dataKategori)
        ->addIndexColumn()
        ->addColumn('actions', function ($dataKategori) {
            return "
                <button class='btn btn-sm btn-success' style='padding: 1px 5px;' onclick='editKategori(" . $dataKategori['id'] . ")'>
                    <i class='ri-edit-2-line'></i>
                </button> 
                <button class='btn btn-sm btn-danger' style='padding: 1px 5px;' onclick='deleteKategori(" . $dataKategori['id'] . ")'>
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
        $data = $request->input('kategori');

        Log::info($data);

        foreach ($data as $kategori) {
            $this->kategoriTransaksiService->createKategori([
                'nama_kategori' => $kategori['nama_kategori'],
                'tipe' => $kategori['tipe'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Signup successful!',
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
        $kategori = $this->kategoriTransaksiService->findKategori($id);
        return response()->json($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        Log::info($request);
        $kategori = $this->kategoriTransaksiService->findKategori($id);

        $kategori->update([
            'nama_kategori' => $request['kategori'][0]['nama_kategori'],
            'tipe' => $request['kategori'][0]['tipe'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori updated successfully!',
        ], 200);
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function destroy($id)
    {
        try {
            // Cari role berdasarkan ID
            $user = $this->kategoriTransaksiService->findKategori($id);
            // Hapus role
            $user->delete();

            // Berikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'user berhasil dihapus.'
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
