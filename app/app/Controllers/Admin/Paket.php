<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Paket extends BaseController
{
    public function index()
    {
        $paketModel = new \App\Models\PaketModel();
        
        $data = [
            'title' => 'Kelola Paket Makeup',
            'paket' => $paketModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/paket/index', $data);
    }
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Paket Makeup',
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/paket/tambah', $data);
    }
    
    public function simpan()
    {
        $paketModel = new \App\Models\PaketModel();
        
        $validationRules = [
            'nama_paket' => 'required',
            'harga' => 'required|numeric',
            'deskripsi' => 'required'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_paket' => $this->request->getPost('nama_paket'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga' => $this->request->getPost('harga'),
            'durasi' => $this->request->getPost('durasi'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'features' => json_encode(explode("\n", $this->request->getPost('features'))),
            'urutan' => $this->request->getPost('urutan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($paketModel->save($data)) {
            return redirect()->to('/admin/paket')->with('success', 'Paket berhasil ditambahkan.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan paket.');
    }
    
    public function edit($id)
    {
        $paketModel = new \App\Models\PaketModel();
        $paket = $paketModel->find($id);
        
        if (!$paket) {
            return redirect()->to('/admin/paket')->with('error', 'Paket tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Paket Makeup',
            'paket' => $paket,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/paket/edit', $data);
    }
    
    public function update($id)
    {
        $paketModel = new \App\Models\PaketModel();
        
        $validationRules = [
            'nama_paket' => 'required',
            'harga' => 'required|numeric',
            'deskripsi' => 'required'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nama_paket' => $this->request->getPost('nama_paket'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga' => $this->request->getPost('harga'),
            'durasi' => $this->request->getPost('durasi'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'features' => json_encode(explode("\n", $this->request->getPost('features'))),
            'urutan' => $this->request->getPost('urutan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($paketModel->save($data)) {
            return redirect()->to('/admin/paket')->with('success', 'Paket berhasil diperbarui.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui paket.');
    }
    
    public function hapus($id)
    {
        $paketModel = new \App\Models\PaketModel();
        
        if ($paketModel->delete($id)) {
            return redirect()->to('/admin/paket')->with('success', 'Paket berhasil dihapus.');
        }
        
        return redirect()->to('/admin/paket')->with('error', 'Gagal menghapus paket.');
    }
}