<?php
// File: app/Controllers/Admin/KostumAjax.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KostumModel;

class KostumAjax extends BaseController
{
    protected $kostumModel;
    protected $kategoriOptions = [];
    protected $kondisiOptions = [];

    public function __construct()
    {
        $this->kostumModel = new KostumModel();
        $this->kategoriOptions = $this->kostumModel->getKategoriOptions();
        $this->kondisiOptions = $this->kostumModel->getKondisiOptions();
        helper(['form', 'url', 'text','image']);
    }

    /**
     * Tampilkan halaman utama dengan AJAX
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola Kostum (AJAX)',
            'kategori_options' => $this->kategoriOptions,
            'kondisi_options' => $this->kondisiOptions
        ];
        
        return view('admin/kostum/ajax_index', $data);
    }

    /**
     * Get all kostum (JSON for AJAX)
     */
    public function getKostum()
    {
        // Get parameters
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        $kategori = $this->request->getPost('kategori');
        $status = $this->request->getPost('status');

        // Build query
        $builder = $this->kostumModel->builder();
        
        // Total records
        $totalRecords = $builder->countAll();
        
        // Apply filters
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('nama_kostum', $searchValue)
                    ->orLike('deskripsi', $searchValue)
                    ->orLike('kategori', $searchValue)
                    ->groupEnd();
        }
        
        if (!empty($kategori)) {
            $builder->where('kategori', $kategori);
        }
        
        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('is_active', 0);
        }
        
        // Filtered records
        $filteredRecords = $builder->countAllResults(false);
        
        // Get data with pagination
        $builder->orderBy('created_at', 'DESC')
                ->limit($length, $start);
        
        $query = $builder->get();
        $kostum = $query->getResultArray();
        
        // Format data for DataTables
        $data = [];
        foreach ($kostum as $row) {
            // Status badge
            $statusBadge = $row['is_active'] 
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-danger">Nonaktif</span>';
            
            // Featured badge
            $featuredBadge = $row['is_featured']
                ? '<span class="badge bg-warning">Featured</span>'
                : '<span class="badge bg-secondary">-</span>';
            
            // Stock badge
            $stockClass = 'bg-success';
            if ($row['stok_tersedia'] == 0) {
                $stockClass = 'bg-danger';
            } elseif ($row['stok_tersedia'] <= 2) {
                $stockClass = 'bg-warning';
            }
            $stockBadge = '<span class="badge ' . $stockClass . '">' . $row['stok_tersedia'] . '/' . $row['stok'] . '</span>';
            
            // Image
            $image = '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">';
            if (!empty($row['gambar'])) {
                $image = '<img src="' . base_url('uploads/kostum/' . $row['gambar']) . '" 
                            alt="' . $row['nama_kostum'] . '" 
                            class="rounded" 
                            style="width: 60px; height: 60px; object-fit: cover;">';
            } else {
                $image = '<i class="bi bi-image text-muted"></i>';
            }
            $image .= '</div>';
            
            // Action buttons
            $actions = '
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary view-btn" data-id="' . $row['id'] . '" title="Lihat">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning edit-btn" data-id="' . $row['id'] . '" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-' . ($row['is_active'] ? 'danger' : 'success') . ' toggle-status-btn" 
                            data-id="' . $row['id'] . '" 
                            data-status="' . $row['is_active'] . '"
                            title="' . ($row['is_active'] ? 'Nonaktifkan' : 'Aktifkan') . '">
                        <i class="bi bi-power"></i>
                    </button>
                    <button class="btn btn-outline-' . ($row['is_featured'] ? 'secondary' : 'warning') . ' toggle-featured-btn" 
                            data-id="' . $row['id'] . '" 
                            data-featured="' . $row['is_featured'] . '"
                            title="' . ($row['is_featured'] ? 'Hapus Featured' : 'Jadikan Featured') . '">
                        <i class="bi bi-star"></i>
                    </button>
                    <button class="btn btn-outline-danger delete-btn" data-id="' . $row['id'] . '" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            ';
            
            $data[] = [
                'checkbox' => '<input type="checkbox" class="row-checkbox" value="' . $row['id'] . '">',
                'gambar' => $image,
                'nama_kostum' => '<strong>' . $row['nama_kostum'] . '</strong><br><small class="text-muted">' . character_limiter($row['deskripsi'], 50) . '</small>',
                'kategori' => '<span class="badge bg-light text-dark">' . ($this->kategoriOptions[$row['kategori']] ?? $row['kategori']) . '</span>',
                'harga_sewa' => 'Rp ' . number_format($row['harga_sewa'], 0, ',', '.') . '<br><small class="text-muted">' . $row['durasi_sewa'] . '</small>',
                'stok' => $stockBadge,
                'status' => $statusBadge,
                'featured' => $featuredBadge,
                'created_at' => date('d/m/Y', strtotime($row['created_at'])),
                'aksi' => $actions
            ];
        }
        
        // Return JSON response
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
        
        return $this->response->setJSON($response);
    }

    /**
     * Get single kostum (JSON)
     */
    public function getKostumDetail($id)
    {
        $kostum = $this->kostumModel->find($id);
        
        if (!$kostum) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ]);
        }
        
        // Decode spesifikasi jika ada
        if (!empty($kostum['spesifikasi'])) {
            $spesifikasi = json_decode($kostum['spesifikasi'], true);
            $kostum['spesifikasi_text'] = is_array($spesifikasi) ? implode("\n", $spesifikasi) : '';
        } else {
            $kostum['spesifikasi_text'] = '';
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $kostum
        ]);
    }

    /**
     * Save kostum (AJAX)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'nama_kostum' => 'required|min_length[3]|max_length[100]',
            'kategori' => 'required',
            'harga_sewa' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'max_size[gambar,20480]|is_image[gambar]'
        ];

        if (!$validation->setRules($rules)->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            // Handle file upload
            $gambarName = null;
            $gambarFile = $this->request->getFile('gambar');
            
            if ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
                $gambarName = $gambarFile->getRandomName();
                $gambarFile->move(ROOTPATH . 'public/uploads/kostum', $gambarName);
            }

            // Format harga
            $hargaSewa = str_replace(['.', ','], '', $this->request->getPost('harga_sewa'));
            
            // Format spesifikasi
            $spesifikasi = $this->request->getPost('spesifikasi');
            $spesifikasiJson = null;
            
            if (!empty($spesifikasi)) {
                $lines = explode("\n", $spesifikasi);
                $cleanedLines = array_filter(array_map('trim', $lines));
                
                if (!empty($cleanedLines)) {
                    $spesifikasiJson = json_encode($cleanedLines, JSON_UNESCAPED_UNICODE);
                }
            }

            $data = [
                'kategori' => $this->request->getPost('kategori'),
                'nama_kostum' => $this->request->getPost('nama_kostum'),
                'deskripsi' => $this->request->getPost('deskripsi') ?? '',
                'harga_sewa' => (float) $hargaSewa,
                'durasi_sewa' => $this->request->getPost('durasi_sewa') ?? '3 hari',
                'spesifikasi' => $spesifikasiJson,
                'gambar' => $gambarName,
                'ukuran' => $this->request->getPost('ukuran') ?? '',
                'warna' => $this->request->getPost('warna') ?? '',
                'bahan' => $this->request->getPost('bahan') ?? '',
                'kondisi' => $this->request->getPost('kondisi') ?? 'baik',
                'stok' => (int) $this->request->getPost('stok'),
                'stok_tersedia' => (int) ($this->request->getPost('stok_tersedia') ?: $this->request->getPost('stok')),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'urutan' => (int) ($this->request->getPost('urutan') ?: 0)
            ];

            // Handle custom duration
            if ($this->request->getPost('durasi_sewa') === 'Kustom' && $this->request->getPost('durasi_kustom')) {
                $data['durasi_sewa'] = $this->request->getPost('durasi_kustom');
            }

            // Generate slug
            $slug = url_title($data['nama_kostum'], '-', true);
            $originalSlug = $slug;
            $counter = 1;
            
            while ($this->kostumModel->where('slug', $slug)->countAllResults() > 0) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $data['slug'] = $slug;

            // Save to database
            if ($this->kostumModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kostum berhasil ditambahkan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan ke database',
                    'errors' => $this->kostumModel->errors()
                ]);
            }

        } catch (\Exception $e) {
            // Delete uploaded file if error
            if (isset($gambarName) && file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

/**
 * Update kostum (AJAX) - FIXED VERSION
 */
public function update($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request'
        ]);
    }

    $existing = $this->kostumModel->find($id);
    if (!$existing) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Kostum tidak ditemukan'
        ]);
    }

    try {
        // DEBUG: Lihat data yang diterima
        log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'FILES Data: ' . print_r($this->request->getFiles(), true));
        
        // Handle file upload
        $gambarName = $existing['gambar'];
        $gambarFile = $this->request->getFile('gambar');
        
        // Check if delete existing image
        $hapusGambar = $this->request->getPost('hapus_gambar');
        
        if ($hapusGambar && $gambarName) {
            if (file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
            }
            $gambarName = null;
        } elseif ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            // Validate image
            if ($gambarFile->getSize() > 2097152) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran gambar maksimal 2MB'
                ]);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!in_array($gambarFile->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP'
                ]);
            }
            
            $newGambarName = $gambarFile->getRandomName();
            $gambarFile->move(ROOTPATH . 'public/uploads/kostum', $newGambarName);
            
            // Delete old image if exists
            if ($gambarName && file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
            }
            
            $gambarName = $newGambarName;
        }

        // Format harga - pastikan berupa float
        $hargaSewa = $this->request->getPost('harga_sewa');
        if (is_string($hargaSewa)) {
            $hargaSewa = str_replace(['.', ','], '', $hargaSewa);
        }
        $hargaSewa = (float) $hargaSewa;

        // Format spesifikasi - pastikan JSON string atau null
        $spesifikasi = $this->request->getPost('spesifikasi');
        $spesifikasiJson = null;
        
        if (!empty($spesifikasi)) {
            // Jika sudah array, langsung encode
            if (is_array($spesifikasi)) {
                $spesifikasiJson = json_encode(array_filter($spesifikasi), JSON_UNESCAPED_UNICODE);
            } else {
                // Jika string, split by newline
                $lines = explode("\n", $spesifikasi);
                $cleanedLines = array_filter(array_map('trim', $lines));
                
                if (!empty($cleanedLines)) {
                    $spesifikasiJson = json_encode($cleanedLines, JSON_UNESCAPED_UNICODE);
                }
            }
        } else {
            // Keep existing spesifikasi if not changed
            $spesifikasiJson = $existing['spesifikasi'];
        }

        // DEBUG: Lihat data sebelum disimpan
        log_message('debug', 'Spesifikasi JSON: ' . $spesifikasiJson);
        
        // Prepare data - HANYA field yang diperlukan
        $data = [
            'kategori' => (string) $this->request->getPost('kategori'),
            'nama_kostum' => (string) $this->request->getPost('nama_kostum'),
            'deskripsi' => (string) ($this->request->getPost('deskripsi') ?? ''),
            'harga_sewa' => $hargaSewa,
            'durasi_sewa' => (string) ($this->request->getPost('durasi_sewa') ?? '3 hari'),
            'spesifikasi' => $spesifikasiJson, // JSON string atau null
            'gambar' => $gambarName, // string atau null
            'ukuran' => (string) ($this->request->getPost('ukuran') ?? ''),
            'warna' => (string) ($this->request->getPost('warna') ?? ''),
            'bahan' => (string) ($this->request->getPost('bahan') ?? ''),
            'kondisi' => (string) ($this->request->getPost('kondisi') ?? 'baik'),
            'stok' => (int) $this->request->getPost('stok'),
            'stok_tersedia' => (int) ($this->request->getPost('stok_tersedia') ?: $this->request->getPost('stok')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'urutan' => (int) ($this->request->getPost('urutan') ?: 0)
        ];

        // Handle custom duration
        $durasiSewa = $this->request->getPost('durasi_sewa');
        $durasiKustom = $this->request->getPost('durasi_kustom');
        
        if ($durasiSewa === 'Kustom' && $durasiKustom) {
            $data['durasi_sewa'] = (string) $durasiKustom;
        }

        // Update slug if nama_kostum changed
        if ($existing['nama_kostum'] != $data['nama_kostum']) {
            $slug = url_title($data['nama_kostum'], '-', true);
            $originalSlug = $slug;
            $counter = 1;
            
            while ($this->kostumModel->where('slug', $slug)->where('id !=', $id)->countAllResults() > 0) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $data['slug'] = $slug;
        }

        // DEBUG: Lihat data akhir
        log_message('debug', 'Data to update: ' . print_r($data, true));
        
        // Update database
        if ($this->kostumModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kostum berhasil diperbarui'
            ]);
        } else {
            $errors = $this->kostumModel->errors();
            log_message('error', 'Update errors: ' . print_r($errors, true));
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui database',
                'errors' => $errors
            ]);
        }

    } catch (\Exception $e) {
        log_message('error', 'Update exception: ' . $e->getMessage());
        log_message('error', 'Trace: ' . $e->getTraceAsString());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

    /**
     * Delete kostum (AJAX)
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $existing = $this->kostumModel->find($id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ]);
        }

        try {
            // Delete image if exists
            if (!empty($existing['gambar']) && file_exists(ROOTPATH . 'public/uploads/kostum/' . $existing['gambar'])) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $existing['gambar']);
            }
            
            // Delete from database
            if ($this->kostumModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kostum berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus dari database'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle status (AJAX)
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $existing = $this->kostumModel->find($id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ]);
        }

        try {
            $newStatus = $existing['is_active'] ? 0 : 1;
            
            if ($this->kostumModel->update($id, ['is_active' => $newStatus])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status berhasil diubah',
                    'new_status' => $newStatus
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah status'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle featured (AJAX)
     */
    public function toggleFeatured($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $existing = $this->kostumModel->find($id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ]);
        }

        try {
            $newFeatured = $existing['is_featured'] ? 0 : 1;
            
            if ($this->kostumModel->update($id, ['is_featured' => $newFeatured])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status featured berhasil diubah',
                    'new_featured' => $newFeatured
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah status featured'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk actions (AJAX)
     */
    public function bulkAction()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $ids = $this->request->getPost('ids');
        $action = $this->request->getPost('action');

        if (empty($ids) || empty($action)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih aksi dan kostum terlebih dahulu'
            ]);
        }

        try {
            $success = false;
            
            switch ($action) {
                case 'activate':
                    $success = $this->kostumModel->whereIn('id', $ids)->set(['is_active' => 1])->update();
                    break;
                case 'deactivate':
                    $success = $this->kostumModel->whereIn('id', $ids)->set(['is_active' => 0])->update();
                    break;
                case 'feature':
                    $success = $this->kostumModel->whereIn('id', $ids)->set(['is_featured' => 1])->update();
                    break;
                case 'unfeature':
                    $success = $this->kostumModel->whereIn('id', $ids)->set(['is_featured' => 0])->update();
                    break;
                case 'delete':
                    // Get images to delete
                    $kostumToDelete = $this->kostumModel->whereIn('id', $ids)->findAll();
                    foreach ($kostumToDelete as $kostum) {
                        if (!empty($kostum['gambar']) && file_exists(ROOTPATH . 'public/uploads/kostum/' . $kostum['gambar'])) {
                            unlink(ROOTPATH . 'public/uploads/kostum/' . $kostum['gambar']);
                        }
                    }
                    $success = $this->kostumModel->whereIn('id', $ids)->delete();
                    break;
            }

            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Aksi berhasil diterapkan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menerapkan aksi'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}