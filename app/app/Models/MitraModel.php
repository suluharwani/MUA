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
        'spesialisasi',
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
        'foto',
        'layanan',
        'harga_mulai',
        'tarif',
        'pengalaman',
        'portofolio',
        'keahlian',
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
        'id' => 'permit_empty|integer',
        'nama_mitra' => 'required|min_length[3]|max_length[200]',
        'slug' => 'required|is_unique[mitra.slug,id,{id}]',
        'kategori' => 'required',
        'whatsapp' => 'required|min_length[10]|max_length[20]',
        'email' => 'valid_email',
        'telepon' => 'max_length[20]'
    ];
    
    protected $validationMessages = [
        'nama_mitra' => [
            'required' => 'Nama mitra wajib diisi',
            'min_length' => 'Nama mitra minimal 3 karakter',
            'max_length' => 'Nama mitra maksimal 200 karakter'
        ],
        'slug' => [
            'required' => 'Slug wajib diisi',
            'is_unique' => 'Slug sudah digunakan'
        ],
        'whatsapp' => [
            'required' => 'Nomor WhatsApp wajib diisi',
            'min_length' => 'Nomor WhatsApp minimal 10 digit',
            'max_length' => 'Nomor WhatsApp maksimal 20 digit'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid'
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
        if (isset($data['data']['nama_mitra'])) {
            if (!isset($data['data']['slug']) || empty($data['data']['slug'])) {
                $slug = url_title($data['data']['nama_mitra'], '-', true);
                
                // Check if slug exists
                $builder = $this->db->table($this->table);
                $builder->where('slug', $slug);
                if (isset($data['id'])) {
                    $builder->where('id !=', $data['id']);
                }
                $result = $builder->get()->getRowArray();
                
                if ($result) {
                    $slug .= '-' . time();
                }
                
                $data['data']['slug'] = $slug;
            }
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
        
        // Format spesialisasi dari array ke string
        if (isset($data['data']['spesialisasi']) && is_array($data['data']['spesialisasi'])) {
            $data['data']['spesialisasi'] = implode(', ', $data['data']['spesialisasi']);
        }
        
        // Format keahlian dari array ke string
        if (isset($data['data']['keahlian']) && is_array($data['data']['keahlian'])) {
            $data['data']['keahlian'] = implode(', ', $data['data']['keahlian']);
        }
        
        // Set gambar default jika tidak ada foto
        if (!isset($data['data']['gambar']) && isset($data['data']['foto'])) {
            $data['data']['gambar'] = $data['data']['foto'];
        }
        
        // Set tarif default jika tidak ada harga_mulai
        if (!isset($data['data']['harga_mulai']) && isset($data['data']['tarif'])) {
            $data['data']['harga_mulai'] = $data['data']['tarif'];
        }
        
        return $data;
    }
    
    /**
     * Get all mitra dengan filter
     */
    /**
 * Get all mitra dengan filter
 */
public function getAllWithFilter($params = [])
{
    // Set default values
    $defaultParams = [
        'status' => 'active',
        'search' => null,
        'spesialisasi' => null,
        'kategori' => null,
        'featured' => null,
        'limit' => null,
        'offset' => 0
    ];
    
    // Merge with provided params
    $params = array_merge($defaultParams, $params);
    
    $builder = $this->db->table($this->table);
    $builder->select('*');
    
    // Filter kategori
    if (!empty($params['kategori'])) {
        $builder->where('kategori', $params['kategori']);
    }
    
    // Filter status
    if ($params['status'] === 'active') {
        $builder->where('is_active', 1);
    } elseif ($params['status'] === 'inactive') {
        $builder->where('is_active', 0);
    } elseif ($params['status'] === 'all') {
        // Show all, no filter
    }
    
    // Filter featured
    if ($params['featured'] === 'yes') {
        $builder->where('is_featured', 1);
    } elseif ($params['featured'] === 'no') {
        $builder->where('is_featured', 0);
    }
    
    // Filter spesialisasi
    if (!empty($params['spesialisasi'])) {
        $builder->like('spesialisasi', $params['spesialisasi']);
    }
    
    // Filter search
    if (!empty($params['search'])) {
        $builder->groupStart();
        $builder->like('nama_mitra', $params['search']);
        $builder->orLike('deskripsi', $params['search']);
        $builder->orLike('alamat', $params['search']);
        $builder->orLike('layanan', $params['search']);
        $builder->orLike('spesialisasi', $params['search']);
        $builder->orLike('keahlian', $params['search']);
        $builder->groupEnd();
    }
    
    // Ordering
    $builder->orderBy('is_featured', 'DESC');
    $builder->orderBy('urutan', 'ASC');
    $builder->orderBy('nama_mitra', 'ASC');
    
    // Limit and offset
    if (!empty($params['limit'])) {
        $builder->limit($params['limit'], $params['offset']);
    }
    
    $query = $builder->get();
    $mitra = $query->getResultArray();
    
    // Decode JSON fields untuk setiap mitra
    foreach ($mitra as &$item) {
        if (isset($item['layanan'])) {
            $item['layanan'] = json_decode($item['layanan'] ?? '[]', true);
        }
        if (isset($item['gambar_tambahan'])) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
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
     * Get mitra by spesialisasi
     */
    public function getBySpesialisasi($spesialisasi, $limit = null)
    {
        $builder = $this->like('spesialisasi', $spesialisasi)
                        ->where('is_active', 1)
                        ->orderBy('urutan', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
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
     * Get spesialisasi options
     */
    public function getSpesialisasiOptions()
    {
        $builder = $this->db->table($this->table);
        $builder->select('spesialisasi');
        $builder->distinct();
        $builder->where('spesialisasi !=', '');
        $builder->where('is_active', 1);
        $builder->orderBy('spesialisasi', 'ASC');
        $query = $builder->get();
        $result = $query->getResultArray();
        
        $options = [];
        foreach ($result as $row) {
            $specs = explode(',', $row['spesialisasi']);
            foreach ($specs as $spec) {
                $spec = trim($spec);
                if ($spec && !in_array($spec, $options)) {
                    $options[] = $spec;
                }
            }
        }
        
        sort($options);
        return $options;
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
        $stats = [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'featured' => $this->where('is_featured', 1)->countAllResults(),
            'by_kategori' => [],
            'by_spesialisasi' => []
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
        
        // Hitung per spesialisasi
        $builder = $this->db->table($this->table);
        $builder->select('spesialisasi, COUNT(*) as total');
        $builder->where('is_active', 1);
        $builder->groupBy('spesialisasi');
        $builder->orderBy('total', 'DESC');
        $result = $builder->get()->getResultArray();
        
        foreach ($result as $row) {
            $stats['by_spesialisasi'][$row['spesialisasi']] = $row['total'];
        }
        
        return $stats;
    }
    
    /**
     * Search mitra with pagination
     */
    public function searchWithPagination($keyword, $kategori = null, $spesialisasi = null, $perPage = 12, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where('is_active', 1);
        
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        
        if ($spesialisasi) {
            $builder->like('spesialisasi', $spesialisasi);
        }
        
        if ($keyword) {
            $builder->groupStart();
            $builder->like('nama_mitra', $keyword);
            $builder->orLike('deskripsi', $keyword);
            $builder->orLike('alamat', $keyword);
            $builder->orLike('layanan', $keyword);
            $builder->orLike('spesialisasi', $keyword);
            $builder->orLike('keahlian', $keyword);
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
    
    /**
     * Generate slug dari nama mitra (public method)
     */
    public function generateSlugPublic($nama)
    {
        $slug = url_title($nama, '-', true);
        
        // Check if slug exists
        $builder = $this->db->table($this->table);
        $builder->where('slug', $slug);
        $result = $builder->get()->getRowArray();
        
        if ($result) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }
}