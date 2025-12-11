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
        'slug' => 'permit_empty|is_unique[kostum.slug,id,{id}]',
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
    protected $beforeInsert = ['generateSlug', 'setDefaultValues'];
    protected $beforeUpdate = ['generateSlug'];
    
    /**
     * Generate slug dari nama kostum
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['nama_kostum']) && !isset($data['data']['slug'])) {
            $slug = url_title($data['data']['nama_kostum'], '-', true);
            
            // Make slug unique
            $originalSlug = $slug;
            $counter = 1;
            
            // Check if we're updating
            if (isset($data['id'])) {
                $where = "slug = '{$slug}' AND id != {$data['id']}";
            } else {
                $where = "slug = '{$slug}'";
            }
            
            while ($this->where($where)->countAllResults() > 0) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $data['data']['slug'] = $slug;
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
        if (!isset($data['data']['stok_tersedia']) || empty($data['data']['stok_tersedia'])) {
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
        
        // Set is_featured default
        if (!isset($data['data']['is_featured'])) {
            $data['data']['is_featured'] = 0;
        }
        
        // Format spesifikasi dari array ke JSON
        if (isset($data['data']['spesifikasi']) && is_array($data['data']['spesifikasi'])) {
            $data['data']['spesifikasi'] = json_encode($data['data']['spesifikasi']);
        } elseif (isset($data['data']['spesifikasi']) && is_string($data['data']['spesifikasi'])) {
            // Jika spesifikasi adalah string, konversi ke array lalu ke JSON
            $lines = array_filter(array_map('trim', explode("\n", $data['data']['spesifikasi'])));
            $data['data']['spesifikasi'] = json_encode($lines);
        }
        
        // Format gambar tambahan dari array ke JSON
        if (isset($data['data']['gambar_tambahan']) && is_array($data['data']['gambar_tambahan'])) {
            $data['data']['gambar_tambahan'] = json_encode($data['data']['gambar_tambahan']);
        }
        
        return $data;
    }
    
    // =============================================================
    // METODE BARU UNTUK FIX CRUD ISSUES
    // =============================================================
    
    /**
     * Simpan kostum dengan data yang sudah diformat
     */
    public function saveKostum($data, $id = null)
    {
        try {
            // Format harga - hapus titik dan koma
            if (isset($data['harga_sewa'])) {
                $data['harga_sewa'] = (float) str_replace(['.', ','], '', $data['harga_sewa']);
            }
            
            // Pastikan stok tersedia tidak melebihi stok
            if (isset($data['stok']) && isset($data['stok_tersedia'])) {
                if ($data['stok_tersedia'] > $data['stok']) {
                    $data['stok_tersedia'] = $data['stok'];
                }
                if ($data['stok_tersedia'] < 0) {
                    $data['stok_tersedia'] = 0;
                }
            }
            
            // Convert boolean values
            $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $data['is_featured'] = isset($data['is_featured']) ? (int)$data['is_featured'] : 0;
            
            // Convert integer values
            $data['stok'] = isset($data['stok']) ? (int)$data['stok'] : 1;
            $data['stok_tersedia'] = isset($data['stok_tersedia']) ? (int)$data['stok_tersedia'] : ($data['stok'] ?? 1);
            $data['urutan'] = isset($data['urutan']) ? (int)$data['urutan'] : 0;
            
            // Handle spesifikasi
            if (isset($data['spesifikasi']) && is_string($data['spesifikasi'])) {
                $lines = array_filter(array_map('trim', explode("\n", $data['spesifikasi'])));
                $data['spesifikasi'] = !empty($lines) ? json_encode($lines) : null;
            }
            
            if ($id) {
                return $this->update($id, $data);
            } else {
                return $this->insert($data);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving kostum: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get kostum by ID dengan data yang sudah diformat
     */
    public function getKostumById($id)
    {
        $kostum = $this->find($id);
        
        if ($kostum) {
            // Decode JSON fields
            if (!empty($kostum['spesifikasi'])) {
                $spesifikasi = json_decode($kostum['spesifikasi'], true);
                $kostum['spesifikasi'] = is_array($spesifikasi) ? $spesifikasi : [];
                $kostum['spesifikasi_text'] = is_array($spesifikasi) ? implode("\n", $spesifikasi) : '';
            } else {
                $kostum['spesifikasi'] = [];
                $kostum['spesifikasi_text'] = '';
            }
            
            if (!empty($kostum['gambar_tambahan'])) {
                $gambarTambahan = json_decode($kostum['gambar_tambahan'], true);
                $kostum['gambar_tambahan'] = is_array($gambarTambahan) ? $gambarTambahan : [];
            } else {
                $kostum['gambar_tambahan'] = [];
            }
        }
        
        return $kostum;
    }
    
    // =============================================================
    // METODE EXISTING (DARI FILE ASLI) - TIDAK DIUBAH
    // =============================================================
    
    public function getFiltered($search = null, $kategori = null, $status = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        
        // Search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_kostum', $search)
                    ->orLike('deskripsi', $search)
                    ->orLike('ukuran', $search)
                    ->orLike('warna', $search)
                    ->orLike('bahan', $search)
                    ->groupEnd();
        }
        
        // Kategori filter
        if (!empty($kategori)) {
            $builder->where('kategori', $kategori);
        }
        
        // Status filter
        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('is_active', 0);
        }
        
        // Order by latest
        $builder->orderBy('created_at', 'DESC');
        
        // Get results
        $query = $builder->get($limit, $offset);
        
        return $query->getResultArray();
    }
    
    public function countFiltered($search = null, $kategori = null, $status = null)
    {
        $builder = $this->builder();
        
        // Search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_kostum', $search)
                    ->orLike('deskripsi', $search)
                    ->orLike('ukuran', $search)
                    ->orLike('warna', $search)
                    ->orLike('bahan', $search)
                    ->groupEnd();
        }
        
        // Kategori filter
        if (!empty($kategori)) {
            $builder->where('kategori', $kategori);
        }
        
        // Status filter
        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('is_active', 0);
        }
        
        return $builder->countAllResults();
    }
    
    public function getAllWithFilter($kategori = null, $status = 'active', $featured = null, $search = null, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
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
        $result = $query->getResultArray();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($result as &$item) {
            if (!empty($item['spesifikasi'])) {
                $item['spesifikasi'] = json_decode($item['spesifikasi'], true);
            } else {
                $item['spesifikasi'] = [];
            }
        }
        
        return $result;
    }
    
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
            if (!empty($item['spesifikasi'])) {
                $item['spesifikasi'] = json_decode($item['spesifikasi'], true);
            } else {
                $item['spesifikasi'] = [];
            }
        }
        
        return $kostum;
    }
    
    public function getFeatured($limit = 6)
    {
        $kostum = $this->where('is_featured', 1)
                       ->where('is_active', 1)
                       ->orderBy('urutan', 'ASC')
                       ->limit($limit)
                       ->findAll();
        
        // Decode spesifikasi untuk setiap kostum
        foreach ($kostum as &$item) {
            if (!empty($item['spesifikasi'])) {
                $item['spesifikasi'] = json_decode($item['spesifikasi'], true);
            } else {
                $item['spesifikasi'] = [];
            }
        }
        
        return $kostum;
    }
    
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
            if (!empty($item['spesifikasi'])) {
                $item['spesifikasi'] = json_decode($item['spesifikasi'], true);
            } else {
                $item['spesifikasi'] = [];
            }
        }
        
        return $kostum;
    }
    
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
        $builder = $this->builder();
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
    
    public function toggleStatus($id)
    {
        $kostum = $this->find($id);
        
        if ($kostum) {
            $newStatus = $kostum['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    public function toggleFeatured($id)
    {
        $kostum = $this->find($id);
        
        if ($kostum) {
            $newFeatured = $kostum['is_featured'] ? 0 : 1;
            return $this->update($id, ['is_featured' => $newFeatured]);
        }
        
        return false;
    }
    
    public function bulkActionSimple($ids, $action)
    {
        if (empty($ids)) {
            return false;
        }
        
        $data = [];
        
        switch ($action) {
            case 'activate':
                $data['is_active'] = 1;
                break;
            case 'deactivate':
                $data['is_active'] = 0;
                break;
            case 'feature':
                $data['is_featured'] = 1;
                break;
            case 'unfeature':
                $data['is_featured'] = 0;
                break;
            case 'delete':
                // Delete multiple records
                return $this->whereIn('id', $ids)->delete();
        }
        
        if (!empty($data)) {
            return $this->whereIn('id', $ids)->set($data)->update();
        }
        
        return false;
    }
    
    public function importCSVSimple($csvData)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($csvData as $index => $row) {
            try {
                // Validasi data minimal
                if (empty($row['nama_kostum']) || empty($row['harga_sewa'])) {
                    $errorCount++;
                    $errors[] = "Baris {$index}: Nama kostum dan harga harus diisi";
                    continue;
                }
                
                $data = [
                    'kategori' => $row['kategori'] ?? 'lainnya',
                    'nama_kostum' => $row['nama_kostum'],
                    'deskripsi' => $row['deskripsi'] ?? '',
                    'harga_sewa' => (float) str_replace(['.', ','], '', $row['harga_sewa']),
                    'durasi_sewa' => $row['durasi_sewa'] ?? '3 hari',
                    'ukuran' => $row['ukuran'] ?? '',
                    'warna' => $row['warna'] ?? '',
                    'bahan' => $row['bahan'] ?? '',
                    'kondisi' => $row['kondisi'] ?? 'baik',
                    'stok' => (int) ($row['stok'] ?? 1),
                    'stok_tersedia' => (int) ($row['stok_tersedia'] ?? $row['stok'] ?? 1),
                    'is_active' => isset($row['is_active']) && in_array(strtolower($row['is_active']), ['1', 'true', 'yes', 'aktif']) ? 1 : 0,
                    'is_featured' => isset($row['is_featured']) && in_array(strtolower($row['is_featured']), ['1', 'true', 'yes']) ? 1 : 0,
                    'meta_keywords' => $row['meta_keywords'] ?? '',
                    'meta_description' => $row['meta_description'] ?? ''
                ];
                
                // Handle spesifikasi
                if (!empty($row['spesifikasi'])) {
                    $spesifikasiArray = is_array($row['spesifikasi']) ? $row['spesifikasi'] : explode('|', $row['spesifikasi']);
                    $data['spesifikasi'] = json_encode(array_filter(array_map('trim', $spesifikasiArray)));
                }
                
                if ($this->save($data)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Baris {$index}: " . implode(', ', $this->errors());
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris {$index}: " . $e->getMessage();
            }
        }
        
        return [
            'success' => $successCount,
            'error' => $errorCount,
            'errors' => $errors
        ];
    }
    
    public function getKategoriOptions()
    {
        return [
            'pengantin_wanita' => 'Pengantin Wanita',
            'pengantin_pria' => 'Pengantin Pria',
            'keluarga' => 'Keluarga',
            'lainnya' => 'Lainnya'
        ];
    }
    
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