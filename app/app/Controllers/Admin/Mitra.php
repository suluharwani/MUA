<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MitraModel;

class Mitra extends BaseController
{
    protected $mitraModel;
    
    public function __construct()
    {
        $this->mitraModel = new MitraModel();
        helper(['form', 'text', 'url']);
    }
    
    public function index()
{
    $status = $this->request->getGet('status') ?? 'active';
    $search = $this->request->getGet('search');
    $spesialisasi = $this->request->getGet('spesialisasi');
    
    // Gunakan parameter array
    $filterParams = [
        'status' => $status,
        'search' => $search,
        'spesialisasi' => $spesialisasi
    ];
    
    $data = [
        'title' => 'Kelola Mitra',
        'mitra' => $this->mitraModel->getAllWithFilter($filterParams),
        'spesialisasi_options' => $this->mitraModel->getSpesialisasiOptions(),
        'stats' => $this->mitraModel->getStatistics(),
        'filter_status' => $status,
        'filter_search' => $search,
        'filter_spesialisasi' => $spesialisasi,
        'validation' => \Config\Services::validation()
    ];
    
    return view('admin/mitra/index', $data);
}
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Mitra',
            'spesialisasi_options' => $this->mitraModel->getSpesialisasiOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/mitra/tambah', $data);
    }
    
    public function simpan()
    {
        // Set validation rules
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama_mitra' => [
                'label' => 'Nama Mitra',
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 200 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email',
                'errors' => [
                    'valid_email' => 'Format {field} tidak valid'
                ]
            ],
            'telepon' => [
                'label' => 'Telepon',
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => '{field} maksimal 20 karakter'
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
            // Generate slug
            $nama_mitra = $this->request->getPost('nama_mitra');
            $slug = $this->mitraModel->generateSlug($nama_mitra);
            
            // Handle file upload - foto
            $foto = $this->request->getFile('foto');
            $fotoName = null;
            
            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                $newName = $foto->getRandomName();
                $foto->move(ROOTPATH . 'public/uploads/mitra', $newName);
                $fotoName = $newName;
            }
            
            // Handle portofolio files upload
            $portofolioFiles = $this->request->getFileMultiple('portofolio_files');
            $portofolioFileNames = [];
            
            if ($portofolioFiles) {
                foreach ($portofolioFiles as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(ROOTPATH . 'public/uploads/mitra/portofolio', $newName);
                        $portofolioFileNames[] = $newName;
                    }
                }
            }
            
            // Prepare data
            $data = [
                'nama_mitra' => $nama_mitra,
                'slug' => $slug,
                'deskripsi' => $this->request->getPost('deskripsi'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'website' => $this->request->getPost('website'),
                'instagram' => $this->request->getPost('instagram'),
                'facebook' => $this->request->getPost('facebook'),
                'foto' => $fotoName,
                'spesialisasi' => $this->request->getPost('spesialisasi'),
                'pengalaman' => $this->request->getPost('pengalaman'),
                'portofolio' => json_encode($portofolioFileNames),
                'keahlian' => $this->request->getPost('keahlian'),
                'tarif' => $this->request->getPost('tarif'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'urutan' => (int) $this->request->getPost('urutan') ?? 0,
                'meta_keywords' => $this->request->getPost('meta_keywords'),
                'meta_description' => $this->request->getPost('meta_description'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert data
            $result = $this->mitraModel->save($data);
            
            if ($result) {
                return redirect()->to('/admin/mitra')
                               ->with('success', 'Mitra berhasil ditambahkan!');
            } else {
                // Get model errors
                $errors = $this->mitraModel->errors();
                
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Gagal menambahkan mitra: ' . implode(', ', $errors ?? []));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in simpan mitra: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $mitra = $this->mitraModel->find($id);
        
        if (!$mitra) {
            return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
        }
        
        // Decode portofolio files
        $mitra['portofolio'] = json_decode($mitra['portofolio'] ?? '[]', true);
        
        $data = [
            'title' => 'Edit Mitra',
            'mitra' => $mitra,
            'spesialisasi_options' => $this->mitraModel->getSpesialisasiOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/mitra/edit', $data);
    }
    
    public function update($id)
    {
        $mitra = $this->mitraModel->find($id);
        
        if (!$mitra) {
            return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
        }
        
        // Set validation rules
        $validation = \Config\Services::validation();
        
        $validationRules = [
            'nama_mitra' => [
                'label' => 'Nama Mitra',
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 200 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email',
                'errors' => [
                    'valid_email' => 'Format {field} tidak valid'
                ]
            ],
            'telepon' => [
                'label' => 'Telepon',
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => '{field} maksimal 20 karakter'
                ]
            ]
        ];
        
        // Jika slug berubah, validasi unik
        $newSlug = url_title($this->request->getPost('nama_mitra'), '-', true);
        if ($newSlug !== $mitra['slug']) {
            $validationRules['slug'] = [
                'label' => 'Slug',
                'rules' => 'required|is_unique[mitra.slug,id,' . $id . ']',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'is_unique' => '{field} sudah digunakan'
                ]
            ];
        }
        
        $validation->setRules($validationRules);
        
        // Run validation
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        try {
            // Generate new slug jika berubah
            $slug = $mitra['slug'];
            if ($newSlug !== $slug) {
                $slug = $newSlug;
                
                // Check uniqueness
                $exists = $this->mitraModel->where('slug', $slug)->where('id !=', $id)->first();
                if ($exists) {
                    $slug .= '-' . time();
                }
            }
            
            // Handle file upload - foto
            $foto = $this->request->getFile('foto');
            $fotoName = $mitra['foto'];
            
            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                // Delete old image if exists
                if ($fotoName && file_exists(ROOTPATH . 'public/uploads/mitra/' . $fotoName)) {
                    @unlink(ROOTPATH . 'public/uploads/mitra/' . $fotoName);
                }
                
                $newName = $foto->getRandomName();
                if ($foto->move(ROOTPATH . 'public/uploads/mitra', $newName)) {
                    $fotoName = $newName;
                }
            }
            
            // Handle portofolio files upload
            $portofolioFiles = $this->request->getFileMultiple('portofolio_files');
            $portofolioFileNames = json_decode($mitra['portofolio'] ?? '[]', true);
            
            if ($portofolioFiles) {
                foreach ($portofolioFiles as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        if ($file->move(ROOTPATH . 'public/uploads/mitra/portofolio', $newName)) {
                            $portofolioFileNames[] = $newName;
                        }
                    }
                }
            }
            
            // Handle deleted portofolio files
            $deletedFiles = $this->request->getPost('deleted_portofolio');
            if ($deletedFiles && $deletedFiles !== 'null') {
                $deletedArray = json_decode($deletedFiles, true);
                if (is_array($deletedArray)) {
                    foreach ($deletedArray as $deletedFile) {
                        $key = array_search($deletedFile, $portofolioFileNames);
                        if ($key !== false) {
                            // Remove from array
                            unset($portofolioFileNames[$key]);
                            // Delete file if exists
                            $filePath = ROOTPATH . 'public/uploads/mitra/portofolio/' . $deletedFile;
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                    $portofolioFileNames = array_values($portofolioFileNames); // Reindex array
                }
            }
            
            // Prepare data
            $data = [
                'id' => $id,
                'nama_mitra' => $this->request->getPost('nama_mitra'),
                'slug' => $slug,
                'deskripsi' => $this->request->getPost('deskripsi'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'website' => $this->request->getPost('website'),
                'instagram' => $this->request->getPost('instagram'),
                'facebook' => $this->request->getPost('facebook'),
                'foto' => $fotoName,
                'spesialisasi' => $this->request->getPost('spesialisasi'),
                'pengalaman' => $this->request->getPost('pengalaman'),
                'portofolio' => json_encode($portofolioFileNames),
                'keahlian' => $this->request->getPost('keahlian'),
                'tarif' => $this->request->getPost('tarif'),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'urutan' => (int) $this->request->getPost('urutan') ?? 0,
                'meta_keywords' => $this->request->getPost('meta_keywords'),
                'meta_description' => $this->request->getPost('meta_description'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Update data
            $result = $this->mitraModel->save($data);
            
            if ($result) {
                return redirect()->to('/admin/mitra')
                               ->with('success', 'Mitra berhasil diperbarui!');
            } else {
                // Get model errors
                $errors = $this->mitraModel->errors();
                
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Gagal memperbarui mitra: ' . implode(', ', $errors ?? []));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in update mitra: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function hapus($id)
    {
        try {
            $mitra = $this->mitraModel->find($id);
            
            if (!$mitra) {
                return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
            }
            
            // Delete foto if exists
            if ($mitra['foto'] && file_exists(ROOTPATH . 'public/uploads/mitra/' . $mitra['foto'])) {
                @unlink(ROOTPATH . 'public/uploads/mitra/' . $mitra['foto']);
            }
            
            // Delete portofolio files
            $portofolioFiles = json_decode($mitra['portofolio'] ?? '[]', true);
            foreach ($portofolioFiles as $file) {
                $filePath = ROOTPATH . 'public/uploads/mitra/portofolio/' . $file;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            
            $result = $this->mitraModel->delete($id);
            
            if ($result) {
                return redirect()->to('/admin/mitra')
                               ->with('success', 'Mitra berhasil dihapus!');
            } else {
                return redirect()->to('/admin/mitra')
                               ->with('error', 'Gagal menghapus mitra.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in hapus mitra: ' . $e->getMessage());
            return redirect()->to('/admin/mitra')
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $result = $this->mitraModel->toggleStatus($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status mitra berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status mitra.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleStatus mitra: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleFeatured($id)
    {
        try {
            $result = $this->mitraModel->toggleFeatured($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status featured berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status featured.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleFeatured mitra: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada mitra yang dipilih.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($ids as $id) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($this->mitraModel->update($id, ['is_active' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'deactivate':
                        if ($this->mitraModel->update($id, ['is_active' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'feature':
                        if ($this->mitraModel->update($id, ['is_featured' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'unfeature':
                        if ($this->mitraModel->update($id, ['is_featured' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'delete':
                        // Delete files first
                        $mitra = $this->mitraModel->find($id);
                        if ($mitra) {
                            // Delete foto
                            if ($mitra['foto'] && file_exists(ROOTPATH . 'public/uploads/mitra/' . $mitra['foto'])) {
                                @unlink(ROOTPATH . 'public/uploads/mitra/' . $mitra['foto']);
                            }
                            
                            // Delete portofolio files
                            $portofolioFiles = json_decode($mitra['portofolio'] ?? '[]', true);
                            foreach ($portofolioFiles as $file) {
                                $filePath = ROOTPATH . 'public/uploads/mitra/portofolio/' . $file;
                                if (file_exists($filePath)) {
                                    @unlink($filePath);
                                }
                            }
                        }
                        
                        if ($this->mitraModel->delete($id)) {
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
                log_message('error', 'Error in bulk action mitra: ' . $e->getMessage());
            }
        }
        
        $message = "Berhasil diproses: {$successCount} mitra";
        if ($errorCount > 0) {
            $message .= ", gagal: {$errorCount} mitra";
        }
        
        $alertType = $errorCount > 0 ? ($successCount > 0 ? 'warning' : 'error') : 'success';
        
        return redirect()->back()->with($alertType, $message);
    }
}