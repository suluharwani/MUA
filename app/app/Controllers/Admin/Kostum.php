<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Kostum extends BaseController
{
    protected $kostumModel;
    
    public function __construct()
    {
        $this->kostumModel = new \App\Models\KostumModel();
    }
    
    public function index()
    {
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        
        $data = [
            'title' => 'Kelola Kostum',
            'kostum' => $this->kostumModel->getAllWithFilter($kategori, $status, null, $search),
            'stats' => $this->kostumModel->getStatistics(),
            'kategori_filter' => $kategori,
            'status_filter' => $status,
            'search_term' => $search,
            'kategori_options' => $this->kostumModel->getKategoriOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/kostum/index', $data);
    }
    
    public function tambah()
    {
        $data = [
            'title' => 'Tambah Kostum Baru',
            'kategori_options' => $this->kostumModel->getKategoriOptions(),
            'ukuran_options' => $this->kostumModel->getUkuranOptions(),
            'kondisi_options' => $this->kostumModel->getKondisiOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/kostum/tambah', $data);
    }
    
    public function simpan()
    {
        // Validation rules
        $validationRules = [
            'nama_kostum' => 'required|min_length[3]',
            'kategori' => 'required',
            'harga_sewa' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => [
                'uploaded[gambar]',
                'mime_in[gambar,image/jpg,image/jpeg,image/png]',
                'max_size[gambar,2048]'
            ]
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'nama_kostum' => $this->request->getPost('nama_kostum'),
            'kategori' => $this->request->getPost('kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga_sewa' => $this->request->getPost('harga_sewa'),
            'durasi_sewa' => $this->request->getPost('durasi_sewa'),
            'spesifikasi' => explode("\n", $this->request->getPost('spesifikasi')),
            'ukuran' => $this->request->getPost('ukuran'),
            'warna' => $this->request->getPost('warna'),
            'bahan' => $this->request->getPost('bahan'),
            'kondisi' => $this->request->getPost('kondisi'),
            'stok' => $this->request->getPost('stok'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'urutan' => $this->request->getPost('urutan'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'meta_description' => $this->request->getPost('meta_description')
        ];
        
        if ($this->kostumModel->save($data)) {
            return redirect()->to('/admin/kostum')->with('success', 'Kostum berhasil ditambahkan.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kostum.');
    }
    
    public function edit($id)
    {
        $kostum = $this->kostumModel->find($id);
        
        if (!$kostum) {
            return redirect()->to('/admin/kostum')->with('error', 'Kostum tidak ditemukan.');
        }
        
        // Decode spesifikasi dan gambar tambahan
        $kostum['spesifikasi'] = json_decode($kostum['spesifikasi'] ?? '[]', true);
        $kostum['gambar_tambahan'] = json_decode($kostum['gambar_tambahan'] ?? '[]', true);
        
        $data = [
            'title' => 'Edit Kostum',
            'kostum' => $kostum,
            'kategori_options' => $this->kostumModel->getKategoriOptions(),
            'ukuran_options' => $this->kostumModel->getUkuranOptions(),
            'kondisi_options' => $this->kostumModel->getKondisiOptions(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/kostum/edit', $data);
    }
    
    public function update($id)
    {
        $kostum = $this->kostumModel->find($id);
        
        if (!$kostum) {
            return redirect()->to('/admin/kostum')->with('error', 'Kostum tidak ditemukan.');
        }
        
        // Validation rules
        $validationRules = [
            'nama_kostum' => 'required|min_length[3]',
            'kategori' => 'required',
            'harga_sewa' => 'required|numeric',
            'stok' => 'required|integer'
        ];
        
        // Gambar tidak wajib di update
        if ($this->request->getFile('gambar')->isValid()) {
            $validationRules['gambar'] = [
                'uploaded[gambar]',
                'mime_in[gambar,image/jpg,image/jpeg,image/png]',
                'max_size[gambar,2048]'
            ];
        }
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'nama_kostum' => $this->request->getPost('nama_kostum'),
            'kategori' => $this->request->getPost('kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga_sewa' => $this->request->getPost('harga_sewa'),
            'durasi_sewa' => $this->request->getPost('durasi_sewa'),
            'spesifikasi' => explode("\n", $this->request->getPost('spesifikasi')),
            'ukuran' => $this->request->getPost('ukuran'),
            'warna' => $this->request->getPost('warna'),
            'bahan' => $this->request->getPost('bahan'),
            'kondisi' => $this->request->getPost('kondisi'),
            'stok' => $this->request->getPost('stok'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'urutan' => $this->request->getPost('urutan'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'meta_description' => $this->request->getPost('meta_description')
        ];
        
        if ($this->kostumModel->save($data)) {
            return redirect()->to('/admin/kostum')->with('success', 'Kostum berhasil diperbarui.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kostum.');
    }
    
    public function hapus($id)
    {
        $kostum = $this->kostumModel->find($id);
        
        if (!$kostum) {
            return redirect()->to('/admin/kostum')->with('error', 'Kostum tidak ditemukan.');
        }
        
        // Hapus gambar utama
        if (!empty($kostum['gambar'])) {
            $gambarPath = WRITEPATH . 'uploads/kostum/' . $kostum['gambar'];
            if (file_exists($gambarPath)) {
                unlink($gambarPath);
            }
        }
        
        // Hapus gambar tambahan
        if (!empty($kostum['gambar_tambahan'])) {
            $gambarTambahan = json_decode($kostum['gambar_tambahan'], true) ?? [];
            foreach ($gambarTambahan as $gambar) {
                $gambarPath = WRITEPATH . 'uploads/kostum/tambahan/' . $gambar;
                if (file_exists($gambarPath)) {
                    unlink($gambarPath);
                }
            }
        }
        
        if ($this->kostumModel->delete($id)) {
            return redirect()->to('/admin/kostum')->with('success', 'Kostum berhasil dihapus.');
        }
        
        return redirect()->to('/admin/kostum')->with('error', 'Gagal menghapus kostum.');
    }
    
    public function hapusGambarTambahan($id, $gambar)
    {
        if ($this->kostumModel->deleteAdditionalImage($id, $gambar)) {
            return redirect()->back()->with('success', 'Gambar berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'Gagal menghapus gambar.');
    }
    
    public function toggleStatus($id)
    {
        if ($this->kostumModel->toggleStatus($id)) {
            return redirect()->back()->with('success', 'Status kostum berhasil diubah.');
        }
        
        return redirect()->back()->with('error', 'Gagal mengubah status kostum.');
    }
    
    public function toggleFeatured($id)
    {
        if ($this->kostumModel->toggleFeatured($id)) {
            return redirect()->back()->with('success', 'Status featured berhasil diubah.');
        }
        
        return redirect()->back()->with('error', 'Gagal mengubah status featured.');
    }
    
    public function updateStok($id)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'stok' => 'required|integer|greater_than_equal_to[0]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }
        
        $newStok = $this->request->getPost('stok');
        
        if ($this->kostumModel->updateStok($id, $newStok)) {
            return redirect()->back()->with('success', 'Stok berhasil diperbarui.');
        }
        
        return redirect()->back()->with('error', 'Gagal memperbarui stok.');
    }
    
    public function import()
    {
        $data = [
            'title' => 'Import Kostum dari CSV'
        ];
        
        return view('admin/kostum/import', $data);
    }
    
    public function prosesImport()
    {
        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }
        
        // Validasi ekstensi file
        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'File harus berformat CSV.');
        }
        
        // Pindahkan file ke temp
        $tempPath = WRITEPATH . 'uploads/temp/' . $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/temp/', $file->getName());
        
        // Proses import
        $result = $this->kostumModel->importFromCSV($tempPath);
        
        if ($result['success']) {
            $message = "Import berhasil: {$result['imported']} data berhasil diimport";
            if ($result['failed'] > 0) {
                $message .= ", {$result['failed']} data gagal";
            }
            
            session()->setFlashdata('import_result', $result);
            return redirect()->to('/admin/kostum')->with('success', $message);
        }
        
        return redirect()->back()->with('error', 'Gagal mengimport data.');
    }
    
    public function export()
    {
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        
        $filters = [];
        if ($kategori) {
            $filters['kategori'] = $kategori;
        }
        if ($status !== null) {
            $filters['is_active'] = (int)$status;
        }
        
        $filePath = $this->kostumModel->exportToCSV($filters);
        
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null);
        }
        
        return redirect()->back()->with('error', 'Gagal mengeksport data.');
    }
    
    public function view($id)
    {
        $kostum = $this->kostumModel->find($id);
        
        if (!$kostum) {
            return redirect()->to('/admin/kostum')->with('error', 'Kostum tidak ditemukan.');
        }
        
        // Decode data
        $kostum['spesifikasi'] = json_decode($kostum['spesifikasi'] ?? '[]', true);
        $kostum['gambar_tambahan'] = json_decode($kostum['gambar_tambahan'] ?? '[]', true);
        
        $data = [
            'title' => 'Detail Kostum: ' . $kostum['nama_kostum'],
            'kostum' => $kostum,
            'kategori_options' => $this->kostumModel->getKategoriOptions()
        ];
        
        return view('admin/kostum/view', $data);
    }
    
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada kostum yang dipilih.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($ids as $id) {
            switch ($action) {
                case 'activate':
                    if ($this->kostumModel->update($id, ['is_active' => 1])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
                    
                case 'deactivate':
                    if ($this->kostumModel->update($id, ['is_active' => 0])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
                    
                case 'feature':
                    if ($this->kostumModel->update($id, ['is_featured' => 1])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
                    
                case 'unfeature':
                    if ($this->kostumModel->update($id, ['is_featured' => 0])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
                    
                case 'delete':
                    if ($this->kostumModel->delete($id)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
            }
        }
        
        $message = "Aksi berhasil: {$successCount} kostum diproses";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} gagal";
        }
        
        return redirect()->back()->with('success', $message);
    }
}