<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_pesanan',
        'nama_lengkap',
        'no_whatsapp',
        'email',
        'jenis_layanan',
        'paket_id',
        'kostum_id',
        'tanggal_acara',
        'lokasi_acara',
        'informasi_tambahan',
        'status',
        'total_harga',
        'dp_dibayar',
        'metode_pembayaran',
        'catatan_admin',
        'bukti_pembayaran',
        'tanggal_pelunasan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'kode_pesanan' => 'required|is_unique[pesanan.kode_pesanan,id,{id}]',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'no_whatsapp' => 'required|min_length[10]|max_length[20]',
        'jenis_layanan' => 'required|in_list[makeup,kostum,keduanya]',
        'tanggal_acara' => 'required|valid_date',
        'lokasi_acara' => 'required|min_length[5]',
        'status' => 'required|in_list[pending,dikonfirmasi,diproses,selesai,dibatalkan]'
    ];
    
    protected $validationMessages = [
        'kode_pesanan' => [
            'required' => 'Kode pesanan wajib diisi',
            'is_unique' => 'Kode pesanan sudah digunakan'
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap wajib diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'no_whatsapp' => [
            'required' => 'Nomor WhatsApp wajib diisi',
            'min_length' => 'Nomor WhatsApp minimal 10 digit',
            'max_length' => 'Nomor WhatsApp maksimal 20 digit'
        ],
        'tanggal_acara' => [
            'required' => 'Tanggal acara wajib diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'lokasi_acara' => [
            'required' => 'Lokasi acara wajib diisi',
            'min_length' => 'Lokasi acara minimal 5 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodePesanan', 'setDefaultValues'];
    protected $beforeUpdate = ['beforeUpdateCallback'];
    
    /**
     * Generate kode pesanan otomatis
     */
    protected function generateKodePesanan(array $data)
    {
        if (!isset($data['data']['kode_pesanan']) || empty($data['data']['kode_pesanan'])) {
            $prefix = 'ORD';
            $date = date('Ymd');
            $random = strtoupper(substr(md5(uniqid()), 0, 6));
            $data['data']['kode_pesanan'] = $prefix . '-' . $date . '-' . $random;
        }
        return $data;
    }
    
    /**
     * Set nilai default
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status']) || empty($data['data']['status'])) {
            $data['data']['status'] = 'pending';
        }
        
        if (!isset($data['data']['created_at']) || empty($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }
    
    /**
     * Before update callback
     */
    protected function beforeUpdateCallback(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
    
    /**
     * Get pesanan dengan relasi
     */
    public function getPesananWithRelations($id = null, $status = null, $limit = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, 
                         pm.nama_paket as paket_nama, 
                         pm.harga as paket_harga,
                         k.nama_kostum as kostum_nama,
                         k.harga_sewa as kostum_harga,
                         k.kategori as kostum_kategori');
        
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        
        if ($id) {
            $builder->where('p.id', $id);
        }
        
        if ($status) {
            $builder->where('p.status', $status);
        }
        
        $builder->orderBy('p.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $query = $builder->get();
        
        if ($id) {
            return $query->getRowArray();
        }
        
        return $query->getResultArray();
    }
    
    /**
     * Get pesanan by status
     */
    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get pesanan by bulan
     */
    public function getByMonth($year, $month)
    {
        return $this->where("DATE_FORMAT(tanggal_acara, '%Y-%m')", "$year-$month")
                    ->orderBy('tanggal_acara', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get statistik pesanan
     */
    public function getStatistics()
    {
        $stats = [
            'total' => $this->countAll(),
            'pending' => $this->where('status', 'pending')->countAllResults(),
            'dikonfirmasi' => $this->where('status', 'dikonfirmasi')->countAllResults(),
            'diproses' => $this->where('status', 'diproses')->countAllResults(),
            'selesai' => $this->where('status', 'selesai')->countAllResults(),
            'dibatalkan' => $this->where('status', 'dibatalkan')->countAllResults(),
        ];
        
        // Hitung total pendapatan dari pesanan selesai
        $builder = $this->db->table($this->table);
        $builder->selectSum('total_harga', 'total_pendapatan');
        $builder->where('status', 'selesai');
        $query = $builder->get();
        $result = $query->getRow();
        
        $stats['total_pendapatan'] = $result->total_pendapatan ?? 0;
        
        return $stats;
    }
    
    /**
     * Get pesanan terbaru
     */
    public function getLatest($limit = 10)
    {
        return $this->select('p.*, pm.nama_paket, k.nama_kostum')
                    ->from($this->table . ' p', true)
                    ->join('paket_makeup pm', 'pm.id = p.paket_id', 'left')
                    ->join('kostum k', 'k.id = p.kostum_id', 'left')
                    ->orderBy('p.created_at', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }
    
    /**
     * Update status pesanan
     */
    public function updateStatus($id, $status, $catatan = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($catatan) {
            $data['catatan_admin'] = $catatan;
        }
        
        // Jika status selesai, set tanggal pelunasan jika belum ada
        if ($status == 'selesai') {
            $pesanan = $this->find($id);
            if ($pesanan && empty($pesanan['tanggal_pelunasan'])) {
                $data['tanggal_pelunasan'] = date('Y-m-d H:i:s');
            }
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Update pembayaran
     */
    public function updatePembayaran($id, $dp_dibayar, $total_harga = null, $metode = null, $bukti = null)
    {
        $data = [
            'dp_dibayar' => $dp_dibayar,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($total_harga) {
            $data['total_harga'] = $total_harga;
        }
        
        if ($metode) {
            $data['metode_pembayaran'] = $metode;
        }
        
        if ($bukti) {
            $data['bukti_pembayaran'] = $bukti;
        }
        
        // Jika DP sudah lunas, update status
        $pesanan = $this->find($id);
        if ($pesanan && $dp_dibayar >= ($pesanan['total_harga'] * 0.5)) {
            $data['status'] = 'dikonfirmasi';
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Search pesanan
     */
    public function search($keyword)
    {
        return $this->select('p.*, pm.nama_paket, k.nama_kostum')
                    ->from($this->table . ' p', true)
                    ->join('paket_makeup pm', 'pm.id = p.paket_id', 'left')
                    ->join('kostum k', 'k.id = p.kostum_id', 'left')
                    ->groupStart()
                        ->like('p.kode_pesanan', $keyword)
                        ->orLike('p.nama_lengkap', $keyword)
                        ->orLike('p.no_whatsapp', $keyword)
                        ->orLike('pm.nama_paket', $keyword)
                        ->orLike('k.nama_kostum', $keyword)
                    ->groupEnd()
                    ->orderBy('p.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
    }
    
    /**
     * Get pesanan untuk laporan
     */
    public function getForReport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, 
                         pm.nama_paket, 
                         pm.harga as paket_harga,
                         k.nama_kostum,
                         k.harga_sewa as kostum_harga');
        
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        
        if ($startDate && $endDate) {
            $builder->where('DATE(p.created_at) >=', $startDate);
            $builder->where('DATE(p.created_at) <=', $endDate);
        }
        
        if ($status) {
            $builder->where('p.status', $status);
        }
        
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Hitung total pesanan per bulan untuk chart
     */
    public function getMonthlyCount($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table($this->table);
        $builder->select("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total");
        $builder->where("YEAR(created_at)", $year);
        $builder->groupBy("DATE_FORMAT(created_at, '%Y-%m')");
        $builder->orderBy('bulan', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        // Format untuk chart
        $months = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf('%02d', $i);
            $key = $year . '-' . $month;
            $months[] = date('M', strtotime($key . '-01'));
            
            $found = false;
            foreach ($result as $row) {
                if ($row['bulan'] == $key) {
                    $data[] = (int)$row['total'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $data[] = 0;
            }
        }
        
        return [
            'months' => $months,
            'data' => $data
        ];
    }
    
    /**
     * Get pesanan yang akan datang (7 hari ke depan)
     */
    public function getUpcomingEvents($days = 7)
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+$days days"));
        
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, pm.nama_paket, k.nama_kostum');
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        
        $builder->where("DATE(p.tanggal_acara) >=", $startDate);
        $builder->where("DATE(p.tanggal_acara) <=", $endDate);
        $builder->whereIn('p.status', ['dikonfirmasi', 'diproses']);
        
        $builder->orderBy('p.tanggal_acara', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}