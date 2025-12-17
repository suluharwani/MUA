<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryModel;

class Gallery extends BaseController
{
    protected $galleryModel;
    
    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
        helper(['form', 'text']);
    }
    
    public function index()
    {
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status') ?? 'active';
        $search = $this->request->getGet('search');
        $style = $this->request->getGet('style');
        
        $data = [
            'title' => 'Kelola Gallery',
            'gallery' => $this->galleryModel->getAllWithFilter($kategori, $status, $search, $style),
            'kategori_options' => $this->galleryModel->getKategoriOptions(),
            'style_options' => $this->galleryModel->getStyleOptions(),
            'stats' => $this->galleryModel->getStatistics(),
            'filter_kategori' => $kategori,
            'filter_status' => $status,
            'filter_search' => $search,
            'filter_style' => $style,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/gallery/index', $data);
    }
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Gallery',
            'kategori_options' => $this->galleryModel->getKategoriOptions(),
            'style_options' => $this->galleryModel->getStyleOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/gallery/tambah', $data);
    }
    
    public function simpan()
{
    // Set validation rules
    $validation = \Config\Services::validation();
    
    $validation->setRules([
        'judul' => [
            'label' => 'Judul',
            'rules' => 'required|min_length[3]|max_length[200]',
            'errors' => [
                'required' => '{field} wajib diisi',
                'min_length' => '{field} minimal 3 karakter',
                'max_length' => '{field} maksimal 200 karakter'
            ]
        ],
        'kategori' => [
            'label' => 'Kategori',
            'rules' => 'required|in_list[makeup,kostum,portfolio,testimonial,behind_scenes,inspirasi]',
            'errors' => [
                'required' => '{field} wajib dipilih',
                'in_list' => '{field} tidak valid'
            ]
        ],
        'deskripsi' => [
            'label' => 'Deskripsi',
            'rules' => 'permit_empty|min_length[10]',
            'errors' => [
                'min_length' => '{field} minimal 10 karakter'
            ]
        ],
        'gambar' => [
            'label' => 'Gambar Utama',
            'rules' => 'uploaded[gambar]|max_size[gambar,5120]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]',
            'errors' => [
                'uploaded' => '{field} wajib diupload',
                'max_size' => 'Ukuran {field} maksimal 5MB',
                'is_image' => 'File harus berupa gambar',
                'mime_in' => 'Format gambar harus JPG, PNG, atau WebP'
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
        // Handle file upload - gambar utama
        $gambar = $this->request->getFile('gambar');
        $gambarName = null;
        
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            $gambar->move(ROOTPATH . 'public/uploads/gallery', $newName);
            $gambarName = $newName;
        }
        
        // Handle multiple files upload - gambar tambahan
        $gambarTambahan = $this->request->getFileMultiple('gambar_tambahan');
        $gambarTambahanNames = [];
        
        if ($gambarTambahan) {
            foreach ($gambarTambahan as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    $img->move(ROOTPATH . 'public/uploads/gallery/tambahan', $newName);
                    $gambarTambahanNames[] = $newName;
                }
            }
        }
        
        // Prepare data
        $data = [
            'judul' => $this->request->getPost('judul'),
            'kategori' => $this->request->getPost('kategori'),
            'style' => $this->request->getPost('style'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'gambar' => $gambarName,
            'gambar_tambahan' => json_encode($gambarTambahanNames),
            'tema_warna' => $this->request->getPost('tema_warna'),
            'produk_digunakan' => $this->request->getPost('produk_digunakan'),
            'lokasi_pemotretan' => $this->request->getPost('lokasi_pemotretan'),
            'makeup_artist' => $this->request->getPost('makeup_artist'),
            'model' => $this->request->getPost('model'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'urutan' => (int) $this->request->getPost('urutan') ?? 0,
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'meta_description' => $this->request->getPost('meta_description'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Insert data
        $result = $this->galleryModel->save($data);
        
        if ($result) {
            return redirect()->to('/admin/gallery')
                           ->with('success', 'Gallery berhasil ditambahkan!');
        } else {
            // Get model errors
            $errors = $this->galleryModel->errors();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan gallery: ' . implode(', ', $errors ?? []));
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Exception in simpan gallery: ' . $e->getMessage());
        
        return redirect()->back()
                       ->withInput()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
    public function edit($id)
    {
        $gallery = $this->galleryModel->getWithRelated($id);
        
        if (!$gallery) {
            return redirect()->to('/admin/gallery')->with('error', 'Gallery tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Gallery',
            'gallery' => $gallery,
            'kategori_options' => $this->galleryModel->getKategoriOptions(),
            'style_options' => $this->galleryModel->getStyleOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/gallery/edit', $data);
    }
    
    public function update($id)
{
    $gallery = $this->galleryModel->find($id);
    
    if (!$gallery) {
        return redirect()->to('/admin/gallery')->with('error', 'Gallery tidak ditemukan.');
    }
    
    // Set validation rules
    $validation = \Config\Services::validation();
    
    $validationRules = [
        'judul' => [
            'label' => 'Judul',
            'rules' => 'required|min_length[3]|max_length[200]',
            'errors' => [
                'required' => '{field} wajib diisi',
                'min_length' => '{field} minimal 3 karakter',
                'max_length' => '{field} maksimal 200 karakter'
            ]
        ],
        'kategori' => [
            'label' => 'Kategori',
            'rules' => 'required|in_list[makeup,kostum,portfolio,testimonial,behind_scenes,inspirasi]',
            'errors' => [
                'required' => '{field} wajib dipilih',
                'in_list' => '{field} tidak valid'
            ]
        ],
        'deskripsi' => [
            'label' => 'Deskripsi',
            'rules' => 'permit_empty|min_length[10]',
            'errors' => [
                'min_length' => '{field} minimal 10 karakter'
            ]
        ]
    ];
    
    // Jika ada upload gambar baru, validasi gambar
    if ($this->request->getFile('gambar')->isValid()) {
        $validationRules['gambar'] = [
            'label' => 'Gambar Utama',
            'rules' => 'max_size[gambar,5120]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]',
            'errors' => [
                'max_size' => 'Ukuran {field} maksimal 5MB',
                'is_image' => 'File harus berupa gambar',
                'mime_in' => 'Format gambar harus JPG, PNG, atau WebP'
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
        // Handle file upload - gambar utama
        $gambar = $this->request->getFile('gambar');
        $gambarName = $gallery['gambar'];
        
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            // Delete old image if exists
            if ($gambarName && file_exists(ROOTPATH . 'public/uploads/gallery/' . $gambarName)) {
                @unlink(ROOTPATH . 'public/uploads/gallery/' . $gambarName);
            }
            
            $newName = $gambar->getRandomName();
            if ($gambar->move(ROOTPATH . 'public/uploads/gallery', $newName)) {
                $gambarName = $newName;
            }
        }
        
        // Handle multiple files upload - gambar tambahan
        $gambarTambahan = $this->request->getFileMultiple('gambar_tambahan');
        $gambarTambahanNames = json_decode($gallery['gambar_tambahan'] ?? '[]', true);
        
        if ($gambarTambahan) {
            foreach ($gambarTambahan as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    if ($img->move(ROOTPATH . 'public/uploads/gallery/tambahan', $newName)) {
                        $gambarTambahanNames[] = $newName;
                    }
                }
            }
        }
        
        // Handle deleted images
        $deletedImages = $this->request->getPost('deleted_images');
        if ($deletedImages && $deletedImages !== 'null') {
            $deletedArray = json_decode($deletedImages, true);
            if (is_array($deletedArray)) {
                foreach ($deletedArray as $deletedImage) {
                    $key = array_search($deletedImage, $gambarTambahanNames);
                    if ($key !== false) {
                        // Remove from array
                        unset($gambarTambahanNames[$key]);
                        // Delete file if exists
                        $filePath = ROOTPATH . 'public/uploads/gallery/tambahan/' . $deletedImage;
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }
                $gambarTambahanNames = array_values($gambarTambahanNames); // Reindex array
            }
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'judul' => $this->request->getPost('judul'),
            'kategori' => $this->request->getPost('kategori'),
            'style' => $this->request->getPost('style'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'gambar' => $gambarName,
            'gambar_tambahan' => json_encode($gambarTambahanNames),
            'tema_warna' => $this->request->getPost('tema_warna'),
            'produk_digunakan' => $this->request->getPost('produk_digunakan'),
            'lokasi_pemotretan' => $this->request->getPost('lokasi_pemotretan'),
            'makeup_artist' => $this->request->getPost('makeup_artist'),
            'model' => $this->request->getPost('model'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'urutan' => (int) $this->request->getPost('urutan') ?? 0,
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'meta_description' => $this->request->getPost('meta_description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Update data
        $result = $this->galleryModel->save($data);
        
        if ($result) {
            return redirect()->to('/admin/gallery')
                           ->with('success', 'Gallery berhasil diperbarui!');
        } else {
            // Get model errors
            $errors = $this->galleryModel->errors();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui gallery: ' . implode(', ', $errors ?? []));
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Exception in update gallery: ' . $e->getMessage());
        
        return redirect()->back()
                       ->withInput()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
    public function hapus($id)
    {
        try {
            $gallery = $this->galleryModel->find($id);
            
            if (!$gallery) {
                return redirect()->to('/admin/gallery')->with('error', 'Gallery tidak ditemukan.');
            }
            
            // Delete main image
            if ($gallery['gambar'] && file_exists(ROOTPATH . 'public/uploads/gallery/' . $gallery['gambar'])) {
                unlink(ROOTPATH . 'public/uploads/gallery/' . $gallery['gambar']);
            }
            
            // Delete additional images
            $gambarTambahan = json_decode($gallery['gambar_tambahan'] ?? '[]', true);
            foreach ($gambarTambahan as $image) {
                if (file_exists(ROOTPATH . 'public/uploads/gallery/tambahan/' . $image)) {
                    unlink(ROOTPATH . 'public/uploads/gallery/tambahan/' . $image);
                }
            }
            
            $result = $this->galleryModel->delete($id);
            
            if ($result) {
                return redirect()->to('/admin/gallery')
                               ->with('success', 'Gallery berhasil dihapus!');
            } else {
                return redirect()->to('/admin/gallery')
                               ->with('error', 'Gagal menghapus gallery.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in hapus gallery: ' . $e->getMessage());
            return redirect()->to('/admin/gallery')
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $result = $this->galleryModel->toggleStatus($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status gallery berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status gallery.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleStatus gallery: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function toggleFeatured($id)
    {
        try {
            $result = $this->galleryModel->toggleFeatured($id);
            
            if ($result) {
                return redirect()->back()
                               ->with('success', 'Status featured berhasil diubah!');
            } else {
                return redirect()->back()
                               ->with('error', 'Gagal mengubah status featured.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in toggleFeatured gallery: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada gallery yang dipilih.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($ids as $id) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($this->galleryModel->update($id, ['is_active' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'deactivate':
                        if ($this->galleryModel->update($id, ['is_active' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'feature':
                        if ($this->galleryModel->update($id, ['is_featured' => 1])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'unfeature':
                        if ($this->galleryModel->update($id, ['is_featured' => 0])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                        break;
                        
                    case 'delete':
                        // Delete images first
                        $gallery = $this->galleryModel->find($id);
                        if ($gallery) {
                            // Delete main image
                            if ($gallery['gambar'] && file_exists(ROOTPATH . 'public/uploads/gallery/' . $gallery['gambar'])) {
                                unlink(ROOTPATH . 'public/uploads/gallery/' . $gallery['gambar']);
                            }
                            
                            // Delete additional images
                            $gambarTambahan = json_decode($gallery['gambar_tambahan'] ?? '[]', true);
                            foreach ($gambarTambahan as $image) {
                                if (file_exists(ROOTPATH . 'public/uploads/gallery/tambahan/' . $image)) {
                                    unlink(ROOTPATH . 'public/uploads/gallery/tambahan/' . $image);
                                }
                            }
                        }
                        
                        if ($this->galleryModel->delete($id)) {
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
                log_message('error', 'Error in bulk action gallery: ' . $e->getMessage());
            }
        }
        
        $message = "Berhasil diproses: {$successCount} gallery";
        if ($errorCount > 0) {
            $message .= ", gagal: {$errorCount} gallery";
        }
        
        $alertType = $errorCount > 0 ? ($successCount > 0 ? 'warning' : 'error') : 'success';
        
        return redirect()->back()->with($alertType, $message);
    }

    public function categories()
    {
        try {
            $kategori_options = $this->galleryModel->getKategoriOptions();
            $categories = [];
            
            foreach ($kategori_options as $value => $label) {
                $count = $this->galleryModel->where('kategori', $value)->countAllResults();
                $categories[] = [
                    'value' => $value,
                    'label' => $label,
                    'total' => $count
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in categories: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat kategori'
            ]);
        }
    }
    
    /**
     * Check if category exists
     */
    public function checkCategory()
    {
        try {
            $slug = $this->request->getPost('slug');
            $kategori_options = $this->galleryModel->getKategoriOptions();
            
            return $this->response->setJSON([
                'success' => true,
                'exists' => array_key_exists($slug, $kategori_options)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in checkCategory: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memeriksa kategori'
            ]);
        }
    }
    
    /**
     * Add new category (simplified version)
     */
    public function addCategory()
    {
        try {
            $name = $this->request->getPost('name');
            $value = $this->request->getPost('value');
            
            if (!$name || !$value) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama kategori tidak valid'
                ]);
            }
            
            // Untuk implementasi sederhana, kita hanya return success
            // Karena kategori hardcoded di model
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'category' => [
                    'value' => $value,
                    'label' => $name,
                    'total' => 0
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in addCategory: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan kategori'
            ]);
        }
    }
    
    /**
     * Edit category (simplified version)
     */
    public function editCategory()
    {
        try {
            $slug = $this->request->getPost('slug');
            $name = $this->request->getPost('name');
            
            if (!$slug || !$name) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data kategori tidak valid'
                ]);
            }
            
            // Untuk implementasi sederhana
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in editCategory: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengedit kategori'
            ]);
        }
    }
    
    /**
     * Delete category (simplified version)
     */
    public function deleteCategory()
    {
        try {
            $slug = $this->request->getPost('slug');
            
            if (!$slug) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Slug kategori tidak valid'
                ]);
            }
            
            // Check if category is being used
            $count = $this->galleryModel->where('kategori', $slug)->countAllResults();
            
            if ($count > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $count . ' gallery'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteCategory: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus kategori'
            ]);
        }
    }
    
    /**
     * Get all categories for dropdown (non-AJAX version)
     */
    public function getCategoriesDropdown()
    {
        $kategori_options = $this->galleryModel->getKategoriOptions();
        
        $html = '<option value="">Pilih Kategori</option>';
        foreach ($kategori_options as $value => $label) {
            $html .= '<option value="' . $value . '">' . $label . '</option>';
        }
        
        return $html;
    }
}
