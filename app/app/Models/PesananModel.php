<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_pesanan', 'nama_lengkap', 'no_whatsapp', 'email', 'jenis_layanan',
        'paket_id', 'kostum_id', 'tanggal_acara', 'lokasi_acara', 'informasi_tambahan',
        'status', 'total_harga', 'dp_dibayar', 'metode_pembayaran', 'bukti_pembayaran',
        'tanggal_pelunasan', 'catatan_admin', 'area_id', 'biaya_transport',
        'diskon_persen', 'diskon_nominal', 'pajak_persen', 'pajak_nominal',
        'subtotal', 'total_akhir', 'dp_minimal', 'lama_sewa', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Generate kode pesanan
    public function generateKodePesanan()
{
    $prefix = 'INV';
    $year = date('y'); // 24 untuk 2024
    $month = date('m'); // 12 untuk Desember
    
    // Cari nomor urut terakhir untuk bulan ini
    $builder = $this->db->table($this->table);
    $builder->where('YEAR(created_at)', date('Y'));
    $builder->where('MONTH(created_at)', date('m'));
    $builder->orderBy('id', 'DESC');
    $lastOrder = $builder->get()->getRow();
    
    if ($lastOrder && !empty($lastOrder->kode_pesanan)) {
        // Extract number from existing code
        $lastCode = $lastOrder->kode_pesanan;
        // Pattern: MAULIA2412-0001
        if (preg_match('/-(\d+)$/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
    } else {
        $number = 1;
    }
    
    // Format: MAULIA2412-0001
    return $prefix . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
}

    // Get all data for DataTables
public function getDataTables($start = 0, $length = 10, $search = '', $status = '', $date = '', $layanan = '')
{
    $builder = $this->db->table('pesanan p');
    
    // Select hanya field yang dibutuhkan
    $builder->select('p.id, p.kode_pesanan, p.nama_lengkap, p.no_whatsapp, p.email, 
                     p.jenis_layanan, p.tanggal_acara, p.total_harga, p.total_akhir,
                     p.status, p.created_at, p.dp_dibayar, p.lokasi_acara');
    
    // Join jika perlu, tapi hati-hati dengan field yang ambigu
    $builder->select('pm.nama_paket, k.nama_kostum');
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Apply search filter
    if (!empty($search)) {
        $search = $this->db->escapeLikeString($search);
        $builder->groupStart();
        $builder->like('p.kode_pesanan', $search);
        $builder->orLike('p.nama_lengkap', $search);
        $builder->orLike('p.no_whatsapp', $search);
        $builder->orLike('p.email', $search);
        $builder->orLike('p.lokasi_acara', $search);
        $builder->groupEnd();
    }
    
    // Apply status filter
    if (!empty($status)) {
        $builder->where('p.status', $status);
    }
    
    // Apply date filter
    if (!empty($date)) {
        $builder->where('DATE(p.tanggal_acara)', $date);
    }
    
    // Apply layanan filter
    if (!empty($layanan)) {
        $builder->where('p.jenis_layanan', $layanan);
    }
    
    // Order by created_at DESC
    $builder->orderBy('p.created_at', 'DESC');
    
    // Apply limit and offset
    $builder->limit($length, $start);
    
    // Debug query
    // log_message('debug', 'SQL Query: ' . $builder->getCompiledSelect());
    
    $result = $builder->get();
    
    if (!$result) {
        log_message('error', 'Query failed: ' . $this->db->error());
        return [];
    }
    
    return $result->getResultArray();
}


public function countFiltered($search = '', $status = '', $date = '', $layanan = '')
{
    $builder = $this->db->table('pesanan p');
    
    // Apply search filter
    if (!empty($search)) {
        $search = $this->db->escapeLikeString($search);
        $builder->groupStart();
        $builder->like('p.kode_pesanan', $search);
        $builder->orLike('p.nama_lengkap', $search);
        $builder->orLike('p.no_whatsapp', $search);
        $builder->orLike('p.email', $search);
        $builder->orLike('p.lokasi_acara', $search);
        $builder->groupEnd();
    }
    
    // Apply status filter
    if (!empty($status)) {
        $builder->where('p.status', $status);
    }
    
    // Apply date filter
    if (!empty($date)) {
        $builder->where('DATE(p.tanggal_acara)', $date);
    }
    
    // Apply layanan filter
    if (!empty($layanan)) {
        $builder->where('p.jenis_layanan', $layanan);
    }
    
    return $builder->countAllResults();
}

public function getPesananWithRelations($id = null)
{
    $builder = $this->db->table('pesanan p');
    $builder->select('p.*, 
        pm.nama_paket as paket_nama, 
        pm.harga as paket_harga, 
        pm.deskripsi as paket_deskripsi,
        k.nama_kostum as kostum_nama, 
        k.harga_sewa as kostum_harga, 
        k.deskripsi as kostum_deskripsi, 
        k.kategori as kostum_kategori,
        a.nama_area, 
        a.biaya_tambahan, 
        a.keterangan as area_keterangan,
        u.nama as admin_nama'
    );
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    $builder->join('area_layanan a', 'a.id = p.area_id', 'left');
    $builder->join('users u', 'u.id = p.created_by', 'left');
    
    if ($id) {
        $builder->where('p.id', $id);
        $result = $builder->get()->getRowArray();
        
        // Set default values untuk field yang mungkin null
        if ($result) {
            $result['paket_nama'] = $result['paket_nama'] ?? null;
            $result['paket_harga'] = $result['paket_harga'] ?? 0;
            $result['paket_deskripsi'] = $result['paket_deskripsi'] ?? null;
            $result['kostum_nama'] = $result['kostum_nama'] ?? null;
            $result['kostum_harga'] = $result['kostum_harga'] ?? 0;
            $result['kostum_deskripsi'] = $result['kostum_deskripsi'] ?? null;
            $result['kostum_kategori'] = $result['kostum_kategori'] ?? null;
        }
        
        return $result;
    }
    
    $results = $builder->get()->getResultArray();
    
    // Set default values untuk semua hasil
    foreach ($results as &$result) {
        $result['paket_nama'] = $result['paket_nama'] ?? null;
        $result['paket_harga'] = $result['paket_harga'] ?? 0;
        $result['paket_deskripsi'] = $result['paket_deskripsi'] ?? null;
        $result['kostum_nama'] = $result['kostum_nama'] ?? null;
        $result['kostum_harga'] = $result['kostum_harga'] ?? 0;
        $result['kostum_deskripsi'] = $result['kostum_deskripsi'] ?? null;
        $result['kostum_kategori'] = $result['kostum_kategori'] ?? null;
    }
    
    return $results;
}

    // Calculate total price
// Update calculateTotalPrice method
public function calculateTotalPrice($data)
{
    $subtotal = 0;
    
    // Add package price
    if (!empty($data['paket_id'])) {
        $paketModel = new \App\Models\PaketMakeupModel();
        $paket = $paketModel->find($data['paket_id']);
        if ($paket && isset($paket['harga'])) {
            $subtotal += (float)$paket['harga'];
        }
    }
    
    // Add costume price
    if (!empty($data['kostum_id'])) {
        $kostumModel = new \App\Models\KostumModel();
        $kostum = $kostumModel->find($data['kostum_id']);
        if ($kostum && isset($kostum['harga_sewa'])) {
            $lamaSewa = $data['lama_sewa'] ?? 1;
            $subtotal += ((float)$kostum['harga_sewa'] * (int)$lamaSewa);
        }
    }
    
    // Add transport cost from area
    if (!empty($data['area_id'])) {
        $areaModel = new \App\Models\AreaLayananModel();
        $area = $areaModel->find($data['area_id']);
        if ($area && isset($area['biaya_tambahan'])) {
            $subtotal += (float)$area['biaya_tambahan'];
        }
    }
    
    // Add custom transport cost
    if (!empty($data['biaya_transport'])) {
        $subtotal += (float)$data['biaya_transport'];
    }
    
    // Calculate discount
    $diskonNominal = 0;
    if (!empty($data['diskon_persen']) && $data['diskon_persen'] > 0) {
        $diskonNominal = ($subtotal * (float)$data['diskon_persen'] / 100);
    } elseif (!empty($data['diskon_nominal']) && $data['diskon_nominal'] > 0) {
        $diskonNominal = (float)$data['diskon_nominal'];
    }
    
    $subtotalSetelahDiskon = $subtotal - $diskonNominal;
    
    // Calculate tax
    $pajakNominal = 0;
    if (!empty($data['pajak_persen']) && $data['pajak_persen'] > 0) {
        $pajakNominal = ($subtotalSetelahDiskon * (float)$data['pajak_persen'] / 100);
    } elseif (!empty($data['pajak_nominal']) && $data['pajak_nominal'] > 0) {
        $pajakNominal = (float)$data['pajak_nominal'];
    }
    
    $totalAkhir = $subtotalSetelahDiskon + $pajakNominal;
    
    return [
        'subtotal' => $subtotal,
        'diskon_nominal' => $diskonNominal,
        'subtotal_setelah_diskon' => $subtotalSetelahDiskon,
        'pajak_nominal' => $pajakNominal,
        'total_akhir' => $totalAkhir
    ];
}

    // Create new order
    public function createOrder($data)
{
    try {
        // Generate kode pesanan
        $kodePesanan = $this->generateKodePesanan();
        $data['kode_pesanan'] = $kodePesanan;
        
        // Set created by
        $session = \Config\Services::session();
        $data['created_by'] = $session->get('id') ?? 1;
        
        // Set default values
        $data['status'] = $data['status'] ?? 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Hitung harga jika diperlukan
        if (isset($data['paket_id']) || isset($data['kostum_id'])) {
            $calculated = $this->calculateTotalPrice($data);
            
            // Gabungkan hasil perhitungan
            foreach ($calculated as $key => $value) {
                $data[$key] = $value;
            }
            
            // Set total harga
            $data['total_harga'] = $data['total_akhir'] ?? $calculated['total_akhir'] ?? 0;
        }
        
        // Insert ke database
        if ($this->insert($data)) {
            $insertId = $this->db->insertID();
            
            return [
                'success' => true,
                'id' => $insertId,
                'kode_pesanan' => $kodePesanan
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal menyimpan pesanan ke database'
        ];
        
    } catch (\Exception $e) {
        log_message('error', 'Error creating order: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

    // Update order
    public function updateOrder($id, $data)
    {
        try {
            // Recalculate prices if needed
            $needsRecalculation = false;
            $recalculationFields = ['paket_id', 'kostum_id', 'area_id', 'biaya_transport', 
                                   'diskon_persen', 'diskon_nominal', 'pajak_persen', 
                                   'pajak_nominal', 'lama_sewa'];
            
            foreach ($recalculationFields as $field) {
                if (isset($data[$field])) {
                    $needsRecalculation = true;
                    break;
                }
            }
            
            if ($needsRecalculation) {
                // Get existing data
                $existing = $this->find($id);
                if ($existing) {
                    // Merge with new data
                    foreach ($data as $key => $value) {
                        $existing[$key] = $value;
                    }
                    
                    $calculated = $this->calculateTotalPrice($existing);
                    foreach ($calculated as $key => $value) {
                        $data[$key] = $value;
                    }
                    $data['total_harga'] = $data['total_akhir'];
                }
            }
            
            return $this->update($id, $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in updateOrder: ' . $e->getMessage());
            return false;
        }
    }

    // Get statistics
    public function getStatistics($search = null, $status = null, $date = null, $layanan = null, $dateRange = null)
    {
        $builder = $this->db->table('pesanan');
        
        if ($search) {
            $builder->groupStart();
            $builder->like('kode_pesanan', $search);
            $builder->orLike('nama_lengkap', $search);
            $builder->orLike('no_whatsapp', $search);
            $builder->groupEnd();
        }
        
        if ($status && $status !== '') {
            $builder->where('status', $status);
        }
        
        if ($date && $date !== '') {
            $builder->where('DATE(tanggal_acara)', $date);
        }
        
        if ($layanan && $layanan !== '') {
            $builder->where('jenis_layanan', $layanan);
        }
        
        if ($dateRange && $dateRange !== '') {
            list($startDate, $endDate) = explode(' to ', $dateRange);
            if ($startDate && $endDate) {
                $builder->where('DATE(tanggal_acara) >=', $startDate);
                $builder->where('DATE(tanggal_acara) <=', $endDate);
            }
        }
        
        $total = $builder->countAllResults();
        
        // Get total revenue
        $revenueBuilder = $this->db->table('pesanan');
        $revenueBuilder->selectSum('total_harga', 'total_revenue');
        
        // Apply same filters
        if ($search) {
            $revenueBuilder->groupStart();
            $revenueBuilder->like('kode_pesanan', $search);
            $revenueBuilder->orLike('nama_lengkap', $search);
            $revenueBuilder->orLike('no_whatsapp', $search);
            $revenueBuilder->groupEnd();
        }
        
        if ($status && $status !== '') {
            $revenueBuilder->where('status', $status);
        }
        
        if ($date && $date !== '') {
            $revenueBuilder->where('DATE(tanggal_acara)', $date);
        }
        
        if ($layanan && $layanan !== '') {
            $revenueBuilder->where('jenis_layanan', $layanan);
        }
        
        if ($dateRange && $dateRange !== '') {
            list($startDate, $endDate) = explode(' to ', $dateRange);
            if ($startDate && $endDate) {
                $revenueBuilder->where('DATE(tanggal_acara) >=', $startDate);
                $revenueBuilder->where('DATE(tanggal_acara) <=', $endDate);
            }
        }
        
        $totalRevenueResult = $revenueBuilder->get()->getRow();
        $totalRevenue = $totalRevenueResult ? (float)$totalRevenueResult->total_revenue : 0;
        
         $stats = [
        'total' => $total,
        'pending' => $this->countByStatus('pending', $search, $date, $layanan, $dateRange),
        'dikonfirmasi' => $this->countByStatus('dikonfirmasi', $search, $date, $layanan, $dateRange),
        'diproses' => $this->countByStatus('diproses', $search, $date, $layanan, $dateRange),
        'selesai' => $this->countByStatus('selesai', $search, $date, $layanan, $dateRange),
        'dibatalkan' => $this->countByStatus('dibatalkan', $search, $date, $layanan, $dateRange),
        'total_revenue' => $totalRevenue,  // PERHATIAN: ini 'total_revenue' bukan 'total_pendapatan'
        'avg_order_value' => $total > 0 ? ($totalRevenue / $total) : 0
    ];
        
        return $stats;
    }

    // Helper function to count by status
    private function countByStatus($status, $search = null, $date = null, $layanan = null, $dateRange = null)
    {
        $builder = $this->db->table('pesanan');
        $builder->where('status', $status);
        
        if ($search) {
            $builder->groupStart();
            $builder->like('kode_pesanan', $search);
            $builder->orLike('nama_lengkap', $search);
            $builder->orLike('no_whatsapp', $search);
            $builder->groupEnd();
        }
        
        if ($date && $date !== '') {
            $builder->where('DATE(tanggal_acara)', $date);
        }
        
        if ($layanan && $layanan !== '') {
            $builder->where('jenis_layanan', $layanan);
        }
        
        if ($dateRange && $dateRange !== '') {
            list($startDate, $endDate) = explode(' to ', $dateRange);
            if ($startDate && $endDate) {
                $builder->where('DATE(tanggal_acara) >=', $startDate);
                $builder->where('DATE(tanggal_acara) <=', $endDate);
            }
        }
        
        return $builder->countAllResults();
    }

    // Update status with note
    public function updateStatus($id, $status, $catatan = null)
    {
        $data = ['status' => $status];
        
        if ($catatan) {
            $data['catatan_admin'] = $catatan;
        }
        
        return $this->update($id, $data);
    }

    // Update payment
    public function updatePembayaran($id, $dp_dibayar, $total_harga, $metode, $bukti = null)
    {
        $data = [
            'dp_dibayar' => $dp_dibayar,
            'total_harga' => $total_harga,
            'metode_pembayaran' => $metode
        ];
        
        if ($bukti) {
            $data['bukti_pembayaran'] = $bukti;
        }
        
        return $this->update($id, $data);
    }

    // Get for export/report
    public function getForReport($search = null, $status = null, $date = null, $layanan = null)
    {
        $builder = $this->db->table('pesanan p');
        $builder->select('p.*, pm.nama_paket, pm.harga as paket_harga, k.nama_kostum, k.harga_sewa as kostum_harga');
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        
        if ($search) {
            $builder->groupStart();
            $builder->like('p.kode_pesanan', $search);
            $builder->orLike('p.nama_lengkap', $search);
            $builder->orLike('p.no_whatsapp', $search);
            $builder->groupEnd();
        }
        
        if ($status && $status !== '') {
            $builder->where('p.status', $status);
        }
        
        if ($date && $date !== '') {
            $builder->where('DATE(p.tanggal_acara)', $date);
        }
        
        if ($layanan && $layanan !== '') {
            $builder->where('p.jenis_layanan', $layanan);
        }
        
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    // Get by month for calendar
    public function getByMonth($year, $month)
    {
        $builder = $this->db->table('pesanan p');
        $builder->select('p.*, pm.nama_paket, k.nama_kostum');
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        $builder->where("YEAR(p.tanggal_acara) =", $year);
        $builder->where("MONTH(p.tanggal_acara) =", $month);
        $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
        $builder->orderBy('p.tanggal_acara', 'ASC');
        
        return $builder->get()->getResultArray();
    }
// Get monthly order count for chart/statistics
public function getMonthlyCount($year = null)
{
    $currentYear = $year ?? date('Y');
    
    $builder = $this->db->table('pesanan');
    
    // Select bulan dan jumlah pesanan
    $builder->select("
        MONTH(created_at) as bulan,
        COUNT(*) as jumlah_pesanan,
        SUM(total_harga) as total_pendapatan,
        SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as pesanan_selesai,
        SUM(CASE WHEN status = 'dibatalkan' THEN 1 ELSE 0 END) as pesanan_dibatalkan
    ");
    
    // Filter berdasarkan tahun
    $builder->where("YEAR(created_at)", $currentYear);
    
    // Group by bulan
    $builder->groupBy("MONTH(created_at)");
    
    // Order by bulan
    $builder->orderBy("MONTH(created_at)", "ASC");
    
    $result = $builder->get()->getResultArray();
    
    // Format hasil untuk semua bulan (1-12)
    $monthlyData = [];
    
    for ($i = 1; $i <= 12; $i++) {
        $monthlyData[$i] = [
            'bulan' => $i,
            'nama_bulan' => $this->getMonthName($i),
            'jumlah_pesanan' => 0,
            'total_pendapatan' => 0,
            'pesanan_selesai' => 0,
            'pesanan_dibatalkan' => 0
        ];
    }
    
    // Isi data yang ada
    foreach ($result as $row) {
        $bulan = (int)$row['bulan'];
        $monthlyData[$bulan] = [
            'bulan' => $bulan,
            'nama_bulan' => $this->getMonthName($bulan),
            'jumlah_pesanan' => (int)$row['jumlah_pesanan'],
            'total_pendapatan' => (float)$row['total_pendapatan'],
            'pesanan_selesai' => (int)$row['pesanan_selesai'],
            'pesanan_dibatalkan' => (int)$row['pesanan_dibatalkan']
        ];
    }
    
    return array_values($monthlyData);
}

// Helper method to get month name
private function getMonthName($monthNumber)
{
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    
    return $months[$monthNumber] ?? 'Bulan Tidak Dikenal';
}

// Get yearly summary (optional)
public function getYearlySummary($year = null)
{
    $currentYear = $year ?? date('Y');
    
    $builder = $this->db->table('pesanan');
    
    $builder->select("
        YEAR(created_at) as tahun,
        COUNT(*) as total_pesanan,
        SUM(total_harga) as total_pendapatan,
        SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as pesanan_selesai,
        SUM(CASE WHEN status = 'dibatalkan' THEN 1 ELSE 0 END) as pesanan_dibatalkan,
        AVG(total_harga) as rata_rata_pesanan
    ");
    
    if ($currentYear) {
        $builder->where("YEAR(created_at)", $currentYear);
    }
    
    $builder->groupBy("YEAR(created_at)");
    $builder->orderBy("YEAR(created_at)", "DESC");
    
    return $builder->get()->getRowArray();
}

// Get monthly count with custom filters (optional)
public function getMonthlyCountWithFilters($year = null, $status = null, $jenisLayanan = null)
{
    $currentYear = $year ?? date('Y');
    
    $builder = $this->db->table('pesanan');
    
    $builder->select("
        MONTH(created_at) as bulan,
        COUNT(*) as jumlah_pesanan,
        SUM(total_harga) as total_pendapatan
    ");
    
    // Filter berdasarkan tahun
    $builder->where("YEAR(created_at)", $currentYear);
    
    // Filter status jika diberikan
    if ($status && $status !== '') {
        $builder->where('status', $status);
    }
    
    // Filter jenis layanan jika diberikan
    if ($jenisLayanan && $jenisLayanan !== '') {
        $builder->where('jenis_layanan', $jenisLayanan);
    }
    
    $builder->groupBy("MONTH(created_at)");
    $builder->orderBy("MONTH(created_at)", "ASC");
    
    $result = $builder->get()->getResultArray();
    
    // Format hasil untuk semua bulan (1-12)
    $monthlyData = [];
    
    for ($i = 1; $i <= 12; $i++) {
        $monthlyData[$i] = [
            'bulan' => $i,
            'nama_bulan' => $this->getMonthName($i),
            'jumlah_pesanan' => 0,
            'total_pendapatan' => 0
        ];
    }
    
    // Isi data yang ada
    foreach ($result as $row) {
        $bulan = (int)$row['bulan'];
        $monthlyData[$bulan] = [
            'bulan' => $bulan,
            'nama_bulan' => $this->getMonthName($bulan),
            'jumlah_pesanan' => (int)$row['jumlah_pesanan'],
            'total_pendapatan' => (float)$row['total_pendapatan']
        ];
    }
    
    return array_values($monthlyData);
}

// Get latest orders with limit - PERBAIKAN VERSION
public function getLatest($limit = 5)
{
    $builder = $this->db->table('pesanan p');
    
    // Select field yang diperlukan untuk tampilan daftar pesanan terbaru
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.status,
        p.total_harga,
        p.total_akhir,
        p.created_at,
        p.lokasi_acara,
        p.dp_dibayar,
        pm.nama_paket as paket_nama,
        k.nama_kostum as kostum_nama
    ');
    
    // Join dengan tabel terkait
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Order by created_at DESC untuk mendapatkan yang terbaru
    $builder->orderBy('p.created_at', 'DESC');
    
    // Limit jumlah data
    $builder->limit($limit);
    
    return $builder->get()->getResultArray();
}
// Get latest orders with optional status filter
// Get latest orders with optional status filter - PERBAIKAN VERSION
public function getLatestByStatus($limit = 5, $status = null)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.status,
        p.total_harga,
        p.total_akhir,
        p.created_at,
        p.lokasi_acara,
        p.dp_dibayar,
        pm.nama_paket as paket_nama,
        k.nama_kostum as kostum_nama
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Filter by status jika diberikan
    if ($status !== null) {
        $builder->where('p.status', $status);
    }
    
    $builder->orderBy('p.created_at', 'DESC');
    $builder->limit($limit);
    
    return $builder->get()->getResultArray();
}

// Get upcoming orders (orders with tanggal_acara in the future) - PERBAIKAN VERSION
public function getUpcomingOrders($limit = 5)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.status,
        p.total_harga,
        p.total_akhir,
        p.created_at,
        p.lokasi_acara,
        p.dp_dibayar,
        pm.nama_paket as paket_nama,
        k.nama_kostum as kostum_nama,
        DATEDIFF(p.tanggal_acara, CURDATE()) as hari_menuju_acara
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Hanya pesanan dengan tanggal acara di masa depan
    $builder->where('p.tanggal_acara >=', date('Y-m-d'));
    
    // Hanya pesanan dengan status aktif
    $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    
    // Urutkan berdasarkan tanggal acara terdekat
    $builder->orderBy('p.tanggal_acara', 'ASC');
    
    // Limit jumlah data
    $builder->limit($limit);
    
    return $builder->get()->getResultArray();
}

// Get latest pending orders (for admin notification)
public function getLatestPending($limit = 5)
{
    return $this->getLatestByStatus($limit, 'pending');
}

// Get upcoming orders (orders with tanggal_acara in the future)


// Get latest orders with full details (for dashboard)
public function getLatestWithDetails($limit = 5)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.email,
        p.jenis_layanan,
        p.tanggal_acara,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        p.total_akhir,
        p.dp_dibayar,
        p.metode_pembayaran,
        p.created_at,
        p.updated_at,
        p.catatan_admin,
        pm.nama_paket,
        pm.harga as paket_harga,
        k.nama_kostum,
        k.harga_sewa as kostum_harga,
        a.nama_area,
        a.biaya_tambahan,
        u.nama as admin_nama
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    $builder->join('area_layanan a', 'a.id = p.area_id', 'left');
    $builder->join('users u', 'u.id = p.created_by', 'left');
    
    $builder->orderBy('p.created_at', 'DESC');
    $builder->limit($limit);
    
    return $builder->get()->getResultArray();
}

// Get today's orders
public function getTodayOrders()
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.jenis_layanan,
        p.tanggal_acara,
        p.status,
        p.total_harga,
        p.created_at
    ');
    
    $builder->where('DATE(p.created_at)', date('Y-m-d'));
    
    $builder->orderBy('p.created_at', 'DESC');
    
    return $builder->get()->getResultArray();
}

// Get latest orders for a specific customer
public function getLatestByCustomer($customerPhone, $limit = 5)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.jenis_layanan,
        p.tanggal_acara,
        p.status,
        p.total_harga,
        p.total_akhir,
        p.created_at,
        pm.nama_paket,
        k.nama_kostum
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    $builder->where('p.no_whatsapp', $customerPhone);
    
    $builder->orderBy('p.created_at', 'DESC');
    $builder->limit($limit);
    
    return $builder->get()->getResultArray();
}
// Get upcoming events (events within specified days)
public function getUpcomingEvents($daysAhead = 7, $limit = 10)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.email,
        p.jenis_layanan,
        p.tanggal_acara,
        p.tanggal_acara as event_date,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        p.dp_dibayar,
        p.catatan_admin,
        pm.nama_paket,
        pm.harga as paket_harga,
        k.nama_kostum,
        k.harga_sewa as kostum_harga,
        DATEDIFF(p.tanggal_acara, CURDATE()) as days_until_event,
        CASE 
            WHEN DATEDIFF(p.tanggal_acara, CURDATE()) = 0 THEN "Hari Ini"
            WHEN DATEDIFF(p.tanggal_acara, CURDATE()) = 1 THEN "Besok"
            ELSE CONCAT(DATEDIFF(p.tanggal_acara, CURDATE()), " hari lagi")
        END as time_until_event
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Hanya acara di masa depan
    $builder->where('p.tanggal_acara >=', date('Y-m-d'));
    
    // Hanya acara dalam X hari ke depan
    $builder->where('p.tanggal_acara <=', date('Y-m-d', strtotime("+{$daysAhead} days")));
    
    // Hanya status yang aktif/diproses
    $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    
    // Urutkan berdasarkan tanggal acara terdekat
    $builder->orderBy('p.tanggal_acara', 'ASC');
    $builder->orderBy('p.created_at', 'DESC');
    
    // Limit jumlah data
    if ($limit > 0) {
        $builder->limit($limit);
    }
    
    $result = $builder->get()->getResultArray();
    
    // Format hasil untuk tampilan yang lebih baik
    foreach ($result as &$row) {
        $row['formatted_date'] = $this->formatEventDate($row['tanggal_acara']);
        $row['day_name'] = $this->getDayName($row['tanggal_acara']);
        $row['event_type'] = $row['nama_paket'] ? 'Makeup' : ($row['nama_kostum'] ? 'Kostum' : 'Lainnya');
        $row['is_today'] = $row['days_until_event'] == 0;
        $row['is_tomorrow'] = $row['days_until_event'] == 1;
        $row['urgency_level'] = $this->getUrgencyLevel($row['days_until_event']);
    }
    
    return $result;
}

// Get upcoming events with status filter
public function getUpcomingEventsByStatus($daysAhead = 7, $status = null, $limit = 10)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        pm.nama_paket,
        k.nama_kostum,
        DATEDIFF(p.tanggal_acara, CURDATE()) as days_until_event
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Hanya acara di masa depan
    $builder->where('p.tanggal_acara >=', date('Y-m-d'));
    
    // Hanya acara dalam X hari ke depan
    $builder->where('p.tanggal_acara <=', date('Y-m-d', strtotime("+{$daysAhead} days")));
    
    // Filter status jika diberikan
    if ($status !== null) {
        $builder->where('p.status', $status);
    } else {
        $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    }
    
    $builder->orderBy('p.tanggal_acara', 'ASC');
    
    if ($limit > 0) {
        $builder->limit($limit);
    }
    
    return $builder->get()->getResultArray();
}

// Get today's events
public function getTodayEvents()
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        pm.nama_paket,
        k.nama_kostum
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Acara hari ini
    $builder->where('DATE(p.tanggal_acara)', date('Y-m-d'));
    
    // Hanya status yang aktif
    $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    
    $builder->orderBy('p.tanggal_acara', 'ASC');
    
    return $builder->get()->getResultArray();
}

// Get events for specific date
public function getEventsByDate($date, $status = null)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        p.dp_dibayar,
        pm.nama_paket,
        k.nama_kostum,
        TIMESTAMPDIFF(HOUR, NOW(), CONCAT(p.tanggal_acara, " 00:00:00")) as hours_until_event
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    $builder->where('DATE(p.tanggal_acara)', $date);
    
    // Filter status jika diberikan
    if ($status !== null) {
        $builder->where('p.status', $status);
    }
    
    $builder->orderBy('p.tanggal_acara', 'ASC');
    
    return $builder->get()->getResultArray();
}

// Get events count by date range for calendar
public function getEventsCountByDateRange($startDate, $endDate)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        DATE(p.tanggal_acara) as event_date,
        COUNT(*) as event_count,
        SUM(CASE WHEN p.jenis_layanan = "makeup" THEN 1 ELSE 0 END) as makeup_count,
        SUM(CASE WHEN p.jenis_layanan = "kostum" THEN 1 ELSE 0 END) as kostum_count
    ');
    
    $builder->where('p.tanggal_acara >=', $startDate);
    $builder->where('p.tanggal_acara <=', $endDate);
    $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    
    $builder->groupBy('DATE(p.tanggal_acara)');
    $builder->orderBy('event_date', 'ASC');
    
    $result = $builder->get()->getResultArray();
    
    // Format ke array asosiatif dengan tanggal sebagai key
    $eventsByDate = [];
    foreach ($result as $event) {
        $eventsByDate[$event['event_date']] = $event;
    }
    
    return $eventsByDate;
}

// Get urgent events (events within 3 days)
public function getUrgentEvents($limit = 5)
{
    $builder = $this->db->table('pesanan p');
    
    $builder->select('
        p.id,
        p.kode_pesanan,
        p.nama_lengkap,
        p.no_whatsapp,
        p.jenis_layanan,
        p.tanggal_acara,
        p.lokasi_acara,
        p.status,
        p.total_harga,
        p.dp_dibayar,
        pm.nama_paket,
        k.nama_kostum,
        DATEDIFF(p.tanggal_acara, CURDATE()) as days_until_event
    ');
    
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Acara dalam 3 hari ke depan
    $builder->where('p.tanggal_acara >=', date('Y-m-d'));
    $builder->where('p.tanggal_acara <=', date('Y-m-d', strtotime('+3 days')));
    
    // Hanya status yang aktif
    $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
    
    // Urutan berdasarkan urgency
    $builder->orderBy('days_until_event', 'ASC');
    $builder->orderBy('p.created_at', 'DESC');
    
    $builder->limit($limit);
    
    $result = $builder->get()->getResultArray();
    
    foreach ($result as &$row) {
        $row['urgency_level'] = $this->getUrgencyLevel($row['days_until_event']);
        $row['is_urgent'] = $row['days_until_event'] <= 1;
    }
    
    return $result;
}

// Helper methods
private function formatEventDate($date)
{
    $timestamp = strtotime($date);
    $today = strtotime('today');
    $tomorrow = strtotime('tomorrow');
    
    if ($timestamp == $today) {
        return 'Hari Ini';
    } elseif ($timestamp == $tomorrow) {
        return 'Besok';
    } else {
        return date('d M Y', $timestamp);
    }
}

private function getDayName($date)
{
    $dayNames = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    $englishDay = date('l', strtotime($date));
    return $dayNames[$englishDay] ?? $englishDay;
}

private function getUrgencyLevel($daysUntil)
{
    if ($daysUntil == 0) {
        return 'danger'; // Hari ini
    } elseif ($daysUntil == 1) {
        return 'warning'; // Besok
    } elseif ($daysUntil <= 3) {
        return 'info'; // 2-3 hari lagi
    } else {
        return 'success'; // Lebih dari 3 hari
    }
}
}