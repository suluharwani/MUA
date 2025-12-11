<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Area extends BaseController
{
    protected $areaModel;
    
    public function __construct()
    {
        $this->areaModel = new \App\Models\AreaLayananModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kelola Area Layanan',
            'areas' => $this->areaModel->orderBy('jenis_area', 'DESC')
                                      ->orderBy('urutan', 'ASC')
                                      ->findAll(),
            'stats' => $this->areaModel->getStatistics(),
            'jenis_options' => $this->areaModel->getJenisAreaOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/area/index', $data);
    }
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Area Layanan',
            'jenis_options' => $this->areaModel->getJenisAreaOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/area/tambah', $data);
    }
    
    public function simpan()
    {
        $validationRules = [
            'nama_area' => 'required|min_length[3]|max_length[100]',
            'jenis_area' => 'required|in_list[utama,sekunder]',
            'biaya_tambahan' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_area' => $this->request->getPost('nama_area'),
            'jenis_area' => $this->request->getPost('jenis_area'),
            'keterangan' => $this->request->getPost('keterangan'),
            'biaya_tambahan' => $this->request->getPost('biaya_tambahan') ?? 0,
            'urutan' => $this->request->getPost('urutan') ?? 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($this->areaModel->save($data)) {
            return redirect()->to('/admin/area')->with('success', 'Area layanan berhasil ditambahkan.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan area layanan.');
    }
    
    public function edit($id)
    {
        $area = $this->areaModel->find($id);
        
        if (!$area) {
            return redirect()->to('/admin/area')->with('error', 'Area layanan tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Area Layanan',
            'area' => $area,
            'jenis_options' => $this->areaModel->getJenisAreaOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/area/edit', $data);
    }
    
    public function update($id)
    {
        $area = $this->areaModel->find($id);
        
        if (!$area) {
            return redirect()->to('/admin/area')->with('error', 'Area layanan tidak ditemukan.');
        }
        
        $validationRules = [
            'nama_area' => 'required|min_length[3]|max_length[100]',
            'jenis_area' => 'required|in_list[utama,sekunder]',
            'biaya_tambahan' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nama_area' => $this->request->getPost('nama_area'),
            'jenis_area' => $this->request->getPost('jenis_area'),
            'keterangan' => $this->request->getPost('keterangan'),
            'biaya_tambahan' => $this->request->getPost('biaya_tambahan') ?? 0,
            'urutan' => $this->request->getPost('urutan') ?? 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($this->areaModel->save($data)) {
            return redirect()->to('/admin/area')->with('success', 'Area layanan berhasil diperbarui.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui area layanan.');
    }
    
    public function hapus($id)
    {
        if ($this->areaModel->delete($id)) {
            return redirect()->to('/admin/area')->with('success', 'Area layanan berhasil dihapus.');
        }
        
        return redirect()->to('/admin/area')->with('error', 'Gagal menghapus area layanan.');
    }
    
    public function toggleStatus($id)
    {
        if ($this->areaModel->toggleStatus($id)) {
            return redirect()->back()->with('success', 'Status area berhasil diubah.');
        }
        
        return redirect()->back()->with('error', 'Gagal mengubah status area.');
    }
}