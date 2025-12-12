<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PengaturanAjax extends BaseController
{
    protected $pengaturanModel;
    
    public function __construct()
    {
        $this->pengaturanModel = new \App\Models\PengaturanModel();
        helper(['form', 'text']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Website (AJAX)',
            'categories' => $this->pengaturanModel->getCategories(),
            'field_types' => $this->pengaturanModel->getFieldTypes(),
            'system_info' => $this->pengaturanModel->getSystemInfo()
        ];
        
        return view('admin/pengaturan/ajax_index', $data);
    }
    
public function getSettings()
{
    $category = $this->request->getGet('category') ?? 'general';
    $draw = $this->request->getGet('draw');
    $start = $this->request->getGet('start') ?? 0;
    $length = $this->request->getGet('length') ?? 10;
    $searchValue = $this->request->getGet('search')['value'] ?? '';
    
    // Convert to integers
    $start = (int)$start;
    $length = (int)$length;
    
    // Query data
    $builder = $this->pengaturanModel->where('category', $category);
    
    // Total records
    $totalRecords = $this->pengaturanModel->where('category', $category)->countAllResults();
    
    // Filtered records
    if (!empty($searchValue)) {
        $builder->groupStart()
               ->like('key_name', $searchValue)
               ->orLike('label', $searchValue)
               ->orLike('value', $searchValue)
               ->groupEnd();
    }
    
    $filteredRecords = $builder->countAllResults();
    
    // Get data with ordering
    $orderColumn = $this->request->getGet('order')[0]['column'] ?? 0;
    $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
    $orderColumnName = $this->request->getGet('columns')[$orderColumn]['data'] ?? 'id';
    
    // Map column names to database columns
    $columnMap = [
        'id' => 'id',
        'key_name' => 'key_name',
        'label' => 'label',
        'value' => 'value',
        'type' => 'type',
        'category' => 'category',
        'status' => 'is_active'
    ];
    
    $dbColumn = $columnMap[$orderColumnName] ?? 'id';
    
    $builder->orderBy($dbColumn, $orderDir);
    
    // Apply limit and offset
    if ($length != -1 && $length > 0) {
        $builder->limit($length, $start);
    }
    
    $settings = $builder->findAll();
    
    // Format data for DataTables
    $data = [];
    foreach ($settings as $setting) {
        $data[] = [
            'id' => $setting['id'],
            'key_name' => '<code>' . $setting['key_name'] . '</code>',
            'label' => '<strong>' . $setting['label'] . '</strong>' . 
                      ($setting['required'] ? ' <span class="text-danger">*</span>' : '') .
                      ($setting['placeholder'] ? '<br><small class="text-muted">' . $setting['placeholder'] . '</small>' : ''),
            'value' => $this->formatValue($setting),
            'type' => '<span class="badge bg-secondary">' . $setting['type'] . '</span>',
            'category' => '<span class="badge bg-info">' . ucfirst($setting['category']) . '</span>',
            'status' => $this->getStatusBadge($setting['is_active']),
            'actions' => $this->getActionButtons($setting)
        ];
    }
    $this->response->setHeader('Content-Type', 'application/json');
    $this->response->setHeader('Access-Control-Allow-Origin', '*');
    return $this->response->setJSON([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);
}
    
    private function formatValue($setting)
    {
        $value = $setting['value'];
        
        if (empty($value)) {
            return '<span class="text-muted fst-italic">(kosong)</span>';
        }
        
        $type = $setting['type'];
        
        switch ($type) {
            case 'password':
                return '<span class="badge bg-dark"><i class="bi bi-key"></i> Password tersimpan</span>';
                
            case 'color':
                return '<div class="d-flex align-items-center">
                          <div class="color-preview me-2" style="width:20px;height:20px;background-color:' . $value . ';border-radius:3px;"></div>
                          <span>' . $value . '</span>
                        </div>';
                
            case 'select':
            case 'radio':
                if (!empty($setting['options'])) {
                    $options = $this->pengaturanModel->parseOptions($setting['options']);
                    return isset($options[$value]) ? $options[$value] : $value;
                }
                return $value;
                
            case 'checkbox':
                if (!empty($setting['options'])) {
                    $options = $this->pengaturanModel->parseOptions($setting['options']);
                    $selected = !empty($value) ? explode(',', $value) : [];
                    $labels = [];
                    foreach ($selected as $val) {
                        if (isset($options[$val])) {
                            $labels[] = $options[$val];
                        } else {
                            $labels[] = $val;
                        }
                    }
                    return !empty($labels) ? implode(', ', $labels) : '-';
                }
                return $value;
                
            case 'file':
                if (!empty($value)) {
                    $filename = basename($value);
                    return '<a href="' . base_url($value) . '" target="_blank" class="text-decoration-none">
                              <i class="bi bi-file-earmark me-1"></i>' . $filename . '
                            </a>';
                }
                return '-';
                
            default:
                if (strlen($value) > 50) {
                    return '<span title="' . esc($value) . '">' . esc(substr($value, 0, 50)) . '...</span>';
                }
                return esc($value);
        }
    }
    
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
        if (!in_array($setting['key_name'], $systemKeys)) {
            $buttons .= '<button type="button" class="btn btn-outline-danger delete-btn" data-id="' . $setting['id'] . '" data-name="' . esc($setting['label']) . '" title="Hapus">
                            <i class="bi bi-trash"></i>
                         </button>';
        } else {
            $buttons .= '<button type="button" class="btn btn-outline-secondary" disabled title="Pengaturan sistem tidak dapat dihapus">
                            <i class="bi bi-trash"></i>
                         </button>';
        }
        
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
    $id = $this->request->getPost('id');
    
    // Validasi manual untuk ID jika ada
    if (!empty($id)) {
        if (!is_numeric($id) || $id <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ]);
        }
    }
    
    $validationRules = [
        'key_name' => 'required|alpha_dash|min_length[3]|max_length[50]',
        'label' => 'required|min_length[3]|max_length[100]',
        'type' => 'required',
        'category' => 'required'
    ];
    
    // Unique check
    if (empty($id)) {
        $validationRules['key_name'] .= '|is_unique[pengaturan.key_name]';
    } else {
        $validationRules['key_name'] .= "|is_unique[pengaturan.key_name,id,{$id}]";
    }
    
    if (!$this->validate($validationRules)) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->validator->getErrors()
        ]);
    }
    $data = [
        'key_name' => $this->request->getPost('key_name'),
        'label' => $this->request->getPost('label'),
        'type' => $this->request->getPost('type'),
        'category' => $this->request->getPost('category'),
        'value' => $this->request->getPost('value') ?? '',
        'options' => $this->request->getPost('options') ?? '',
        'placeholder' => $this->request->getPost('placeholder') ?? '',
        'required' => $this->request->getPost('required') ? 1 : 0,
        'order' => $this->request->getPost('order') ?? 0,
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    // Jika ada ID, tambahkan untuk update
    if (!empty($id)) {
        $data['id'] = $id;
    }
    
    try {
        if ($this->pengaturanModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => empty($id) ? 'Pengaturan berhasil ditambahkan' : 'Pengaturan berhasil diperbarui',
                'id' => empty($id) ? $this->pengaturanModel->getInsertID() : $id
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan pengaturan'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
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
    
}