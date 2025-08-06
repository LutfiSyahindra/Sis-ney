<?php

namespace App\Services\asetTabungan;

use App\Repositories\asetTabungan\asetTabunganRepository; 

class asetTabunganService
{
    protected $asetTabunganRepository;

    public function __construct(asetTabunganRepository $asetTabunganRepository)
    {
        $this->asetTabunganRepository = $asetTabunganRepository;
    }

    public function getAllAsetTabungan()
    {
        return $this->asetTabunganRepository->getAllAset();
    }

    public function getTabunganMasuk()
    {
        return $this->asetTabunganRepository->getTabunganMasuk();
    }

    public function createTabungan(array $data)
    {
        return $this->asetTabunganRepository->createAset($data);
    }

    public function findTabungan($id){
        return $this->asetTabunganRepository->findAset($id);
    }

}
