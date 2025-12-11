<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;
    
    public function __construct()
    {
        $this->pengaturanModel = new \App\Models\PengaturanModel();
    }
    
    public function index()
    {
        $category = $this->request->getGet('category') ?? 'general';
        
        $data = [
            'title' => 'Pengaturan Website',
            'settings' => $this->pengaturanModel->getByCategory($category),
            'categories' => $this->pengaturanModel->getCategories(),
            'current_category' => $category,
            'field_types' => $this->pengaturanModel->getFieldTypes(),
            'system_info' => $this->pengaturanModel->getSystemInfo(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/pengaturan/index', $data);
    }
    
    public function simpan()
    {
        $data = $this->request->getPost();
        
        // Hapus token CSRF
        unset($data['csrf_test_name']);
        
        if ($this->pengaturanModel->updateMultiple($data)) {
            return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
        }
        
        return redirect()->back()->with('error', 'Gagal menyimpan pengaturan.');
    }
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Pengaturan Baru',
            'categories' => $this->pengaturanModel->getCategories(),
            'field_types' => $this->pengaturanModel->getFieldTypes(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/pengaturan/tambah', $data);
    }
    
    public function simpanBaru()
    {
        $validationRules = [
            'key_name' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[pengaturan.key_name]',
            'label' => 'required|min_length[3]|max_length[100]',
            'type' => 'required',
            'category' => 'required'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'key_name' => $this->request->getPost('key_name'),
            'label' => $this->request->getPost('label'),
            'type' => $this->request->getPost('type'),
            'category' => $this->request->getPost('category'),
            'value' => $this->request->getPost('value'),
            'options' => $this->request->getPost('options'),
            'placeholder' => $this->request->getPost('placeholder'),
            'required' => $this->request->getPost('required') ? 1 : 0,
            'order' => $this->request->getPost('order') ?? 0,
            'is_active' => 1
        ];
        
        if ($this->pengaturanModel->save($data)) {
            return redirect()->to('/admin/pengaturan')->with('success', 'Pengaturan berhasil ditambahkan.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pengaturan.');
    }
    
    public function edit($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/pengaturan')->with('error', 'Pengaturan tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Pengaturan',
            'setting' => $setting,
            'categories' => $this->pengaturanModel->getCategories(),
            'field_types' => $this->pengaturanModel->getFieldTypes(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/pengaturan/edit', $data);
    }
    
    public function update($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/pengaturan')->with('error', 'Pengaturan tidak ditemukan.');
        }
        
        $validationRules = [
            'key_name' => "required|alpha_dash|min_length[3]|max_length[50]|is_unique[pengaturan.key_name,id,{$id}]",
            'label' => 'required|min_length[3]|max_length[100]',
            'type' => 'required',
            'category' => 'required'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'key_name' => $this->request->getPost('key_name'),
            'label' => $this->request->getPost('label'),
            'type' => $this->request->getPost('type'),
            'category' => $this->request->getPost('category'),
            'value' => $this->request->getPost('value'),
            'options' => $this->request->getPost('options'),
            'placeholder' => $this->request->getPost('placeholder'),
            'required' => $this->request->getPost('required') ? 1 : 0,
            'order' => $this->request->getPost('order') ?? 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($this->pengaturanModel->save($data)) {
            return redirect()->to('/admin/pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengaturan.');
    }
    
    public function hapus($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/pengaturan')->with('error', 'Pengaturan tidak ditemukan.');
        }
        
        // Jangan hapus pengaturan sistem penting
        $systemKeys = ['nama_toko', 'whatsapp', 'alamat', 'email'];
        if (in_array($setting['key_name'], $systemKeys)) {
            return redirect()->to('/admin/pengaturan')->with('error', 'Pengaturan sistem tidak dapat dihapus.');
        }
        
        if ($this->pengaturanModel->delete($id)) {
            return redirect()->to('/admin/pengaturan')->with('success', 'Pengaturan berhasil dihapus.');
        }
        
        return redirect()->to('/admin/pengaturan')->with('error', 'Gagal menghapus pengaturan.');
    }
    
    public function toggleStatus($id)
    {
        $setting = $this->pengaturanModel->find($id);
        
        if (!$setting) {
            return redirect()->to('/admin/pengaturan')->with('error', 'Pengaturan tidak ditemukan.');
        }
        
        $newStatus = $setting['is_active'] ? 0 : 1;
        
        if ($this->pengaturanModel->update($id, ['is_active' => $newStatus])) {
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Pengaturan berhasil {$statusText}.");
        }
        
        return redirect()->back()->with('error', 'Gagal mengubah status pengaturan.');
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
            return redirect()->back()->with('error', 'File tidak valid.');
        }
        
        if ($file->getExtension() !== 'json') {
            return redirect()->back()->with('error', 'File harus berformat JSON.');
        }
        
        $content = file_get_contents($file->getTempName());
        $data = json_decode($content, true);
        
        if (!$data || !is_array($data)) {
            return redirect()->back()->with('error', 'Format file backup tidak valid.');
        }
        
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
        
        return redirect()->to('/admin/pengaturan')->with('success', "Berhasil restore {$inserted} pengaturan dari backup.");
    }
    
    public function initialize()
    {
        $inserted = $this->pengaturanModel->initializeDefaultSettings();
        
        return redirect()->to('/admin/pengaturan')->with('success', "Berhasil menginisialisasi {$inserted} pengaturan default.");
    }
}