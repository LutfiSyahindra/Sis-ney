<?php

namespace App\Services\Transaksi;

use App\Repositories\Transaksi\TransaksiRepository; 

class transaksiService
{
    protected $TransaksiRepository;

    public function __construct(TransaksiRepository $TransaksiRepository)
    {
        $this->TransaksiRepository = $TransaksiRepository;
    }

    public function createTransaksi(array $data)
    {
        return $this->TransaksiRepository->createTransaksi($data);
    }

    public function getAllTransaksi()
    {
        return $this->TransaksiRepository->getAllTransaksi();
    }

    public function findTransaksi($id)
    {
        return $this->TransaksiRepository->findTransaksi($id);
    }

    public function updateTransaksi(string $id, array $data)
    {
        return $this->TransaksiRepository->updateTransaksi($id, $data);
    }

    public function getTransaksi($start = null, $end = null)
    {
        return $this->TransaksiRepository->getTransaksi($start, $end);
    }

}
