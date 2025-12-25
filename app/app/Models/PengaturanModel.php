<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'id',
        'key_name',
        'value',
        'label',
        'type',
        'category',
        'options',
        'placeholder',
        'required',
        'order',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
protected $validationRules = [];
protected $skipValidation = true;
    protected $validationMessages = [
        'key_name' => [
            'required' => 'Key name wajib diisi',
            'alpha_dash' => 'Key name hanya boleh berisi huruf, angka, dash, dan underscore',
            'min_length' => 'Key name minimal 3 karakter',
            'max_length' => 'Key name maksimal 50 karakter',
            'is_unique' => 'Key name sudah digunakan'
        ],
        'label' => [
            'required' => 'Label wajib diisi',
            'min_length' => 'Label minimal 3 karakter',
            'max_length' => 'Label maksimal 100 karakter'
        ]
    ];
    
    protected $cleanValidationRules = true;
    
    /**
     * Get all settings grouped by category
     */
    public function getAllGrouped()
    {
        $settings = $this->where('is_active', 1)
                         ->orderBy('category', 'ASC')
                         ->orderBy('order', 'ASC')
                         ->findAll();
        
        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting['category']][] = $setting;
        }
        
        return $grouped;
    }
    
    /**
     * Get all settings as array [key => value]
     */
    public function getAllAsArray()
    {
        $settings = $this->where('is_active', 1)->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key_name']] = $setting['value'];
        }
        
        return $result;
    }
    
    /**
     * Get setting by key
     */
    public function getByKey($key, $default = null)
    {
        $setting = $this->where('key_name', $key)
                        ->where('is_active', 1)
                        ->first();
        
        return $setting ? $setting['value'] : $default;
    }
    
    /**
     * Get multiple settings by keys
     */
    public function getByKeys(array $keys)
    {
        $settings = $this->whereIn('key_name', $keys)
                         ->where('is_active', 1)
                         ->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key_name']] = $setting['value'];
        }
        
        // Fill missing keys with null
        foreach ($keys as $key) {
            if (!isset($result[$key])) {
                $result[$key] = null;
            }
        }
        
        return $result;
    }
    
    /**
     * Update setting by key
     */
    public function updateByKey($key, $value)
    {
        return $this->where('key_name', $key)->set(['value' => $value])->update();
    }
    
    /**
     * Update multiple settings
     */
    public function updateMultiple(array $data)
    {
        $success = true;
        
        foreach ($data as $key => $value) {
            $result = $this->where('key_name', $key)->set(['value' => $value])->update();
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Get settings by category
     */
    public function getByCategory($category)
    {
        $settings = $this->where('category', $category)
                         ->where('is_active', 1)
                         ->orderBy('order', 'ASC')
                         ->findAll();
        
        // Parse options untuk select, checkbox, radio
        foreach ($settings as &$setting) {
            if (in_array($setting['type'], ['select', 'checkbox', 'radio']) && !empty($setting['options'])) {
                $setting['options'] = $this->parseOptions($setting['options']);
            }
        }
        
        return $settings;
    }
    
    /**
     * Parse options string to array
     */
    private function parseOptions($optionsString)
    {
        $options = [];
        $lines = explode("\n", $optionsString);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (strpos($line, ':') !== false) {
                list($value, $label) = explode(':', $line, 2);
                $options[trim($value)] = trim($label);
            } else {
                $options[$line] = $line;
            }
        }
        
        return $options;
    }
    
    /**
     * Get system information settings
     */
    public function getSystemInfo()
    {
        return [
            'app_name' => $this->getByKey('nama_toko', 'Maulia Wedding'),
            'app_version' => '1.0.0',
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'php_version' => phpversion(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => $this->db->getPlatform(),
            'environment' => ENVIRONMENT,
            'timezone' => app_timezone(),
            'base_url' => base_url()
        ];
    }
    
    /**
     * Get contact settings
     */
    public function getContactInfo()
    {
        return $this->getByKeys([
            'nama_toko',
            'alamat',
            'whatsapp',
            'telepon',
            'email',
            'jam_kerja',
            'latitude',
            'longitude',
            'google_maps_url'
        ]);
    }
    
    /**
     * Get social media settings
     */
    public function getSocialMedia()
    {
        return $this->getByKeys([
            'instagram',
            'facebook',
            'tiktok',
            'youtube',
            'twitter'
        ]);
    }
    
    /**
     * Get SEO settings
     */
    public function getSeoSettings()
    {
        return $this->getByKeys([
            'meta_title',
            'meta_description',
            'meta_keywords',
            'og_image',
            'twitter_site'
        ]);
    }
    
    /**
     * Get payment settings
     */
    public function getPaymentSettings()
    {
        return $this->getByKeys([
            'bank_name',
            'bank_account',
            'account_name',
            'payment_instruction'
        ]);
    }
    
    /**
     * Initialize default settings if not exists
     */
    public function initializeDefaultSettings()
    {
        $defaultSettings = [
            // General
            [
                'key_name' => 'nama_toko',
                'value' => 'Maulia Makeup & Sewa Kostum',
                'label' => 'Nama Toko/Brand',
                'type' => 'text',
                'category' => 'general',
                'order' => 1,
                'required' => 1
            ],
            [
                'key_name' => 'slogan',
                'value' => 'Profesional Wedding Make Up Artist & Kostum Sewa',
                'label' => 'Slogan/Tagline',
                'type' => 'text',
                'category' => 'general',
                'order' => 2,
                'required' => 0
            ],
            [
                'key_name' => 'logo',
                'value' => '',
                'label' => 'Logo Website',
                'type' => 'file',
                'category' => 'general',
                'order' => 3,
                'required' => 0
            ],
            
            // Contact
            [
                'key_name' => 'alamat',
                'value' => 'Desa Klambu, Kecamatan Klambu, Kabupaten Grobogan, Jawa Tengah',
                'label' => 'Alamat Lengkap',
                'type' => 'textarea',
                'category' => 'kontak',
                'order' => 1,
                'required' => 1
            ],
            [
                'key_name' => 'whatsapp',
                'value' => '087731310979',
                'label' => 'Nomor WhatsApp',
                'type' => 'tel',
                'category' => 'kontak',
                'order' => 2,
                'required' => 1
            ],
            [
                'key_name' => 'telepon',
                'value' => '087731310979',
                'label' => 'Nomor Telepon',
                'type' => 'tel',
                'category' => 'kontak',
                'order' => 3,
                'required' => 0
            ],
            [
                'key_name' => 'email',
                'value' => 'info@maulia.com',
                'label' => 'Email',
                'type' => 'email',
                'category' => 'kontak',
                'order' => 4,
                'required' => 0
            ],
            [
                'key_name' => 'jam_kerja',
                'value' => 'Senin - Minggu: 08:00 - 18:00 WIB',
                'label' => 'Jam Operasional',
                'type' => 'text',
                'category' => 'kontak',
                'order' => 5,
                'required' => 0
            ],
            
            // Location
            [
                'key_name' => 'latitude',
                'value' => '-7.0069338',
                'label' => 'Latitude',
                'type' => 'text',
                'category' => 'lokasi',
                'order' => 1,
                'required' => 0
            ],
            [
                'key_name' => 'longitude',
                'value' => '110.7955922',
                'label' => 'Longitude',
                'type' => 'text',
                'category' => 'lokasi',
                'order' => 2,
                'required' => 0
            ],
            [
                'key_name' => 'google_maps_url',
                'value' => 'https://maps.app.goo.gl/KaDJLxzmVYHNynfs5',
                'label' => 'URL Google Maps',
                'type' => 'text',
                'category' => 'lokasi',
                'order' => 3,
                'required' => 0
            ],
            
            // Social Media
            [
                'key_name' => 'instagram',
                'value' => 'https://instagram.com/maulia',
                'label' => 'Instagram',
                'type' => 'text',
                'category' => 'sosmed',
                'order' => 1,
                'required' => 0
            ],
            [
                'key_name' => 'facebook',
                'value' => 'https://facebook.com/maulia',
                'label' => 'Facebook',
                'type' => 'text',
                'category' => 'sosmed',
                'order' => 2,
                'required' => 0
            ],
            [
                'key_name' => 'tiktok',
                'value' => 'https://tiktok.com/@maulia',
                'label' => 'TikTok',
                'type' => 'text',
                'category' => 'sosmed',
                'order' => 3,
                'required' => 0
            ],
            [
                'key_name' => 'youtube',
                'value' => '',
                'label' => 'YouTube',
                'type' => 'text',
                'category' => 'sosmed',
                'order' => 4,
                'required' => 0
            ],
            
            // SEO
            [
                'key_name' => 'meta_title',
                'value' => 'Maulia - Professional Wedding Make Up Artist & Kostum Sewa - Grobogan',
                'label' => 'Meta Title',
                'type' => 'text',
                'category' => 'seo',
                'order' => 1,
                'required' => 0
            ],
            [
                'key_name' => 'meta_description',
                'value' => 'Profesional Wedding Make Up Artist & Penyewaan Kostum Pernikahan di Grobogan, Jawa Tengah',
                'label' => 'Meta Description',
                'type' => 'textarea',
                'category' => 'seo',
                'order' => 2,
                'required' => 0
            ],
            [
                'key_name' => 'meta_keywords',
                'value' => 'makeup pengantin, sewa kostum, wedding, grobogan, jawa tengah',
                'label' => 'Meta Keywords',
                'type' => 'text',
                'category' => 'seo',
                'order' => 3,
                'required' => 0
            ],
            
            // Payment
            [
                'key_name' => 'bank_name',
                'value' => 'BRI',
                'label' => 'Nama Bank',
                'type' => 'text',
                'category' => 'pembayaran',
                'order' => 1,
                'required' => 0
            ],
            [
                'key_name' => 'bank_account',
                'value' => '1234-5678-9012-3456',
                'label' => 'Nomor Rekening',
                'type' => 'text',
                'category' => 'pembayaran',
                'order' => 2,
                'required' => 0
            ],
            [
                'key_name' => 'account_name',
                'value' => 'MAULIA WEDDING',
                'label' => 'Atas Nama',
                'type' => 'text',
                'category' => 'pembayaran',
                'order' => 3,
                'required' => 0
            ],
            
            // Appearance
            [
                'key_name' => 'primary_color',
                'value' => '#d4b8a3',
                'label' => 'Warna Primer',
                'type' => 'color',
                'category' => 'tampilan',
                'order' => 1,
                'required' => 0
            ],
            [
                'key_name' => 'secondary_color',
                'value' => '#b8a7c8',
                'label' => 'Warna Sekunder',
                'type' => 'color',
                'category' => 'tampilan',
                'order' => 2,
                'required' => 0
            ],
            
            // Business
            [
                'key_name' => 'dp_percentage',
                'value' => '50',
                'label' => 'Persentase DP (%)',
                'type' => 'number',
                'category' => 'bisnis',
                'order' => 1,
                'required' => 1
            ],
            [
                'key_name' => 'late_fee_per_day',
                'value' => '50000',
                'label' => 'Denda Keterlambatan per Hari',
                'type' => 'number',
                'category' => 'bisnis',
                'order' => 2,
                'required' => 0
            ]
        ];
        
        $inserted = 0;
        foreach ($defaultSettings as $setting) {
            $existing = $this->where('key_name', $setting['key_name'])->first();
            if (!$existing) {
                $this->insert($setting);
                $inserted++;
            }
        }
        
        return $inserted;
    }
    
    /**
     * Get setting categories for dropdown
     */
    public function getCategories()
    {
        $categories = $this->select('category')
                          ->groupBy('category')
                          ->orderBy('category')
                          ->findAll();
        
        $result = [];
        foreach ($categories as $cat) {
            $result[$cat['category']] = ucfirst($cat['category']);
        }
        
        return $result;
    }
    
    /**
     * Get field types for dropdown
     */
    public function getFieldTypes()
    {
        return [
            'text' => 'Text',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'email' => 'Email',
            'tel' => 'Telephone',
            'password' => 'Password',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button',
            'file' => 'File Upload',
            'color' => 'Color Picker',
            'date' => 'Date'
        ];
    }
    public function getCategoriesWithCount()
{
    $builder = $this->db->table($this->table);
    $builder->select('category, COUNT(*) as count');
    $builder->groupBy('category');
    $builder->orderBy('category', 'ASC');
    $query = $builder->get();
    
    $result = [];
    foreach ($query->getResultArray() as $row) {
        $result[$row['category']] = ucfirst($row['category']) . ' (' . $row['count'] . ')';
    }
    
    return $result;
}

}