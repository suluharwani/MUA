<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

class PengaturanAjax extends BaseController
{
    protected $pengaturanModel;
    
    public function __construct()
    {
        $this->pengaturanModel = new \App\Models\PengaturanModel();
         helper(['form', 'text', 'file']);
    }
    
    public function index()
    {
        $pengaturanModel = new \App\Models\PengaturanModel();
        $data = [
        'title' => 'Pengaturan Sistem',
        'categories' => $pengaturanModel->getCategories(),
        'field_types' => $pengaturanModel->getFieldTypes(),
        'system_info' => $pengaturanModel->getSystemInfo()
    ];
    
        
        return view('admin/pengaturan/ajax_index', $data);
    }
  public function getSettingByKey($key_name)
{
    $setting = $this->pengaturanModel->where('key_name', $key_name)->first();
    
    if (!$setting) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Pengaturan tidak ditemukan'
        ]);
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $setting
    ]);
}  
public function getSettings()
{
    $draw = $this->request->getGet('draw');
    $start = (int)$this->request->getGet('start');
    $length = (int)$this->request->getGet('length');
    $search = $this->request->getGet('search')['value'] ?? '';
    $category = $this->request->getGet('category') ?? 'general';
    $orderColumn = $this->request->getGet('order')[0]['column'] ?? 0;
    $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
    
    $model = new PengaturanModel();
    
    // Total records tanpa filter
    $totalRecords = $model->countAll();
    
    // Filter by category
    if ($category && $category !== 'all') {
        $model->where('category', $category);
    }
    
    // Apply search
    if (!empty($search)) {
        $model->groupStart()
              ->like('key_name', $search)
              ->orLike('label', $search)
              ->orLike('value', $search)
              ->groupEnd();
    }
    
    // Filtered count dengan filter
    $filteredCount = $model->countAllResults(false);
    
    // Apply ordering
    $columns = ['id', 'key_name', 'label', 'value', 'type', 'category', 'is_active'];
    $orderBy = $columns[$orderColumn] ?? 'id';
    $model->orderBy($orderBy, $orderDir);
    
    // Apply pagination
    $model->limit($length, $start);
    
    // Get data
    $data = $model->findAll();
    
    // Format data for DataTables
    $formattedData = [];
    foreach ($data as $row) {
        $formattedData[] = [
            'id' => $row['id'],
            'key_name' => $row['key_name'],
            'label' => $row['label'],
            'value' => $this->formatValue($row['value'], $row['type']),
            'type' => ucfirst($row['type']),
            'category' => ucfirst($row['category']),
            'status' => $row['is_active'] == 1 
                ? '<span class="badge bg-success">Aktif</span>' 
                : '<span class="badge bg-danger">Nonaktif</span>',
            'actions' => $this->getActionButtons($row)
        ];
    }
    
    return $this->response->setJSON([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredCount,
        'data' => $formattedData
    ]);
}
    
private function formatValue($value, $type)
{
    if (empty($value)) return '<em class="text-muted">(kosong)</em>';
    
    $maxLength = 50;
    
    switch ($type) {
        case 'color':
            return '<div class="d-flex align-items-center">
                      <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: ' . $value . '; border: 1px solid #ddd;"></div>
                      ' . $value . '
                    </div>';
        case 'textarea':
            $shortText = strlen($value) > $maxLength ? substr($value, 0, $maxLength) . '...' : $value;
            return '<span title="' . htmlspecialchars($value) . '">' . htmlspecialchars($shortText) . '</span>';
        case 'password':
            return '••••••••';
        case 'file':
            return '<a href="' . base_url($value) . '" target="_blank">' . basename($value) . '</a>';
        default:
            $shortText = strlen($value) > $maxLength ? substr($value, 0, $maxLength) . '...' : $value;
            return '<span title="' . htmlspecialchars($value) . '">' . htmlspecialchars($shortText) . '</span>';
    }
}

// Helper method untuk action buttons

    
    private function getStatusBadge($status)
    {
        if ($status) {
            return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Aktif</span>';
        } else {
            return '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Nonaktif</span>';
        }
    }
    
    private function getActionButtons($setting)
    {
        $buttons = '<div class="btn-group btn-group-sm" role="group">';
        
        // Edit button
        $buttons .= '<button type="button" class="btn btn-outline-primary edit-btn" data-id="' . $setting['id'] . '" title="Edit">
                        <i class="bi bi-pencil"></i>
                     </button>';
        
        // Toggle status button
        $toggleText = $setting['is_active'] ? 'Nonaktifkan' : 'Aktifkan';
        $toggleIcon = $setting['is_active'] ? 'bi-power' : 'bi-power';
        $toggleClass = $setting['is_active'] ? 'warning' : 'success';
        
        $buttons .= '<button type="button" class="btn btn-outline-' . $toggleClass . ' toggle-status-btn" data-id="' . $setting['id'] . '" title="' . $toggleText . '">
                        <i class="bi ' . $toggleIcon . '"></i>
                     </button>';
        
        // Delete button (disable for system settings)
        $systemKeys = ['nama_toko', 'whatsapp', 'alamat', 'email', 'dp_percentage'];
        // if (!in_array($setting['key_name'], $systemKeys)) {
        //     $buttons .= '<button type="button" class="btn btn-outline-danger delete-btn" data-id="' . $setting['id'] . '" data-name="' . esc($setting['label']) . '" title="Hapus">
        //                     <i class="bi bi-trash"></i>
        //                  </button>';
        // } else {
        //     $buttons .= '<button type="button" class="btn btn-outline-secondary" disabled title="Pengaturan sistem tidak dapat dihapus">
        //                     <i class="bi bi-trash"></i>
        //                  </button>';
        // }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
    
    public function getSettingDetail($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan tidak ditemukan'
            ]);
        }
        
        // Parse options if exists
        if (!empty($setting['options']) && in_array($setting['type'], ['select', 'checkbox', 'radio'])) {
            $setting['options'] = $this->pengaturanModel->parseOptions($setting['options']);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $setting
        ]);
    }
    
public function save()
{
    $response = ['status' => 'error', 'message' => ''];
    
    $id = $this->request->getPost('id');
    $key_name = trim($this->request->getPost('key_name'));
    $label = trim($this->request->getPost('label'));
    $type = $this->request->getPost('type');
    
    // Setup validation rules
    $validationRules = [
        'key_name' => [
            'rules' => 'required|alpha_dash|min_length[3]|max_length[50]',
            'errors' => [
                'required' => 'Key name wajib diisi',
                'alpha_dash' => 'Key name hanya boleh berisi huruf, angka, dash, dan underscore',
                'min_length' => 'Key name minimal 3 karakter',
                'max_length' => 'Key name maksimal 50 karakter',
            ]
        ],
        'label' => [
            'rules' => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required' => 'Label wajib diisi',
                'min_length' => 'Label minimal 3 karakter',
                'max_length' => 'Label maksimal 100 karakter'
            ]
        ],
        'type' => [
            'rules' => 'required|in_list[text,textarea,number,email,tel,password,select,checkbox,radio,file,color,date]',
            'errors' => [
                'required' => 'Tipe wajib dipilih',
                'in_list' => 'Tipe tidak valid'
            ]
        ],
        'category' => [
            'rules' => 'required|max_length[50]',
            'errors' => [
                'required' => 'Kategori wajib dipilih',
                'max_length' => 'Kategori maksimal 50 karakter'
            ]
        ]
    ];
    
    // Custom validation untuk uniqueness
    $model = new PengaturanModel();
    if (empty($id)) {
        $validationRules['key_name']['rules'] .= '|is_unique[pengaturan.key_name]';
        $validationRules['key_name']['errors']['is_unique'] = 'Key name sudah digunakan';
    } else {
        // Untuk update, cek uniqueness secara manual
        $existing = $model->where('key_name', $key_name)
                         ->where('id !=', $id)
                         ->first();
        if ($existing) {
            $response['errors'] = ['key_name' => 'Key name sudah digunakan'];
            return $this->response->setJSON($response);
        }
    }
    
    // Run validation
    $validation = \Config\Services::validation();
    $validation->setRules($validationRules);
    
    if (!$validation->withRequest($this->request)->run()) {
        $response['errors'] = $validation->getErrors();
        return $this->response->setJSON($response);
    }
    
    // Prepare data
    $data = [
        'key_name' => $key_name,
        'value' => $this->request->getPost('value') ?? '',
        'label' => $label,
        'type' => $type,
        'category' => $this->request->getPost('category'),
        'options' => $this->request->getPost('options'),
        'placeholder' => $this->request->getPost('placeholder'),
        'order' => (int)($this->request->getPost('order') ?? 0),
        'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        'required' => $this->request->getPost('required') ? 1 : 0
    ];
    
    // Handle file upload
    if ($type === 'file') {
        $file = $this->request->getFile('value');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Tentukan folder berdasarkan kategori atau key_name
            $folder = 'uploads/settings/';
            if (in_array($key_name, ['logo', 'logo_dark'])) {
                $folder = 'uploads/logo/';
            } elseif ($key_name === 'favicon') {
                $folder = 'uploads/favicon/';
            }
            
            // Buat folder jika belum ada
            if (!is_dir(FCPATH . $folder)) {
                mkdir(FCPATH . $folder, 0755, true);
            }
            
            // Validasi tipe file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'webp', 'pdf'];
            $extension = $file->getExtension();
            
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $response['message'] = 'Format file tidak didukung. Gunakan: ' . implode(', ', $allowedExtensions);
                return $this->response->setJSON($response);
            }
            
            // Validasi ukuran file (max 5MB)
            if ($file->getSize() > 5242880) {
                $response['message'] = 'Ukuran file terlalu besar. Maksimal 5MB';
                return $this->response->setJSON($response);
            }
            
            // Jika update, hapus file lama
            if (!empty($id)) {
                $oldSetting = $model->find($id);
                if ($oldSetting && !empty($oldSetting['value']) && file_exists(FCPATH . $oldSetting['value'])) {
                    unlink(FCPATH . $oldSetting['value']);
                }
            }
            
            // Generate nama file (gunakan key_name + timestamp untuk unik)
            $newFileName = $key_name . '_' . time() . '.' . $extension;
            $filePath = $folder . $newFileName;
            
            // Pindahkan file
            if ($file->move(FCPATH . $folder, $newFileName)) {
                $data['value'] = $filePath;
            } else {
                $response['message'] = 'Gagal mengupload file';
                return $this->response->setJSON($response);
            }
        } else {
            // Jika file tidak diupload dalam edit mode, pertahankan value yang ada
            if (!empty($id)) {
                $oldSetting = $model->find($id);
                if ($oldSetting && $oldSetting['type'] === 'file') {
                    $data['value'] = $oldSetting['value'];
                }
            }
        }
    }
    
    try {
        $model->skipValidation(true);
        
        if (!empty($id)) {
            $result = $model->update($id, $data);
            $message = 'Pengaturan berhasil diperbarui';
        } else {
            $result = $model->insert($data);
            $message = 'Pengaturan berhasil ditambahkan';
        }
        
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = $message;
            
            // Jika file diupload, kirim path untuk preview
            if ($type === 'file' && isset($filePath)) {
                $response['file_path'] = base_url($filePath);
                $response['file_name'] = basename($filePath);
            }
        } else {
            $response['message'] = 'Gagal menyimpan pengaturan';
        }
        
    } catch (\Exception $e) {
        $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    
    return $this->response->setJSON($response);
}
    
    // Tambahkan method untuk upload file saja
    public function uploadFile()
    {
        $response = ['status' => 'error', 'message' => ''];
        
        $key_name = $this->request->getPost('key_name');
        $file = $this->request->getFile('file');
        
        if (!$file || !$file->isValid()) {
            $response['message'] = 'File tidak valid';
            return $this->response->setJSON($response);
        }
        
        // Validasi tipe file
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'webp'];
        $extension = $file->getExtension();
        
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            $response['message'] = 'Format file tidak didukung. Gunakan: ' . implode(', ', $allowedExtensions);
            return $this->response->setJSON($response);
        }
        
        // Validasi ukuran file (max 5MB)
        if ($file->getSize() > 5242880) {
            $response['message'] = 'Ukuran file terlalu besar. Maksimal 5MB';
            return $this->response->setJSON($response);
        }
        
        // Tentukan folder berdasarkan key_name
        $folder = 'uploads/settings/';
        if (in_array($key_name, ['logo', 'logo_dark'])) {
            $folder = 'uploads/logo/';
        } elseif ($key_name === 'favicon') {
            $folder = 'uploads/favicon/';
        }
        
        // Buat folder jika belum ada
        if (!is_dir(FCPATH . $folder)) {
            mkdir(FCPATH . $folder, 0755, true);
        }
        
        // Generate nama file unik
        $fileName = $key_name . '_' . time() . '.' . $extension;
        
        try {
            // Cari setting yang sudah ada
            $setting = $this->pengaturanModel->where('key_name', $key_name)->first();
            
            // Hapus file lama jika ada
            if ($setting && !empty($setting['value']) && file_exists(FCPATH . $setting['value'])) {
                unlink(FCPATH . $setting['value']);
            }
            
            // Pindahkan file
            $file->move(FCPATH . $folder, $fileName);
            
            // Update atau insert setting
            $filePath = $folder . $fileName;
            
            if ($setting) {
                $this->pengaturanModel->update($setting['id'], ['value' => $filePath]);
            } else {
                // Buat setting baru
                $label = ucfirst(str_replace('_', ' ', $key_name));
                $this->pengaturanModel->insert([
                    'key_name' => $key_name,
                    'value' => $filePath,
                    'label' => $label,
                    'type' => 'file',
                    'category' => 'tampilan',
                    'is_active' => 1
                ]);
            }
            
            $response['status'] = 'success';
            $response['message'] = 'File berhasil diupload';
            $response['file_path'] = base_url($filePath);
            $response['file_name'] = $fileName;
            
        } catch (\Exception $e) {
            $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    // Method untuk delete file
    public function deleteFile($key_name)
    {
        $setting = $this->pengaturanModel->where('key_name', $key_name)->first();
        
        if (!$setting) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan tidak ditemukan'
            ]);
        }
        
        try {
            // Hapus file fisik
            if (!empty($setting['value']) && file_exists(FCPATH . $setting['value'])) {
                unlink(FCPATH . $setting['value']);
            }
            
            // Update value menjadi kosong
            $this->pengaturanModel->update($setting['id'], ['value' => '']);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'File berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    // Method untuk get logo dan favicon info
    public function getLogoInfo()
    {
        $logo = $this->pengaturanModel->getByKey('logo');
        $logo_dark = $this->pengaturanModel->getByKey('logo_dark');
        $favicon = $this->pengaturanModel->getByKey('favicon');
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'logo' => $logo ? base_url($logo) : null,
                'logo_dark' => $logo_dark ? base_url($logo_dark) : null,
                'favicon' => $favicon ? base_url($favicon) : null,
                'logo_path' => $logo,
                'favicon_path' => $favicon
            ]
        ]);
    } 
    public function delete($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan tidak ditemukan'
            ]);
        }
        
        // Check if it's a system setting
        $systemKeys = ['nama_toko', 'whatsapp', 'alamat', 'email', 'dp_percentage'];
        if (in_array($setting['key_name'], $systemKeys)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan sistem tidak dapat dihapus'
            ]);
        }
        
        if ($this->pengaturanModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pengaturan berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus pengaturan'
            ]);
        }
    }
    
    public function toggleStatus($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan tidak ditemukan'
            ]);
        }
        
        $newStatus = $setting['is_active'] ? 0 : 1;
        
        if ($this->pengaturanModel->update($id, ['is_active' => $newStatus])) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Status pengaturan berhasil diubah',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengubah status pengaturan'
            ]);
        }
    }
    
    public function updateMultiple()
    {
        $data = $this->request->getPost('settings');
        
        if (empty($data) || !is_array($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }
        
        $success = true;
        $updated = 0;
        
        foreach ($data as $key => $value) {
            $result = $this->pengaturanModel->where('key_name', $key)->set(['value' => $value])->update();
            if ($result) {
                $updated++;
            } else {
                $success = false;
            }
        }
        
        if ($success) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "{$updated} pengaturan berhasil diperbarui"
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => "{$updated} pengaturan diperbarui, beberapa gagal"
            ]);
        }
    }
    
    public function backup()
    {
        $settings = $this->pengaturanModel->findAll();
        
        $filename = 'settings_backup_' . date('Ymd_His') . '.json';
        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (!is_dir(WRITEPATH . 'backups')) {
            mkdir(WRITEPATH . 'backups', 0755, true);
        }
        
        file_put_contents($filepath, json_encode($settings, JSON_PRETTY_PRINT));
        
        return $this->response->download($filepath, null);
    }
    
    public function restore()
    {
        $file = $this->request->getFile('backup_file');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File tidak valid'
            ]);
        }
        
        if ($file->getExtension() !== 'json') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File harus berformat JSON'
            ]);
        }
        
        $content = file_get_contents($file->getTempName());
        $data = json_decode($content, true);
        
        if (!$data || !is_array($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Format file backup tidak valid'
            ]);
        }
        
        try {
            // Kosongkan tabel dulu
            $this->pengaturanModel->truncate();
            
            // Insert data backup
            $inserted = 0;
            foreach ($data as $setting) {
                // Hapus ID agar dibuat baru
                unset($setting['id']);
                if ($this->pengaturanModel->insert($setting)) {
                    $inserted++;
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Berhasil restore {$inserted} pengaturan dari backup",
                'count' => $inserted
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat restore: ' . $e->getMessage()
            ]);
        }
    }
    
    public function initialize()
    {
        try {
            $inserted = $this->pengaturanModel->initializeDefaultSettings();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Berhasil menginisialisasi {$inserted} pengaturan default",
                'count' => $inserted
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    public function getCategories()
    {
        $categories = $this->pengaturanModel->getCategories();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $categories
        ]);
    }
    
    public function getFieldTypes()
    {
        $types = $this->pengaturanModel->getFieldTypes();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $types
        ]);
    }
    public function getSettingsByCategory()
{
    $category = $this->request->getGet('category');
    $model = new PengaturanModel();
    
    if ($category && $category !== 'all') {
        $model->where('category', $category);
    }
    
    $settings = $model->orderBy('order', 'ASC')->findAll();
    
    $formattedData = [];
    foreach ($settings as $row) {
        $formattedData[] = [
            'id' => $row['id'],
            'key_name' => $row['key_name'],
            'label' => $row['label'],
            'value' => $this->formatValue($row['value'], $row['type']),
            'type' => ucfirst($row['type']),
            'category' => ucfirst($row['category']),
            'status' => $row['is_active'] == 1 
                ? '<span class="badge bg-success">Aktif</span>' 
                : '<span class="badge bg-danger">Nonaktif</span>',
            'actions' => $this->getActionButtons($row)
        ];
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $formattedData
    ]);
}
}