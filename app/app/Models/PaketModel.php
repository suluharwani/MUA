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
        'harga' => 'required|numeric',
        'durasi' => 'permit_empty|max_length[50]'
    ];
    
    protected $validationMessages = [
        'nama_paket' => [
            'required' => 'Nama paket wajib diisi',
            'min_length' => 'Nama paket minimal 3 karakter',
            'max_length' => 'Nama paket maksimal 100 karakter'
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
    protected $beforeInsert = ['generateSlug', 'formatFeatures', 'setTimestamps'];
    protected $beforeUpdate = ['generateSlug', 'formatFeatures', 'setTimestamps'];
    
    /**
     * Generate slug dari nama paket
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['nama_paket']) && (!isset($data['data']['slug']) || empty($data['data']['slug']))) {
            helper('text');
            $slug = url_title($data['data']['nama_paket'], '-', true);
            
            // Cek jika slug sudah ada, tambahkan angka unik
            $count = $this->where('slug', $slug);
            if (isset($data['id'])) {
                $count->where('id !=', $data['id']);
            }
            $count = $count->countAllResults();
            
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            
            $data['data']['slug'] = $slug;
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
                $featuresArray = array_filter($featuresArray, function($item) {
                    return !empty($item);
                });
                
                if (!empty($featuresArray)) {
                    $data['data']['features'] = json_encode($featuresArray);
                } else {
                    $data['data']['features'] = '[]';
                }
            }
        } else {
            $data['data']['features'] = '[]';
        }
        return $data;
    }
    
    /**
     * Set timestamps
     */
    protected function setTimestamps(array $data)
    {
        $currentTime = date('Y-m-d H:i:s');
        
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = $currentTime;
        }
        
        $data['data']['updated_at'] = $currentTime;
        
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
        $builder = $this->selectAvg('harga')->where('is_active', 1);
        $avgResult = $builder->get()->getRow();
        $averagePrice = $avgResult ? $avgResult->harga : 0;
        
        return [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'inactive' => $this->where('is_active', 0)->countAllResults(),
            'featured' => $this->where('is_featured', 1)->countAllResults(),
            'average_price' => $averagePrice
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
    
    /**
     * Custom save method dengan debugging
     */
    public function save($data): bool
    {
        try {
            // Debug data
            log_message('info', 'Data to save: ' . print_r($data, true));
            
            // Pastikan features adalah JSON jika array
            if (isset($data['features']) && is_array($data['features'])) {
                $data['features'] = json_encode($data['features']);
            }
            
            // Panggil parent save method
            return parent::save($data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error saving package: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}