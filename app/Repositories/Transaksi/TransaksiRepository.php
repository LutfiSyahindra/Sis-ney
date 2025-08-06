<?php

namespace App\Repositories\Transaksi;

use App\Models\TransaksiModel;
use App\Models\TransferModel;
use App\Models\User;
use App\Repositories\asetTabungan\asetTabunganRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class TransaksiRepository
{
    protected $asetTabunganRepository;

    public function __construct(AsetTabunganRepository $asetTabunganRepository)
    {
        $this->asetTabunganRepository = $asetTabunganRepository;
    }
    public function createTransaksi(array $data)
    {
        return DB::transaction(function () use ($data) {
            $transaksi = TransaksiModel::create($data);

            // Ambil aset
            $aset = $this->asetTabunganRepository->findAset($data['aset_tabungan_id']);

            if ($data['tipe'] === 'pengeluaran') {
                if ($aset->saldo < $data['nominal']) {
                    throw new \Exception("Saldo tidak mencukupi untuk pengeluaran.");
                }
                $aset->saldo -= $data['nominal'];
            } elseif ($data['tipe'] === 'pemasukan') {
                $aset->saldo += $data['nominal'];
            }

            $aset->save();

            return $transaksi;
        });
    }

    public function getAllTransaksi()
    {
        return TransaksiModel::with(['kategori', 'aset', 'user'])->get();
    }

    public function getTransaksi($start = null, $end = null)
    {
        $query = TransaksiModel::with(['user', 'kategori', 'aset']);

        if ($start && $end) {
            if ($start === $end) {
                // Kalau hanya 1 tanggal (flatpickr belum lengkap)
                $query->whereDate('tanggal_transaksi', $start);
            } else {
                // Kalau 2 tanggal, filter range
                $query->whereBetween('tanggal_transaksi', [$start, $end]);
            }
        } elseif ($start) {
            $query->whereDate('tanggal_transaksi', $start);
        }

        return $query->get();
    }

    public function getTransaksiMY($start, $end){
        
    }



    public function findTransaksi($id){
        return TransaksiModel::findOrFail($id);
    }

    public function updateTransaksi(string $id, array $data)
    {
        $transaksi = TransaksiModel::findOrFail($id);
        $transaksi->update($data);
        return $transaksi;
    }

        
}
