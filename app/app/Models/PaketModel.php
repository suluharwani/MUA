<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketModel extends Model
{
    protected $table = 'paket_makeup';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nama_paket',
        'slug',
        'deskripsi',
        'harga',
        'durasi',
        'is_featured',
        'features',
        'urutan',
        'is_active',
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
        'nama_paket' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|is_unique[paket_makeup.slug,id,{id}]',
        'harga' => 'required|numeric',
        'durasi' => 'permit_empty|max_length[50]'
    ];
    
    protected $validationMessages = [
        'nama_paket' => [
            'required' => 'Nama paket wajib diisi',
            'min_length' => 'Nama paket minimal 3 karakter',
            'max_length' => 'Nama paket maksimal 100 karakter'
        ],
        'slug' => [
            'required' => 'Slug wajib diisi',
            'is_unique' => 'Slug sudah digunakan'
        ],
        'harga' => [
            'required' => 'Harga wajib diisi',
            'numeric' => 'Harga harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateSlug', 'formatFeatures'];
    protected $beforeUpdate = ['generateSlug', 'formatFeatures'];
    
    /**
     * Generate slug dari nama paket
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['nama_paket']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['nama_paket'], '-', true);
        }
        return $data;
    }
    
    /**
     * Format features dari textarea ke JSON
     */
    protected function formatFeatures(array $data)
    {
        if (isset($data['data']['features'])) {
            if (is_string($data['data']['features'])) {
                $featuresArray = explode("\n", $data['data']['features']);
                $featuresArray = array_map('trim', $featuresArray);
                $featuresArray = array_filter($featuresArray); // Hapus empty lines
                $data['data']['features'] = json_encode($featuresArray);
            }
        }
        return $data;
    }
    
    /**
     * Get all active packages ordered by featured and order
     */
    public function getActivePackages($limit = null)
    {
        $builder = $this->where('is_active', 1)
                        ->orderBy('is_featured', 'DESC')
                        ->orderBy('urutan', 'ASC')
                        ->orderBy('harga', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $packages = $builder->findAll();
        
        // Decode features untuk setiap paket
        foreach ($packages as &$package) {
            $package['features'] = json_decode($package['features'] ?? '[]', true);
        }
        
        return $packages;
    }
    
    /**
     * Get featured packages
     */
    public function getFeatured($limit = 3)
    {
        $packages = $this->where('is_featured', 1)
                         ->where('is_active', 1)
                         ->orderBy('urutan', 'ASC')
                         ->limit($limit)
                         ->findAll();
        
        // Decode features untuk setiap paket
        foreach ($packages as &$package) {
            $package['features'] = json_decode($package['features'] ?? '[]', true);
        }
        
        return $packages;
    }
    
    /**
     * Get package by slug
     */
    public function getBySlug($slug)
    {
        $package = $this->where('slug', $slug)
                        ->where('is_active', 1)
                        ->first();
        
        if ($package) {
            $package['features'] = json_decode($package['features'] ?? '[]', true);
        }
        
        return $package;
    }
    
    /**
     * Get statistics for admin dashboard
     */
    public function getStatistics()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'inactive' => $this->where('is_active', 0)->countAllResults(),
            'featured' => $this->where('is_featured', 1)->countAllResults(),
            'average_price' => $this->selectAvg('harga')->where('is_active', 1)->get()->getRow()->harga ?? 0
        ];
    }
    
    /**
     * Search packages
     */
    public function search($keyword)
    {
        $packages = $this->like('nama_paket', $keyword)
                         ->orLike('deskripsi', $keyword)
                         ->orLike('features', $keyword)
                         ->orderBy('urutan', 'ASC')
                         ->findAll();
        
        // Decode features untuk setiap paket
        foreach ($packages as &$package) {
            $package['features'] = json_decode($package['features'] ?? '[]', true);
        }
        
        return $packages;
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $package = $this->find($id);
        
        if ($package) {
            $newStatus = $package['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $package = $this->find($id);
        
        if ($package) {
            $newFeatured = $package['is_featured'] ? 0 : 1;
            return $this->update($id, ['is_featured' => $newFeatured]);
        }
        
        return false;
    }
}