<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Mitra extends BaseController
{
    protected $mitraModel;
    
    public function __construct()
    {
        $this->mitraModel = new \App\Models\MitraModel();
    }
    
    public function index()
    {
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        
        $data = [
            'title' => 'Kelola Mitra',
            'mitra' => $this->mitraModel->getAllWithFilter($kategori, $status, $search),
            'kategori_options' => $this->mitraModel->getKategoriOptions(),
            'kategori_filter' => $kategori,
            'status_filter' => $status,
            'search_term' => $search,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/mitra/index', $data);
    }
    
    // Tambahkan method CRUD lainnya sesuai kebutuhan...
}