<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaLayananModel extends Model
{
    protected $table = 'area_layanan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nama_area',
        'jenis_area',
        'keterangan',
        'biaya_tambahan',
        'urutan',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'nama_area' => 'required|min_length[3]|max_length[100]',
        'jenis_area' => 'required|in_list[utama,sekunder]',
        'biaya_tambahan' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'nama_area' => [
            'required' => 'Nama area wajib diisi',
            'min_length' => 'Nama area minimal 3 karakter',
            'max_length' => 'Nama area maksimal 100 karakter'
        ],
        'jenis_area' => [
            'required' => 'Jenis area wajib dipilih',
            'in_list' => 'Jenis area tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    /**
     * Get active areas ordered by type and order
     */
    public function getActiveAreas()
    {
        return $this->where('is_active', 1)
                    ->orderBy('jenis_area', 'DESC')
                    ->orderBy('urutan', 'ASC')
                    ->orderBy('nama_area', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get main areas (utama)
     */
    public function getMainAreas()
    {
        return $this->where('jenis_area', 'utama')
                    ->where('is_active', 1)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get secondary areas (sekunder)
     */
    public function getSecondaryAreas()
    {
        return $this->where('jenis_area', 'sekunder')
                    ->where('is_active', 1)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get area types for dropdown
     */
    public function getJenisAreaOptions()
    {
        return [
            'utama' => 'Area Utama',
            'sekunder' => 'Area Sekunder'
        ];
    }
    
    /**
     * Check if area exists and get cost
     */
    public function getAreaCost($areaName)
    {
        $area = $this->like('nama_area', $areaName)
                     ->where('is_active', 1)
                     ->first();
        
        if ($area) {
            return [
                'biaya_tambahan' => $area['biaya_tambahan'] ?? 0,
                'jenis_area' => $area['jenis_area']
            ];
        }
        
        return null;
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $area = $this->find($id);
        
        if ($area) {
            $newStatus = $area['is_active'] ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
    
    /**
     * Get statistics
     */
    public function getStatistics()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('is_active', 1)->countAllResults(),
            'utama' => $this->where('jenis_area', 'utama')->where('is_active', 1)->countAllResults(),
            'sekunder' => $this->where('jenis_area', 'sekunder')->where('is_active', 1)->countAllResults()
        ];
    }
}