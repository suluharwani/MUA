<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KostumModel;

class Kostum extends BaseController
{
    protected $kostumModel;
    protected $kategoriOptions = [];
    protected $kondisiOptions = [];

    public function __construct()
    {
        $this->kostumModel = new KostumModel();
        $this->kategoriOptions = $this->kostumModel->getKategoriOptions();
        $this->kondisiOptions = $this->kostumModel->getKondisiOptions();
        helper(['form', 'url', 'text']);
    }

    public function index()
    {
        // Get filters from GET parameters
        $search = $this->request->getGet('search');
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        
        // Get paginated data
        $perPage = 10;
        $currentPage = $this->request->getGet('page') ?? 1;
        $offset = ($currentPage - 1) * $perPage;
        
        $kostum = $this->kostumModel->getFiltered($search, $kategori, $status, $perPage, $offset);
        $totalRows = $this->kostumModel->countFiltered($search, $kategori, $status);
        
        // Statistics
        $stats = $this->kostumModel->getStatistics();
        
        $data = [
            'title' => 'Kelola Kostum',
            'kostum' => $kostum,
            'stats' => $stats,
            'kategori_options' => $this->kategoriOptions,
            'search_term' => $search,
            'kategori_filter' => $kategori,
            'status_filter' => $status,
            'currentPage' => $currentPage,
            'totalRows' => $totalRows,
            'perPage' => $perPage,
            'pager' => $this->kostumModel->pager
        ];
        
        return view('admin/kostum/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Kostum Baru',
            'kategori_options' => $this->kategoriOptions,
            'kondisi_options' => $this->kondisiOptions,
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/kostum/tambah', $data);
    }

    public function simpan()
{
    // Validation rules - HAPUS validasi gambar wajib untuk tambah
    $rules = [
        'nama_kostum' => 'required|min_length[3]|max_length[100]',
        'kategori' => 'required',
        'harga_sewa' => 'required|numeric',
        'stok' => 'required|integer',
        'gambar' => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    try {
        // Handle file upload - gambar tidak wajib
        $gambarName = null;
        $gambarFile = $this->request->getFile('gambar');
        
        if ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            $gambarName = $gambarFile->getRandomName();
            $gambarFile->move(ROOTPATH . 'public/uploads/kostum', $gambarName);
        }

        // Format harga dengan benar
        $hargaSewa = $this->request->getPost('harga_sewa');
        $hargaSewa = str_replace(['.', ','], '', $hargaSewa);
        $hargaSewa = (float) $hargaSewa;

        // Format spesifikasi dengan benar (pastikan JSON string)
        $spesifikasi = $this->request->getPost('spesifikasi');
        $spesifikasiJson = null;
        
        if (!empty($spesifikasi)) {
            // Split by new line and trim
            $lines = explode("\n", $spesifikasi);
            $cleanedLines = array_filter(array_map('trim', $lines));
            
            // Convert to JSON string
            if (!empty($cleanedLines)) {
                $spesifikasiJson = json_encode($cleanedLines, JSON_UNESCAPED_UNICODE);
            }
        }

        // Prepare data - SEMUA dalam format string/numeric
        $data = [
            'kategori' => $this->request->getPost('kategori'),
            'nama_kostum' => $this->request->getPost('nama_kostum'),
            'deskripsi' => $this->request->getPost('deskripsi') ?: '',
            'harga_sewa' => $hargaSewa,
            'durasi_sewa' => $this->request->getPost('durasi_sewa') ?: '3 hari',
            'spesifikasi' => $spesifikasiJson, // JSON string atau null
            'gambar' => $gambarName,
            'ukuran' => $this->request->getPost('ukuran') ?: '',
            'warna' => $this->request->getPost('warna') ?: '',
            'bahan' => $this->request->getPost('bahan') ?: '',
            'kondisi' => $this->request->getPost('kondisi') ?: 'baik',
            'stok' => (int) $this->request->getPost('stok'),
            'stok_tersedia' => (int) ($this->request->getPost('stok_tersedia') ?: $this->request->getPost('stok')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'urutan' => (int) ($this->request->getPost('urutan') ?: 0)
        ];

        // Handle custom duration
        if ($this->request->getPost('durasi_sewa') === 'Kustom' && $this->request->getPost('durasi_kustom')) {
            $data['durasi_sewa'] = $this->request->getPost('durasi_kustom');
        }

        // Debug data sebelum save
        // var_dump($data); exit;

        // Save using the model
        if ($this->kostumModel->insert($data)) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('success', 'Kostum berhasil ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kostum');
        }
    } catch (\Exception $e) {
        // Delete uploaded file if error
        if (isset($gambarName) && file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
            unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
        }
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function view($id)
    {
        $kostum = $this->kostumModel->getKostumById($id);
        
        if (!$kostum) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('error', 'Kostum tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Kostum',
            'kostum' => $kostum,
            'kategori_options' => $this->kategoriOptions,
            'kondisi_options' => $this->kondisiOptions
        ];

        return view('admin/kostum/view', $data);
    }

    public function edit($id)
    {
        $kostum = $this->kostumModel->getKostumById($id);
        
        if (!$kostum) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('error', 'Kostum tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Kostum',
            'kostum' => $kostum,
            'kategori_options' => $this->kategoriOptions,
            'kondisi_options' => $this->kondisiOptions,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/kostum/edit', $data);
    }

    public function update($id)
{
    // Check if kostum exists
    $existing = $this->kostumModel->find($id);
    if (!$existing) {
        return redirect()->to(base_url('admin/kostum'))
            ->with('error', 'Kostum tidak ditemukan');
    }

    // Validation rules - gambar tidak wajib
    $rules = [
        'nama_kostum' => 'required|min_length[3]|max_length[100]',
        'kategori' => 'required',
        'harga_sewa' => 'required|numeric',
        'stok' => 'required|integer',
        'gambar' => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    try {
        // Handle file upload
        $gambarName = $existing['gambar'];
        $gambarFile = $this->request->getFile('gambar');
        
        // Check if delete existing image
        $hapusGambar = $this->request->getPost('hapus_gambar');
        
        if ($hapusGambar && $gambarName) {
            // Delete existing image
            if (file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
            }
            $gambarName = null;
        } elseif ($gambarFile && $gambarFile->isValid() && !$gambarFile->hasMoved()) {
            // Upload new image
            $newGambarName = $gambarFile->getRandomName();
            $gambarFile->move(ROOTPATH . 'public/uploads/kostum', $newGambarName);
            
            // Delete old image if exists
            if ($gambarName && file_exists(ROOTPATH . 'public/uploads/kostum/' . $gambarName)) {
                unlink(ROOTPATH . 'public/uploads/kostum/' . $gambarName);
            }
            
            $gambarName = $newGambarName;
        }

        // Format harga dengan benar
        $hargaSewa = $this->request->getPost('harga_sewa');
        $hargaSewa = str_replace(['.', ','], '', $hargaSewa);
        $hargaSewa = (float) $hargaSewa;

        // Format spesifikasi dengan benar (pastikan JSON string)
        $spesifikasi = $this->request->getPost('spesifikasi');
        $spesifikasiJson = null;
        
        if (!empty($spesifikasi)) {
            // Split by new line and trim
            $lines = explode("\n", $spesifikasi);
            $cleanedLines = array_filter(array_map('trim', $lines));
            
            // Convert to JSON string
            if (!empty($cleanedLines)) {
                $spesifikasiJson = json_encode($cleanedLines, JSON_UNESCAPED_UNICODE);
            }
        } elseif (isset($existing['spesifikasi'])) {
            // Keep existing spesifikasi if not changed
            $spesifikasiJson = $existing['spesifikasi'];
        }

        // Prepare data - SEMUA dalam format string/numeric
        $data = [
            'kategori' => $this->request->getPost('kategori'),
            'nama_kostum' => $this->request->getPost('nama_kostum'),
            'deskripsi' => $this->request->getPost('deskripsi') ?: '',
            'harga_sewa' => $hargaSewa,
            'durasi_sewa' => $this->request->getPost('durasi_sewa') ?: '3 hari',
            'spesifikasi' => $spesifikasiJson, // JSON string atau null
            'gambar' => $gambarName,
            'ukuran' => $this->request->getPost('ukuran') ?: '',
            'warna' => $this->request->getPost('warna') ?: '',
            'bahan' => $this->request->getPost('bahan') ?: '',
            'kondisi' => $this->request->getPost('kondisi') ?: 'baik',
            'stok' => (int) $this->request->getPost('stok'),
            'stok_tersedia' => (int) ($this->request->getPost('stok_tersedia') ?: $this->request->getPost('stok')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'urutan' => (int) ($this->request->getPost('urutan') ?: 0)
        ];

        // Handle custom duration
        if ($this->request->getPost('durasi_sewa') === 'Kustom' && $this->request->getPost('durasi_kustom')) {
            $data['durasi_sewa'] = $this->request->getPost('durasi_kustom');
        }

        // Debug data sebelum update
        // var_dump($data); exit;

        // Update using the model
        if ($this->kostumModel->update($id, $data)) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('success', 'Kostum berhasil diperbarui');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kostum');
        }
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function hapus($id)
    {
        // Get existing data to delete images
        $existing = $this->kostumModel->find($id);
        
        if (!$existing) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('error', 'Kostum tidak ditemukan');
        }

        try {
            // Delete images if exists
            if (!empty($existing['gambar'])) {
                $this->deleteImageFiles($existing['gambar']);
            }
            
            // Delete from database
            if ($this->kostumModel->delete($id)) {
                return redirect()->to(base_url('admin/kostum'))
                    ->with('success', 'Kostum berhasil dihapus');
            } else {
                return redirect()->to(base_url('admin/kostum'))
                    ->with('error', 'Gagal menghapus kostum dari database');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        if ($this->kostumModel->toggleStatus($id)) {
            return redirect()->back()
                ->with('success', 'Status berhasil diubah');
        }
        
        return redirect()->back()
            ->with('error', 'Gagal mengubah status');
    }

    public function toggleFeatured($id)
    {
        if ($this->kostumModel->toggleFeatured($id)) {
            return redirect()->back()
                ->with('success', 'Status featured berhasil diubah');
        }
        
        return redirect()->back()
            ->with('error', 'Gagal mengubah status featured');
    }

    public function bulkAction()
    {
        $ids = $this->request->getPost('ids');
        $action = $this->request->getPost('action');
        
        if (empty($ids) || empty($action)) {
            return redirect()->back()
                ->with('error', 'Pilih aksi dan kostum terlebih dahulu');
        }
        
        if ($this->kostumModel->bulkActionSimple($ids, $action)) {
            return redirect()->back()
                ->with('success', 'Aksi berhasil diterapkan');
        }
        
        return redirect()->back()
            ->with('error', 'Gagal menerapkan aksi');
    }

    public function export()
    {
        try {
            // Get all kostum
            $kostum = $this->kostumModel->findAll();
            
            // Prepare CSV
            $filename = 'kostum_export_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($output, "\xEF\xBB\xBF");
            
            // Header
            fputcsv($output, [
                'Kategori', 
                'Nama Kostum', 
                'Deskripsi',
                'Harga Sewa',
                'Durasi Sewa',
                'Spesifikasi',
                'Ukuran',
                'Warna',
                'Bahan',
                'Kondisi',
                'Stok',
                'Stok Tersedia',
                'Status',
                'Featured'
            ], ';');
            
            // Data
            foreach ($kostum as $row) {
                // Decode spesifikasi
                $spesifikasi = '';
                if (!empty($row['spesifikasi'])) {
                    $specArray = json_decode($row['spesifikasi'], true);
                    if (is_array($specArray)) {
                        $spesifikasi = implode('|', $specArray);
                    }
                }
                
                fputcsv($output, [
                    $row['kategori'],
                    $row['nama_kostum'],
                    $row['deskripsi'],
                    $row['harga_sewa'],
                    $row['durasi_sewa'],
                    $spesifikasi,
                    $row['ukuran'],
                    $row['warna'],
                    $row['bahan'],
                    $row['kondisi'],
                    $row['stok'],
                    $row['stok_tersedia'],
                    $row['is_active'] ? 'Aktif' : 'Nonaktif',
                    $row['is_featured'] ? 'Ya' : 'Tidak'
                ], ';');
            }
            
            fclose($output);
            exit();
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/kostum'))
                ->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    public function import()
    {
        $data = [
            'title' => 'Import Kostum',
            'kategori_options' => $this->kategoriOptions,
            'kondisi_options' => $this->kondisiOptions
        ];
        
        return view('admin/kostum/import', $data);
    }

    public function processImport()
    {
        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File tidak valid');
        }
        
        if ($file->getExtension() !== 'csv') {
            return redirect()->back()
                ->with('error', 'Hanya file CSV yang diperbolehkan');
        }
        
        try {
            // Read CSV file
            $csvData = array_map('str_getcsv', file($file->getTempName()));
            $headers = array_map('trim', $csvData[0]);
            
            // Convert to associative array
            $rows = [];
            for ($i = 1; $i < count($csvData); $i++) {
                if (count($csvData[$i]) === count($headers)) {
                    $rows[] = array_combine($headers, $csvData[$i]);
                }
            }
            
            // Process import
            $result = $this->kostumModel->importCSVSimple($rows);
            
            $message = "Import selesai. Berhasil: {$result['success']}, Gagal: {$result['error']}";
            
            if ($result['success'] > 0) {
                return redirect()->to(base_url('admin/kostum'))
                    ->with('success', $message);
            } else {
                return redirect()->back()
                    ->with('error', $message)
                    ->with('import_errors', $result['errors']);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Create thumbnail for uploaded image
     */
    private function createThumbnail($filename)
    {
        $imagePath = ROOTPATH . 'public/uploads/kostum/' . $filename;
        $thumbPath = ROOTPATH . 'public/uploads/kostum/thumb/' . $filename;
        
        if (!file_exists($imagePath)) {
            return false;
        }
        
        // Create thumb directory if not exists
        if (!is_dir(dirname($thumbPath))) {
            mkdir(dirname($thumbPath), 0777, true);
        }
        
        // Use CodeIgniter's image manipulation class
        $image = \Config\Services::image()
            ->withFile($imagePath)
            ->fit(300, 300, 'center')
            ->save($thumbPath);
            
        return $image;
    }

    /**
     * Delete image files (original and thumbnail)
     */
    private function deleteImageFiles($filename)
    {
        $originalPath = ROOTPATH . 'public/uploads/kostum/' . $filename;
        $thumbPath = ROOTPATH . 'public/uploads/kostum/thumb/' . $filename;
        
        if (file_exists($originalPath)) {
            unlink($originalPath);
        }
        
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }
}