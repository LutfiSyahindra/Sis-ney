<?php

namespace App\Services\kategoriTransaksi;

use App\Repositories\kategoriTransaksi\kategoriTransaksiRepository; 

class kategoriTransaksiService
{
    protected $kategoriTransaksiRepository;

    public function __construct(kategoriTransaksiRepository $kategoriTransaksiRepository)
    {
        $this->kategoriTransaksiRepository = $kategoriTransaksiRepository;
    }

    public function getAllKategoriTransaksi()
    {
        return $this->kategoriTransaksiRepository->getAllKategori();
    }

    public function getKategori()
    {
        return $this->kategoriTransaksiRepository->getKategori();
    }

    public function getKategoriPengeluaran()
    {
        return $this->kategoriTransaksiRepository->getKategoriPengeluaran();
    }

    public function createKategori(array $data)
    {
        return $this->kategoriTransaksiRepository->createKategori($data);
    }

    public function findKategori($id){
        return $this->kategoriTransaksiRepository->findKategori($id);
    }

}
