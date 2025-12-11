<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Paket extends BaseController
{
    protected $paketModel;
    
    public function __construct()
    {
        $this->paketModel = new \App\Models\PaketModel();
        helper(['form', 'text']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kelola Paket Makeup',
            'paket' => $this->paketModel->orderBy('urutan', 'ASC')->findAll(),
            'stats' => $this->paketModel->getStatistics(),
            'validation' => \Config\Services::validation()
        ];
        
        // Decode features untuk setiap paket
        foreach ($data['paket'] as &$p) {
            $p['features'] = json_decode($p['features'] ?? '[]', true);
        }
        
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
        // Set validation rules
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama_paket' => [
                'label' => 'Nama Paket',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 100 karakter'
                ]
            ],
            'harga' => [
                'label' => 'Harga',
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'numeric' => '{field} harus berupa angka',
                    'greater_than' => '{field} harus lebih dari 0'
                ]
            ],
            'deskripsi' => [
                'label' => 'Deskripsi',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 10 karakter'
                ]
            ]
        ]);
        
        // Run validation
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        try {
            // Parse harga from formatted string
            $harga = $this->request->getPost('harga');
            if (is_string($harga)) {
                $harga = str_replace(['Rp', '.', ',', ' '], '', $harga);
                $harga = (float) $harga;
            }
            
            // Prepare data
            $data = [
                'nama_paket' => $this->request->getPost('nama_paket'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'harga' => $harga,
                'durasi' => $this->request->getPost('durasi'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'features' => $this->request->getPost('features'),
                'urutan' => (int) $this->request->getPost('urutan') ?? 0,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert data
            $result = $this->paketModel->save($data);
            
            if ($result) {
                return redirect()->to('/admin/paket')
                               ->with('success', 'Paket berhasil ditambahkan!');
            } else {
                // Get model errors
                $errors = $this->paketModel->errors();
                
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Gagal menambahkan paket: ' . implode(', ', $errors ?? []));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in simpan: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $paket = $this->paketModel->find($id);
        
        if (!$paket) {
            return redirect()->to('/admin/paket')->with('error', 'Paket tidak ditemukan.');
        }
        
        // Decode features
        $paket['features'] = json_decode($paket['features'] ?? '[]', true);
        
        $data = [
            'title' => 'Edit Paket Makeup',
            'paket' => $paket,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/paket/edit', $data);
    }
    
    public function update($id)
    {
        $paket = $this->paketModel->find($id);
        
        if (!$paket) {
            return redirect()->to('/admin/paket')->with('error', 'Paket tidak ditemukan.');
        }
        
        // Set validation rules
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama_paket' => [
                'label' => 'Nama Paket',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 100 karakter'
                ]
            ],
            'harga' => [
                'label' => 'Harga',
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'numeric' => '{field} harus berupa angka',
                    'greater_than' => '{field} harus lebih dari 0'
                ]
            ],
            'deskripsi' => [
                'label' => 'Deskripsi',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 10 karakter'
                ]
            ]
        ]);
        
        // Run validation
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        try {
            // Parse harga from formatted string
            $harga = $this->request->getPost('harga');
            if (is_string($harga)) {
                $harga = str_replace(['Rp', '.', ',', ' '], '', $harga);
                $harga = (float) $harga;
            }
            
            // Prepare data
            $data = [
                'id' => $id,
                'nama_paket' => $this->request->getPost('nama_paket'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'harga' => $harga,
                'durasi' => $this->request->getPost('durasi'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'features' => $this->request->getPost('features'),
                'urutan' => (int) $this->request->getPost('urutan') ?? 0,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Update data
            $result = $this->paketModel->save($data);
            
            if ($result) {
                return redirect()->to('/admin/paket')
                               ->with('success', 'Paket berhasil diperbarui!');
            } else {
                // Get model errors
                $errors = $this->paketModel->errors();
                
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Gagal memperbarui paket: ' . implode(', ', $errors ?? []));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in update: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function hapus($id)
    {
        try {
            $result = $this->paketModel->delete($id);
            
            if ($result) {
                return redirect()->to('/admin/paket')
                               ->with('success', 'Paket berhasil dihapus!');
            } else {
                return redirect()->to('/admin/paket')
                               ->with('error', 'Gagal menghapus paket.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in hapus: ' . $e->getMessage());
            return redirect()->to('/admin/paket')
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $result = $this->paketModel->toggleStatus($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status paket berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status paket.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleStatus: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleFeatured($id)
    {
        try {
            $result = $this->paketModel->toggleFeatured($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status featured berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status featured.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleFeatured: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Tambahkan method bulkAction yang hilang
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada paket yang dipilih.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($ids as $id) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($this->paketModel->update($id, ['is_active' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'deactivate':
                        if ($this->paketModel->update($id, ['is_active' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'feature':
                        if ($this->paketModel->update($id, ['is_featured' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'unfeature':
                        if ($this->paketModel->update($id, ['is_featured' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'delete':
                        if ($this->paketModel->delete($id)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    default:
                        $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                log_message('error', 'Error in bulk action: ' . $e->getMessage());
            }
        }
        
        $message = "Berhasil diproses: {$successCount} paket";
        if ($errorCount > 0) {
            $message .= ", gagal: {$errorCount} paket";
        }
        
        $alertType = $errorCount > 0 ? ($successCount > 0 ? 'warning' : 'error') : 'success';
        
        return redirect()->back()->with($alertType, $message);
    }
}