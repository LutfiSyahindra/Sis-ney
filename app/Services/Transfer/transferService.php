<?php

namespace App\Services\Transfer;

use App\Repositories\Transfer\TransferRepository; 

class transferService
{
    protected $TransferRepository;

    public function __construct(TransferRepository $TransferRepository)
    {
        $this->TransferRepository = $TransferRepository;
    }

    public function getAllTransfer()
    {
        return $this->TransferRepository->getAllTransfer();
    }

    public function createTransfer(array $data)
    {
        return $this->TransferRepository->createTransfer($data);
    }

    public function findTransfer($id){
        return $this->TransferRepository->findTransfer($id);
    }

    public function getMutasiByAset($asetId, $startDate = null, $endDate = null)
    {
        $masuk = $this->TransferRepository->findMutasiMasuk($asetId, $startDate, $endDate);
        $keluar = $this->TransferRepository->findMutasiKeluar($asetId, $startDate, $endDate);

        return [
            'masuk' => $masuk,
            'keluar' => $keluar
        ];
    }

}
