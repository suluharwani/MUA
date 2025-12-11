<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kategori',
        'style',
        'judul',
        'deskripsi',
        'gambar',
        'gambar_tambahan',
        'tema_warna',
        'produk_digunakan',
        'lokasi_pemotretan',
        'makeup_artist',
        'model',
        'is_featured',
        'is_active',
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
        'judul' => 'required|min_length[3]|max_length[200]',
        'kategori' => 'required|in_list[makeup,kostum,portfolio,testimonial,behind_scenes]',
        'gambar' => 'required',
        'style' => 'permit_empty|max_length[100]'
    ];
    
    protected $validationMessages = [
        'judul' => [
            'required' => 'Judul gallery wajib diisi',
            'min_length' => 'Judul minimal 3 karakter',
            'max_length' => 'Judul maksimal 200 karakter'
        ],
        'kategori' => [
            'required' => 'Kategori wajib dipilih',
            'in_list' => 'Kategori tidak valid'
        ],
        'gambar' => [
            'required' => 'Gambar utama wajib diupload'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    /**
     * Get all gallery dengan filter
     */
    public function getAllWithFilter($kategori = null, $status = 'active', $search = null, $style = null, $limit = null, $offset = 0)
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
        
        if ($style) {
            $builder->where('style', $style);
        }
        
        if ($search) {
            $builder->groupStart();
            $builder->like('judul', $search);
            $builder->orLike('deskripsi', $search);
            $builder->orLike('tema_warna', $search);
            $builder->orLike('produk_digunakan', $search);
            $builder->orLike('style', $search);
            $builder->groupEnd();
        }
        
        $builder->orderBy('is_featured', 'DESC');
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        $query = $builder->get();
        $gallery = $query->getResultArray();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
    /**
     * Get gallery by ID dengan related info
     */
    public function getWithRelated($id)
    {
        $gallery = $this->find($id);
        
        if ($gallery) {
            // Decode gambar tambahan
            $gallery['gambar_tambahan'] = json_decode($gallery['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
    /**
     * Get featured gallery
     */
    public function getFeatured($limit = 8)
    {
        $gallery = $this->where('is_featured', 1)
                        ->where('is_active', 1)
                        ->orderBy('urutan', 'ASC')
                        ->limit($limit)
                        ->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
    /**
     * Get gallery by kategori
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
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $gallery = $builder->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
    /**
     * Get gallery by style
     */
    public function getByStyle($style, $limit = null)
    {
        $gallery = $this->where('style', $style)
                        ->where('is_active', 1)
                        ->orderBy('is_featured', 'DESC')
                        ->orderBy('urutan', 'ASC')
                        ->limit($limit)
                        ->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
    /**
     * Get kategori options untuk dropdown
     */
    public function getKategoriOptions()
    {
        return [
            'makeup' => 'Makeup Art',
            'kostum' => 'Kostum & Busana',
            'portfolio' => 'Portfolio Lengkap',
            'testimonial' => 'Testimoni Pelanggan',
            'behind_scenes' => 'Behind The Scenes',
            'inspirasi' => 'Inspirasi Tema'
        ];
    }
    
    /**
     * Get style options untuk dropdown (khusus makeup)
     */
    public function getStyleOptions()
    {
        return [
            'tradisional' => 'Tradisional',
            'modern' => 'Modern',
            'natural' => 'Natural Glam',
            'glamour' => 'Glamour',
            'bold' => 'Bold & Dramatic',
            'soft' => 'Soft & Romantic',
            'bohemian' => 'Bohemian',
            'vintage' => 'Vintage',
            'kultural' => 'Kultural (Jawa, Sunda, dll)',
            'kontemporer' => 'Kontemporer'
        ];
    }
    
    /**
     * Get related gallery
     */
    public function getRelated($galleryId, $limit = 6)
    {
        $currentGallery = $this->find($galleryId);
        
        if (!$currentGallery) {
            return [];
        }
        
        $related = $this->where('kategori', $currentGallery['kategori'])
                        ->where('id !=', $galleryId)
                        ->where('is_active', 1)
                        ->orderBy('RAND()')
                        ->limit($limit)
                        ->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($related as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $related;
    }
    
    /**
     * Get gallery untuk homepage
     */
    public function getForHomepage($limit = 6)
    {
        $gallery = $this->where('is_featured', 1)
                        ->where('is_active', 1)
                        ->whereIn('kategori', ['makeup', 'kostum', 'portfolio'])
                        ->orderBy('urutan', 'ASC')
                        ->limit($limit)
                        ->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
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
            'by_style' => []
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
        
        // Hitung per style
        $builder = $this->db->table($this->table);
        $builder->select('style, COUNT(*) as total');
        $builder->where('is_active', 1);
        $builder->where('style !=', '');
        $builder->groupBy('style');
        $builder->orderBy('total', 'DESC');
        $result = $builder->get()->getResultArray();
        
        foreach ($result as $row) {
            $stats['by_style'][$row['style']] = $row['total'];
        }
        
        return $stats;
    }
    
    /**
     * Search gallery with pagination
     */
    public function searchWithPagination($keyword, $kategori = null, $style = null, $perPage = 12, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where('is_active', 1);
        
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        
        if ($style) {
            $builder->where('style', $style);
        }
        
        if ($keyword) {
            $builder->groupStart();
            $builder->like('judul', $keyword);
            $builder->orLike('deskripsi', $keyword);
            $builder->orLike('tema_warna', $keyword);
            $builder->orLike('produk_digunakan', $keyword);
            $builder->orLike('style', $keyword);
            $builder->groupEnd();
        }
        
        // Hitung total hasil
        $total = $builder->countAllResults(false);
        
        // Get data dengan pagination
        $builder->limit($perPage, $offset);
        $builder->orderBy('is_featured', 'DESC');
        $builder->orderBy('urutan', 'ASC');
        $builder->orderBy('created_at', 'DESC');
        $query = $builder->get();
        $gallery = $query->getResultArray();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return [
            'data' => $gallery,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $gallery = $this->find($id);
        
        if ($gallery) {
            $newStatus = $gallery['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $gallery = $this->find($id);
        
        if ($gallery) {
            $newFeatured = $gallery['is_featured'] ? 0 : 1;
            return $this->update($id, ['is_featured' => $newFeatured]);
        }
        
        return false;
    }
    
    /**
     * Get recent gallery
     */
    public function getRecent($limit = 6)
    {
        $gallery = $this->where('is_active', 1)
                        ->orderBy('created_at', 'DESC')
                        ->limit($limit)
                        ->findAll();
        
        // Decode gambar tambahan untuk setiap item
        foreach ($gallery as &$item) {
            $item['gambar_tambahan'] = json_decode($item['gambar_tambahan'] ?? '[]', true);
        }
        
        return $gallery;
    }
    
}