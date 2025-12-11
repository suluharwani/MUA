<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraModel extends Model
{
    protected $table = 'mitra';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nama_mitra',
        'slug',
        'kategori',
        'deskripsi',
        'alamat',
        'whatsapp',
        'telepon',
        'email',
        'website',
        'instagram',
        'facebook',
        'tiktok',
        'gambar',
        'gambar_tambahan',
        'layanan',
        'harga_mulai',
        'pengalaman',
        'rating',
        'rekomendasi',
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
        'nama_mitra' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|is_unique[mitra.slug,id,{id}]',
        'kategori' => 'required',
        'whatsapp' => 'required|min_length[10]|max_length[20]'
    ];
    
    protected $validationMessages = [
        'nama_mitra' => [
            'required' => 'Nama mitra wajib diisi',
            'min_length' => 'Nama mitra minimal 3 karakter',
            'max_length' => 'Nama mitra maksimal 100 karakter'
        ],
        'slug' => [
            'required' => 'Slug wajib diisi',
            'is_unique' => 'Slug sudah digunakan'
        ],
        'whatsapp' => [
            'required' => 'Nomor WhatsApp wajib diisi',
            'min_length' => 'Nomor WhatsApp minimal 10 digit',
            'max_length' => 'Nomor WhatsApp maksimal 20 digit'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateSlug', 'setDefaultValues'];
    protected $beforeUpdate = ['generateSlug'];
    
    /**
     * Generate slug dari nama mitra
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['nama_mitra']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['nama_mitra'], '-', true);
        }
        return $data;
    }
    
    /**
     * Set nilai default
     */
    protected function setDefaultValues(array $data)
    {
        // Set rating default
        if (!isset($data['data']['rating']) || empty($data['data']['rating'])) {
            $data['data']['rating'] = 5.0;
        }
        
        // Set pengalaman default
        if (!isset($data['data']['pengalaman']) || empty($data['data']['pengalaman'])) {
            $data['data']['pengalaman'] = '1 tahun';
        }
        
        // Set is_active default
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }
        
        // Format layanan dari array ke JSON
        if (isset($data['data']['layanan']) && is_array($data['data']['layanan'])) {
            $data['data']['layanan'] = json_encode($data['data']['layanan']);
        }
        
        // Format gambar tambahan dari array ke JSON
        if (isset($data['data']['gambar_tambahan']) && is_array($data['data']['gambar_tambahan'])) {
            $data['data']['gambar_tambahan'] = json_encode($data['data']['gambar_tambahan']);
        }
        
        return $data;
    }
    
    /**
     * Get all mitra dengan filter
     */
    public function getAllWithFilter($kategori = null, $status = 'active', $search = null, $featured = null, $limit = null, $offset = 0)
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
            $builder->like('nama_mitra', $search);
            $builder->orLike('deskripsi', $search);
            $builder->orLike('alamat', $search);
            $builder->orLike('layanan', $search);
            $builder->groupEnd();
        }
        
        $builder->orderBy('is_featured', 'DESC');
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('nama_mitra', 'ASC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        $query = $builder->get();
        $mitra = $query->getResultArray();
        
        // Decode layanan untuk setiap mitra
        foreach ($mitra as &$item) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        
        return $mitra;
    }
    
    /**
     * Get mitra by slug
     */
    public function getBySlug($slug)
    {
        $mitra = $this->where('slug', $slug)
                      ->where('is_active', 1)
                      ->first();
        
        if ($mitra) {
            // Decode layanan dan gambar tambahan
            $mitra['layanan'] = json_decode($mitra['layanan'] ?? '[]', true);
            $mitra['gambar_tambahan'] = json_decode($mitra['gambar_tambahan'] ?? '[]', true);
        }
        
        return $mitra;
    }
    
    /**
     * Get mitra by kategori
     */
    public function getByKategori($kategori, $limit = null, $featuredOnly = false)
    {
        $builder = $this->where('kategori', $kategori)
                        ->where('is_active', 1);
        
        if ($featuredOnly) {
            $builder->where('is_featured', 1);
        }
        
        $builder->orderBy('is_featured', 'DESC');
        $builder->orderBy('urutan', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $mitra = $builder->findAll();
        
        // Decode layanan untuk setiap mitra
        foreach ($mitra as &$item) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        
        return $mitra;
    }
    
    /**
     * Get featured mitra
     */
    public function getFeatured($limit = 6)
    {
        $mitra = $this->where('is_featured', 1)
                      ->where('is_active', 1)
                      ->orderBy('urutan', 'ASC')
                      ->limit($limit)
                      ->findAll();
        
        // Decode layanan untuk setiap mitra
        foreach ($mitra as &$item) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        
        return $mitra;
    }
    
    /**
     * Get kategori options untuk dropdown
     */
    public function getKategoriOptions()
    {
        return [
            'fotografer' => 'Fotografer & Videografer',
            'wo' => 'Wedding Organizer',
            'catering' => 'Catering & Prasmanan',
            'undangan' => 'Percetakan Undangan',
            'dekorasi' => 'Dekorasi & Pelaminan',
            'gedung' => 'Gedung & Venue',
            'busana' => 'Busana Pengantin',
            'rias' => 'Makeup Artist',
            'music' => 'Live Music & DJ',
            'kue' => 'Kue & Souvenir',
            'transport' => 'Transportasi',
            'lainnya' => 'Lainnya'
        ];
    }
    
    /**
     * Get related mitra
     */
    public function getRelated($mitraId, $limit = 4)
    {
        $currentMitra = $this->find($mitraId);
        
        if (!$currentMitra) {
            return [];
        }
        
        $related = $this->where('kategori', $currentMitra['kategori'])
                        ->where('id !=', $mitraId)
                        ->where('is_active', 1)
                        ->orderBy('RAND()')
                        ->limit($limit)
                        ->findAll();
        
        // Decode layanan untuk setiap mitra
        foreach ($related as &$item) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        
        return $related;
    }
    
    /**
     * Get statistics
     */
    public function getStatistics()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'featured' => $this->where('is_featured', 1)->countAllResults(),
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
     * Search mitra with pagination
     */
    public function searchWithPagination($keyword, $kategori = null, $perPage = 12, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where('is_active', 1);
        
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        
        if ($keyword) {
            $builder->groupStart();
            $builder->like('nama_mitra', $keyword);
            $builder->orLike('deskripsi', $keyword);
            $builder->orLike('alamat', $keyword);
            $builder->orLike('layanan', $keyword);
            $builder->groupEnd();
        }
        
        // Hitung total hasil
        $total = $builder->countAllResults(false);
        
        // Get data dengan pagination
        $builder->limit($perPage, $offset);
        $builder->orderBy('is_featured', 'DESC');
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('nama_mitra', 'ASC');
        $query = $builder->get();
        $mitra = $query->getResultArray();
        
        // Decode layanan untuk setiap mitra
        foreach ($mitra as &$item) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        
        return [
            'data' => $mitra,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Update rating mitra
     */
    public function updateRating($id, $newRating)
    {
        $mitra = $this->find($id);
        
        if ($mitra) {
            // Simple average calculation
            $currentRating = $mitra['rating'] ?? 5.0;
            $newAverage = ($currentRating + $newRating) / 2;
            
            return $this->update($id, [
                'rating' => round($newAverage, 1),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false;
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $mitra = $this->find($id);
        
        if ($mitra) {
            $newStatus = $mitra['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $mitra = $this->find($id);
        
        if ($mitra) {
            $newFeatured = $mitra['is_featured'] ? 0 : 1;
            return $this->update($id, ['is_featured' => $newFeatured]);
        }
        
        return false;
    }
}