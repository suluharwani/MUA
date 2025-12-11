<?php

namespace App\Models;

use CodeIgniter\Model;

class KostumModel extends Model
{
    protected $table = 'kostum';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kategori',
        'nama_kostum',
        'slug',
        'deskripsi',
        'harga_sewa',
        'durasi_sewa',
        'spesifikasi',
        'gambar',
        'gambar_tambahan',
        'ukuran',
        'warna',
        'bahan',
        'kondisi',
        'stok',
        'stok_tersedia',
        'is_active',
        'is_featured',
        'urutan',
        'meta_keywords',
        'meta_description',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'nama_kostum' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|is_unique[kostum.slug,id,{id}]',
        'kategori' => 'required|in_list[pengantin_wanita,pengantin_pria,keluarga,lainnya]',
        'harga_sewa' => 'required|numeric',
        'stok' => 'required|integer',
        'stok_tersedia' => 'integer'
    ];
    
    protected $validationMessages = [
        'nama_kostum' => [
            'required' => 'Nama kostum wajib diisi',
            'min_length' => 'Nama kostum minimal 3 karakter',
            'max_length' => 'Nama kostum maksimal 100 karakter'
        ],
        'slug' => [
            'required' => 'Slug wajib diisi',
            'is_unique' => 'Slug sudah digunakan'
        ],
        'kategori' => [
            'required' => 'Kategori wajib dipilih',
            'in_list' => 'Kategori tidak valid'
        ],
        'harga_sewa' => [
            'required' => 'Harga sewa wajib diisi',
            'numeric' => 'Harga harus berupa angka'
        ],
        'stok' => [
            'required' => 'Stok wajib diisi',
            'integer' => 'Stok harus berupa bilangan bulat'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateSlug', 'setDefaultValues', 'handleUpload'];
    protected $beforeUpdate = ['generateSlug', 'handleUpload'];
    protected $afterInsert = ['updateStokTersedia'];
    protected $afterUpdate = ['updateStokTersedia'];
    
    /**
     * Generate slug dari nama kostum
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['nama_kostum']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['nama_kostum'], '-', true);
        }
        return $data;
    }
    
    /**
     * Set nilai default
     */
    protected function setDefaultValues(array $data)
    {
        // Set durasi sewa default jika kosong
        if (!isset($data['data']['durasi_sewa']) || empty($data['data']['durasi_sewa'])) {
            $data['data']['durasi_sewa'] = '3 hari';
        }
        
        // Set stok tersedia sama dengan stok untuk insert baru
        if (!isset($data['data']['stok_tersedia'])) {
            $data['data']['stok_tersedia'] = $data['data']['stok'] ?? 1;
        }
        
        // Set kondisi default jika kosong
        if (!isset($data['data']['kondisi']) || empty($data['data']['kondisi'])) {
            $data['data']['kondisi'] = 'baik';
        }
        
        // Set is_active default
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }
        
        // Format spesifikasi dari array ke JSON
        if (isset($data['data']['spesifikasi']) && is_array($data['data']['spesifikasi'])) {
            $data['data']['spesifikasi'] = json_encode($data['data']['spesifikasi']);
        }
        
        // Format gambar tambahan dari array ke JSON
        if (isset($data['data']['gambar_tambahan']) && is_array($data['data']['gambar_tambahan'])) {
            $data['data']['gambar_tambahan'] = json_encode($data['data']['gambar_tambahan']);
        }
        
        return $data;
    }
    
    /**
     * Handle upload gambar
     */
    protected function handleUpload(array $data)
    {
        // Handle upload gambar utama
        $gambarFile = service('request')->getFile('gambar');
        
        if ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            $newName = $gambarFile->getRandomName();
            $gambarFile->move(WRITEPATH . 'uploads/kostum', $newName);
            $data['data']['gambar'] = $newName;
            
            // Hapus gambar lama jika ada
            if (isset($data['id'][0])) {
                $oldData = $this->find($data['id'][0]);
                if ($oldData && !empty($oldData['gambar'])) {
                    $oldPath = WRITEPATH . 'uploads/kostum/' . $oldData['gambar'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        }
        
        // Handle upload gambar tambahan
        $gambarTambahanFiles = service('request')->getFiles();
        $uploadedImages = [];
        
        if (isset($gambarTambahanFiles['gambar_tambahan'])) {
            foreach ($gambarTambahanFiles['gambar_tambahan'] as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/kostum/tambahan', $newName);
                    $uploadedImages[] = $newName;
                }
            }
            
            if (!empty($uploadedImages)) {
                // Gabungkan dengan gambar tambahan yang sudah ada
                $existingImages = [];
                if (isset($data['id'][0])) {
                    $oldData = $this->find($data['id'][0]);
                    if ($oldData && !empty($oldData['gambar_tambahan'])) {
                        $existingImages = json_decode($oldData['gambar_tambahan'], true) ?? [];
                    }
                }
                
                $allImages = array_merge($existingImages, $uploadedImages);
                $data['data']['gambar_tambahan'] = json_encode($allImages);
            }
        }
        
        return $data;
    }
    
    /**
     * Update stok tersedia setelah insert/update
     */
    protected function updateStokTersedia(array $data)
    {
        if (isset($data['id'])) {
            $kostumId = is_array($data['id']) ? $data['id'][0] : $data['id'];
            
            // Hitung jumlah kostum yang sedang disewa
            $pesananModel = new PesananModel();
            $builder = $pesananModel->db->table('pesanan');
            $builder->select('COUNT(*) as total_disewa');
            $builder->where('kostum_id', $kostumId);
            $builder->whereIn('status', ['dikonfirmasi', 'diproses']);
            $builder->where('DATE(tanggal_acara) >=', date('Y-m-d'));
            $result = $builder->get()->getRow();
            
            $totalDisewa = $result->total_disewa ?? 0;
            
            // Update stok tersedia
            $kostum = $this->find($kostumId);
            $stokTersedia = $kostum['stok'] - $totalDisewa;
            
            if ($stokTersedia < 0) $stokTersedia = 0;
            
            $this->update($kostumId, ['stok_tersedia' => $stokTersedia]);
        }
        
        return $data;
    }
    
    /**
     * Get all kostum dengan filter
     */
    public function getAllWithFilter($kategori = null, $status = 'active', $featured = null, $search = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        
        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('is_active', 0);
        }
        
        if ($featured === 'yes') {
            $builder->where('is_featured', 1);
        } elseif ($featured === 'no') {
            $builder->where('is_featured', 0);
        }
        
        if ($search) {
            $builder->groupStart();
            $builder->like('nama_kostum', $search);
            $builder->orLike('deskripsi', $search);
            $builder->orLike('ukuran', $search);
            $builder->orLike('warna', $search);
            $builder->orLike('bahan', $search);
            $builder->groupEnd();
        }
        
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('nama_kostum', 'ASC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        $query = $builder->get();
        return $query->getResultArray();
    }
    
    /**
     * Get kostum by slug
     */
    public function getBySlug($slug)
    {
        $kostum = $this->where('slug', $slug)
                       ->where('is_active', 1)
                       ->first();
        
        if ($kostum) {
            // Decode spesifikasi dan gambar tambahan
            $kostum['spesifikasi'] = json_decode($kostum['spesifikasi'] ?? '[]', true);
            $kostum['gambar_tambahan'] = json_decode($kostum['gambar_tambahan'] ?? '[]', true);
        }
        
        return $kostum;
    }
    
    /**
     * Get kostum by kategori
     */
    public function getByKategori($kategori, $limit = null, $featuredOnly = false)
    {
        $builder = $this->where('kategori', $kategori)
                        ->where('is_active', 1);
        
        if ($featuredOnly) {
            $builder->where('is_featured', 1);
        }
        
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('nama_kostum', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $kostum = $builder->findAll();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($kostum as &$item) {
            $item['spesifikasi'] = json_decode($item['spesifikasi'] ?? '[]', true);
        }
        
        return $kostum;
    }
    
    /**
     * Get kostum featured
     */
    public function getFeatured($limit = 6)
    {
        $kostum = $this->where('is_featured', 1)
                       ->where('is_active', 1)
                       ->orderBy('urutan', 'ASC')
                       ->limit($limit)
                       ->findAll();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($kostum as &$item) {
            $item['spesifikasi'] = json_decode($item['spesifikasi'] ?? '[]', true);
        }
        
        return $kostum;
    }
    
    /**
     * Get kostum dengan stok tersedia
     */
    public function getAvailable($kategori = null, $limit = null)
    {
        $builder = $this->where('stok_tersedia >', 0)
                        ->where('is_active', 1);
        
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        
        $builder->orderBy('urutan', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $kostum = $builder->findAll();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($kostum as &$item) {
            $item['spesifikasi'] = json_decode($item['spesifikasi'] ?? '[]', true);
        }
        
        return $kostum;
    }
    
    /**
     * Check ketersediaan kostum pada tanggal tertentu
     */
    public function checkAvailability($kostumId, $tanggalMulai, $tanggalSelesai)
    {
        // Konversi durasi sewa ke hari
        $kostum = $this->find($kostumId);
        if (!$kostum) {
            return false;
        }
        
        // Parse durasi sewa (misal: "3 hari")
        $durasiSewa = 3; // default
        if (preg_match('/(\d+)/', $kostum['durasi_sewa'], $matches)) {
            $durasiSewa = (int)$matches[1];
        }
        
        // Hitung tanggal pengembalian
        $tanggalPengembalian = date('Y-m-d', strtotime($tanggalMulai . " + $durasiSewa days"));
        
        // Cek apakah ada pesanan yang bentrok
        $pesananModel = new PesananModel();
        $builder = $pesananModel->db->table('pesanan');
        $builder->select('COUNT(*) as total');
        $builder->where('kostum_id', $kostumId);
        $builder->whereIn('status', ['dikonfirmasi', 'diproses']);
        $builder->groupStart();
        // Cek bentrok tanggal
        $builder->where("DATE(tanggal_acara) BETWEEN '$tanggalMulai' AND '$tanggalPengembalian'");
        $builder->orWhere("DATE(DATE_ADD(tanggal_acara, INTERVAL $durasiSewa DAY)) BETWEEN '$tanggalMulai' AND '$tanggalPengembalian'");
        $builder->groupEnd();
        
        $result = $builder->get()->getRow();
        $totalBentrok = $result->total ?? 0;
        
        // Cek stok tersedia
        $stokTersedia = $kostum['stok_tersedia'] ?? 0;
        
        return $totalBentrok < $stokTersedia;
    }
    
    /**
     * Get statistik kostum
     */
    public function getStatistics()
    {
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'inactive' => $this->where('is_active', 0)->countAllResults(),
            'featured' => $this->where('is_featured', 1)->countAllResults(),
            'out_of_stock' => $this->where('stok_tersedia', 0)->where('is_active', 1)->countAllResults(),
            'low_stock' => $this->where('stok_tersedia >', 0)
                               ->where('stok_tersedia <=', 2)
                               ->where('is_active', 1)
                               ->countAllResults(),
            'by_kategori' => []
        ];
        
        // Hitung per kategori
        $builder = $this->db->table($this->table);
        $builder->select('kategori, COUNT(*) as total');
        $builder->where('is_active', 1);
        $builder->groupBy('kategori');
        $builder->orderBy('total', 'DESC');
        $result = $builder->get()->getResultArray();
        
        foreach ($result as $row) {
            $stats['by_kategori'][$row['kategori']] = $row['total'];
        }
        
        return $stats;
    }
    
    /**
     * Update stok
     */
    public function updateStok($id, $newStok)
    {
        $data = [
            'stok' => $newStok,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->update($id, $data)) {
            // Trigger update stok tersedia
            $this->updateStokTersedia(['id' => $id]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Update status aktif/nonaktif
     */
    public function toggleStatus($id)
    {
        $kostum = $this->find($id);
        
        if ($kostum) {
            $newStatus = $kostum['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    /**
     * Update featured status
     */
    public function toggleFeatured($id)
    {
        $kostum = $this->find($id);
        
        if ($kostum) {
            $newFeatured = $kostum['is_featured'] ? 0 : 1;
            return $this->update($id, ['is_featured' => $newFeatured]);
        }
        
        return false;
    }
    
    /**
     * Hapus gambar tambahan
     */
    public function deleteAdditionalImage($kostumId, $imageName)
    {
        $kostum = $this->find($kostumId);
        
        if ($kostum && !empty($kostum['gambar_tambahan'])) {
            $images = json_decode($kostum['gambar_tambahan'], true) ?? [];
            
            // Cari dan hapus gambar dari array
            $key = array_search($imageName, $images);
            if ($key !== false) {
                unset($images[$key]);
                
                // Hapus file fisik
                $imagePath = WRITEPATH . 'uploads/kostum/tambahan/' . $imageName;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                // Update database
                return $this->update($kostumId, [
                    'gambar_tambahan' => json_encode(array_values($images))
                ]);
            }
        }
        
        return false;
    }
    
    /**
     * Get related kostum
     */
    public function getRelated($kostumId, $limit = 4)
    {
        $currentKostum = $this->find($kostumId);
        
        if (!$currentKostum) {
            return [];
        }
        
        $related = $this->where('kategori', $currentKostum['kategori'])
                        ->where('id !=', $kostumId)
                        ->where('is_active', 1)
                        ->orderBy('RAND()')
                        ->limit($limit)
                        ->findAll();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($related as &$item) {
            $item['spesifikasi'] = json_decode($item['spesifikasi'] ?? '[]', true);
        }
        
        return $related;
    }
    
    /**
     * Get kostum untuk sitemap
     */
    public function getForSitemap()
    {
        return $this->select('slug, updated_at')
                    ->where('is_active', 1)
                    ->orderBy('updated_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Search kostum dengan pagination
     */
    public function searchWithPagination($keyword, $perPage = 12, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where('is_active', 1);
        
        if ($keyword) {
            $builder->groupStart();
            $builder->like('nama_kostum', $keyword);
            $builder->orLike('deskripsi', $keyword);
            $builder->orLike('ukuran', $keyword);
            $builder->orLike('warna', $keyword);
            $builder->orLike('bahan', $keyword);
            $builder->orLike('kategori', $keyword);
            $builder->groupEnd();
        }
        
        // Hitung total hasil
        $total = $builder->countAllResults(false);
        
        // Get data dengan pagination
        $builder->limit($perPage, $offset);
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('nama_kostum', 'ASC');
        $query = $builder->get();
        $kostum = $query->getResultArray();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($kostum as &$item) {
            $item['spesifikasi'] = json_decode($item['spesifikasi'] ?? '[]', true);
        }
        
        return [
            'data' => $kostum,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Import kostum dari CSV
     */
    public function importFromCSV($filePath)
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }
        
        $csvData = array_map('str_getcsv', file($filePath));
        $headers = array_shift($csvData);
        
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($csvData as $index => $row) {
            $data = array_combine($headers, $row);
            
            try {
                // Format data
                $kostumData = [
                    'nama_kostum' => $data['nama_kostum'] ?? '',
                    'kategori' => $data['kategori'] ?? 'lainnya',
                    'deskripsi' => $data['deskripsi'] ?? '',
                    'harga_sewa' => (float)($data['harga_sewa'] ?? 0),
                    'durasi_sewa' => $data['durasi_sewa'] ?? '3 hari',
                    'stok' => (int)($data['stok'] ?? 1),
                    'ukuran' => $data['ukuran'] ?? '',
                    'warna' => $data['warna'] ?? '',
                    'bahan' => $data['bahan'] ?? '',
                    'kondisi' => $data['kondisi'] ?? 'baik',
                    'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
                    'is_featured' => isset($data['is_featured']) ? (bool)$data['is_featured'] : false
                ];
                
                // Handle spesifikasi
                if (!empty($data['spesifikasi'])) {
                    $spesifikasiArray = explode('|', $data['spesifikasi']);
                    $kostumData['spesifikasi'] = json_encode($spesifikasiArray);
                }
                
                if ($this->save($kostumData)) {
                    $imported++;
                } else {
                    $failed++;
                    $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->errors());
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        // Hapus file CSV setelah import
        unlink($filePath);
        
        return [
            'success' => true,
            'imported' => $imported,
            'failed' => $failed,
            'total' => count($csvData),
            'errors' => $errors
        ];
    }
    
    /**
     * Export kostum ke CSV
     */
    public function exportToCSV($filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        
        // Apply filters
        if (!empty($filters['kategori'])) {
            $builder->where('kategori', $filters['kategori']);
        }
        
        if (isset($filters['is_active'])) {
            $builder->where('is_active', $filters['is_active']);
        }
        
        $builder->orderBy('kategori', 'ASC');
        $builder->orderBy('nama_kostum', 'ASC');
        
        $kostum = $builder->get()->getResultArray();
        
        // Buat file CSV
        $filename = 'kostum_export_' . date('Ymd_His') . '.csv';
        $filePath = WRITEPATH . 'exports/' . $filename;
        
        if (!is_dir(WRITEPATH . 'exports')) {
            mkdir(WRITEPATH . 'exports', 0755, true);
        }
        
        $fp = fopen($filePath, 'w');
        
        // Header
        $headers = [
            'id', 'nama_kostum', 'slug', 'kategori', 'deskripsi', 
            'harga_sewa', 'durasi_sewa', 'spesifikasi', 'ukuran', 
            'warna', 'bahan', 'kondisi', 'stok', 'stok_tersedia',
            'is_active', 'is_featured', 'urutan', 'created_at', 'updated_at'
        ];
        fputcsv($fp, $headers);
        
        // Data
        foreach ($kostum as $row) {
            // Decode spesifikasi
            $spesifikasi = json_decode($row['spesifikasi'] ?? '[]', true);
            $row['spesifikasi'] = is_array($spesifikasi) ? implode('|', $spesifikasi) : '';
            
            fputcsv($fp, $row);
        }
        
        fclose($fp);
        
        return $filePath;
    }
    
    /**
     * Get kategori options untuk dropdown
     */
    public function getKategoriOptions()
    {
        return [
            'pengantin_wanita' => 'Pengantin Wanita',
            'pengantin_pria' => 'Pengantin Pria',
            'keluarga' => 'Keluarga',
            'lainnya' => 'Lainnya'
        ];
    }
    
    /**
     * Get ukuran options untuk dropdown
     */
    public function getUkuranOptions()
    {
        return [
            'XS' => 'XS (Extra Small)',
            'S' => 'S (Small)',
            'M' => 'M (Medium)',
            'L' => 'L (Large)',
            'XL' => 'XL (Extra Large)',
            'XXL' => 'XXL (Double Extra Large)',
            'custom' => 'Custom Size'
        ];
    }
    
    /**
     * Get kondisi options untuk dropdown
     */
    public function getKondisiOptions()
    {
        return [
            'baru' => 'Baru',
            'baik' => 'Baik',
            'cukup' => 'Cukup',
            'perlu_perawatan' => 'Perlu Perawatan'
        ];
    }
}