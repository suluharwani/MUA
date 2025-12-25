<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\PaketMakeupModel;
use App\Models\KostumModel;
use App\Models\AreaLayananModel;
use App\Models\PengaturanModel;
use App\Models\PembayaranModel;

class Pesanan extends BaseController
{
    protected $pesananModel;
    protected $paketModel;
    protected $kostumModel;
    protected $areaModel;
    protected $pengaturanModel;
    protected $pembayaranModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->paketModel = new PaketMakeupModel();
        $this->kostumModel = new KostumModel();
        $this->areaModel = new AreaLayananModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->pembayaranModel = new PembayaranModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kelola Pesanan'
        ];
        
        return view('admin/pesanan/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Buat Pesanan Baru',
            'paket_makeup' => $this->paketModel->where('is_active', 1)->findAll(),
            'kostum' => $this->kostumModel->where('is_active', 1)->findAll(),
            'area_layanan' => $this->areaModel->where('is_active', 1)->findAll()
        ];
        
        // Jika request AJAX untuk modal
        if ($this->request->isAJAX()) {
            return view('admin/pesanan/create_modal', $data);
        }
        
        return view('admin/pesanan/create', $data);
    }
    
    // Ajax: Get Data untuk DataTables
// ... di dalam class Pesanan ...

// Ajax: Get Data untuk DataTables - Diperbaiki
public function getData()
{
    // Pastikan ini adalah request AJAX
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(405)->setJSON([
            'error' => 'Method not allowed'
        ]);
    }

    $request = $this->request;
    
    // Debug: Log request
    log_message('debug', 'DataTables request received: ' . print_r($request->getPost(), true));

    // Get POST parameters dengan default values
    $draw = (int)($request->getPost('draw') ?? 0);
    $start = (int)($request->getPost('start') ?? 0);
    $length = (int)($request->getPost('length') ?? 10);
    
    // Handle search parameter
    $searchValue = '';
    if ($request->getPost('search') && is_array($request->getPost('search'))) {
        $searchArray = $request->getPost('search');
        $searchValue = $searchArray['value'] ?? '';
    }
    
    $status = $request->getPost('status') ?? '';
    $date = $request->getPost('date') ?? '';
    $layanan = $request->getPost('layanan') ?? '';

    try {
        // Get data from model
        $pesanan = $this->pesananModel->getDataTables($start, $length, $searchValue, $status, $date, $layanan);
        
        // Debug: Log data count
        log_message('debug', 'Data retrieved: ' . count($pesanan) . ' records');

        // Count total records
        $totalRecords = $this->pesananModel->countAll();
        $totalFiltered = $this->pesananModel->countFiltered($searchValue, $status, $date, $layanan);

        // Prepare data for DataTables
        $data = [];
        $no = $start + 1;

        foreach ($pesanan as $row) {
            // Pastikan $row adalah array
            if (!is_array($row)) {
                continue;
            }

            // Format tanggal
            $tanggalAcara = '';
            if (!empty($row['tanggal_acara'])) {
                try {
                    $dateObj = new \DateTime($row['tanggal_acara']);
                    $tanggalAcara = $dateObj->format('d/m/Y');
                } catch (\Exception $e) {
                    $tanggalAcara = $row['tanggal_acara'];
                }
            }

            // Format tanggal pesan
            $tanggalPesan = '';
            if (!empty($row['created_at'])) {
                try {
                    $dateObj = new \DateTime($row['created_at']);
                    $tanggalPesan = $dateObj->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    $tanggalPesan = $row['created_at'];
                }
            }

            // Format total harga
            $totalHarga = (float)($row['total_harga'] ?? $row['total_akhir'] ?? 0);
            
            // Determine jenis layanan display
            $jenisLayananDisplay = '';
            if (!empty($row['jenis_layanan'])) {
                $jenisLayananDisplay = ucfirst($row['jenis_layanan']);
            }

            // Determine status display
            $statusDisplay = '';
            $statusClass = '';
            if (!empty($row['status'])) {
                $statusDisplay = ucfirst($row['status']);
                $statusClass = $this->getStatusBadgeClass($row['status']);
            }

            $data[] = [
                'DT_RowId' => 'row_' . ($row['id'] ?? ''),
                'DT_RowIndex' => $no++,
                'kode_pesanan' => $row['kode_pesanan'] ?? '',
                'nama_lengkap' => $row['nama_lengkap'] ?? '',
                'no_whatsapp' => $row['no_whatsapp'] ?? '',
                'jenis_layanan' => $jenisLayananDisplay,
                'tanggal_acara' => $tanggalAcara,
                'total_harga' => $totalHarga,
                'status' => $statusDisplay,
                'status_class' => $statusClass,
                'created_at' => $tanggalPesan,
                'id' => $row['id'] ?? 0,
                'actions' => $row['id'] ?? 0
            ];
        }

        // Debug: Log response structure
        log_message('debug', 'DataTables response prepared: ' . count($data) . ' items');

        $response = [
            'draw' => $draw,
            'recordsTotal' => (int)$totalRecords,
            'recordsFiltered' => (int)$totalFiltered,
            'data' => $data
        ];

        // Debug: Log final response
        log_message('debug', 'Sending DataTables response');

        return $this->response->setJSON($response);

    } catch (\Exception $e) {
        log_message('error', 'Error in Pesanan::getData: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());

        return $this->response->setStatusCode(500)->setJSON([
            'draw' => $draw,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
        ]);
    }
}

// Helper untuk badge class status
private function getStatusBadgeClass($status)
{
    $classes = [
        'pending' => 'bg-warning',
        'dikonfirmasi' => 'bg-info',
        'diproses' => 'bg-primary',
        'selesai' => 'bg-success',
        'dibatalkan' => 'bg-danger'
    ];
    
    return $classes[$status] ?? 'bg-secondary';
}
public function generateKode()
{
    try {
        $model = new PesananModel();
        $kode = $model->generateKodePesanan();
        
        return $this->response->setJSON([
            'success' => true,
            'kode' => $kode
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
    
    // Ajax: Get Statistics
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $search = $this->request->getPost('search') ?? '';
        $status = $this->request->getPost('status') ?? '';
        $date = $this->request->getPost('date') ?? '';
        $layanan = $this->request->getPost('layanan') ?? '';
        $dateRange = $this->request->getPost('date_range') ?? '';
        
        try {
            $stats = $this->pesananModel->getStatistics($search, $status, $date, $layanan, $dateRange);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getStats: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'data' => [
                    'total' => 0,
                    'pending' => 0,
                    'dikonfirmasi' => 0,
                    'diproses' => 0,
                    'selesai' => 0,
                    'dibatalkan' => 0,
                    'total_revenue' => 0,
                    'avg_order_value' => 0
                ]
            ]);
        }
    }
    
    // Ajax: Calculate price
    public function calculatePrice()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $data = [
            'paket_id' => $this->request->getPost('paket_id') ? (int)$this->request->getPost('paket_id') : null,
            'kostum_id' => $this->request->getPost('kostum_id') ? (int)$this->request->getPost('kostum_id') : null,
            'area_id' => $this->request->getPost('area_id') ? (int)$this->request->getPost('area_id') : null,
            'biaya_transport' => $this->request->getPost('biaya_transport') ? (float)$this->request->getPost('biaya_transport') : 0,
            'diskon_persen' => $this->request->getPost('diskon_persen') ? (float)$this->request->getPost('diskon_persen') : 0,
            'diskon_nominal' => $this->request->getPost('diskon_nominal') ? (float)$this->request->getPost('diskon_nominal') : 0,
            'pajak_persen' => $this->request->getPost('pajak_persen') ? (float)$this->request->getPost('pajak_persen') : 0,
            'pajak_nominal' => $this->request->getPost('pajak_nominal') ? (float)$this->request->getPost('pajak_nominal') : 0,
            'lama_sewa' => $this->request->getPost('lama_sewa') ? (int)$this->request->getPost('lama_sewa') : 1
        ];
        
        try {
            $calculated = $this->pesananModel->calculateTotalPrice($data);
            
            // Get DP percentage from settings
            $dpPercentage = $this->pengaturanModel->where('key_name', 'dp_percentage')->first();
            $dpPercentageValue = $dpPercentage ? (float)$dpPercentage['value'] : 50;
            $dpMinimal = $calculated['total_akhir'] * $dpPercentageValue / 100;
            
            $calculated['dp_minimal'] = $dpMinimal;
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $calculated
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::calculatePrice: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung harga'
            ]);
        }
    }
    
    // Ajax: Store new order
    // public function store()
    // {
    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request'
    //         ]);
    //     }
        
    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'nama_lengkap' => 'required|min_length[3]|max_length[100]',
    //         'no_whatsapp' => 'required|min_length[10]|max_length[20]',
    //         'email' => 'permit_empty|valid_email',
    //         'jenis_layanan' => 'required|in_list[makeup,kostum,keduanya]',
    //         'tanggal_acara' => 'required|valid_date',
    //         'lokasi_acara' => 'required',
    //         'metode_pembayaran' => 'required'
    //     ]);
        
    //     if (!$validation->withRequest($this->request)->run()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Validasi gagal',
    //             'errors' => $validation->getErrors()
    //         ]);
    //     }
        
    //     // Collect data safely
    //     $data = [
    //         'nama_lengkap' => $this->request->getPost('nama_lengkap'),
    //         'no_whatsapp' => $this->request->getPost('no_whatsapp'),
    //         'email' => $this->request->getPost('email') ?? '',
    //         'jenis_layanan' => $this->request->getPost('jenis_layanan'),
    //         'tanggal_acara' => $this->request->getPost('tanggal_acara'),
    //         'lokasi_acara' => $this->request->getPost('lokasi_acara'),
    //         'informasi_tambahan' => $this->request->getPost('informasi_tambahan') ?? '',
    //         'status' => $this->request->getPost('status') ?? 'pending',
    //         'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
    //         'catatan_admin' => $this->request->getPost('catatan_admin') ?? '',
    //         'dp_dibayar' => $this->request->getPost('dp_dibayar') ? (float)$this->request->getPost('dp_dibayar') : 0
    //     ];
        
    //     // Add optional fields if provided
    //     $optionalFields = [
    //         'paket_id', 'kostum_id', 'area_id', 'biaya_transport',
    //         'diskon_persen', 'diskon_nominal', 'pajak_persen', 
    //         'pajak_nominal', 'lama_sewa'
    //     ];
        
    //     foreach ($optionalFields as $field) {
    //         $value = $this->request->getPost($field);
    //         if ($value !== null && $value !== '') {
    //             $data[$field] = is_numeric($value) ? (float)$value : $value;
    //         }
    //     }
        
    //     try {
    //         $result = $this->pesananModel->createOrder($data);
            
    //         if ($result['success'] ?? false) {
    //             return $this->response->setJSON([
    //                 'success' => true,
    //                 'message' => 'Pesanan berhasil dibuat',
    //                 'data' => [
    //                     'id' => $result['id'] ?? 0,
    //                     'kode_pesanan' => $result['kode_pesanan'] ?? ''
    //                 ]
    //             ]);
    //         }
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => $result['message'] ?? 'Gagal membuat pesanan'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         log_message('error', 'Error in Pesanan::store: ' . $e->getMessage());
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat menyimpan pesanan'
    //         ]);
    //     }
    // }
    
    // Ajax: Get order for edit
    public function getOrderForEdit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        try {
            $pesanan = $this->pesananModel->getPesananWithRelations($id);
            
            if (!$pesanan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $pesanan
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getOrderForEdit: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }
    
    // Ajax: Update order
    // public function update($id)
    // {
    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request'
    //         ]);
    //     }
        
    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'nama_lengkap' => 'required|min_length[3]|max_length[100]',
    //         'no_whatsapp' => 'required|min_length[10]|max_length[20]',
    //         'email' => 'permit_empty|valid_email',
    //         'jenis_layanan' => 'required|in_list[makeup,kostum,keduanya]',
    //         'tanggal_acara' => 'required|valid_date',
    //         'lokasi_acara' => 'required'
    //     ]);
        
    //     if (!$validation->withRequest($this->request)->run()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Validasi gagal',
    //             'errors' => $validation->getErrors()
    //         ]);
    //     }
        
    //     $data = [
    //         'nama_lengkap' => $this->request->getPost('nama_lengkap'),
    //         'no_whatsapp' => $this->request->getPost('no_whatsapp'),
    //         'email' => $this->request->getPost('email') ?? '',
    //         'jenis_layanan' => $this->request->getPost('jenis_layanan'),
    //         'tanggal_acara' => $this->request->getPost('tanggal_acara'),
    //         'lokasi_acara' => $this->request->getPost('lokasi_acara'),
    //         'informasi_tambahan' => $this->request->getPost('informasi_tambahan') ?? '',
    //         'status' => $this->request->getPost('status') ?? 'pending',
    //         'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
    //         'catatan_admin' => $this->request->getPost('catatan_admin') ?? '',
    //         'dp_dibayar' => $this->request->getPost('dp_dibayar') ? (float)$this->request->getPost('dp_dibayar') : 0
    //     ];
        
    //     // Add optional fields if provided
    //     $optionalFields = [
    //         'paket_id', 'kostum_id', 'area_id', 'biaya_transport',
    //         'diskon_persen', 'diskon_nominal', 'pajak_persen', 
    //         'pajak_nominal', 'lama_sewa'
    //     ];
        
    //     foreach ($optionalFields as $field) {
    //         $value = $this->request->getPost($field);
    //         if ($value !== null && $value !== '') {
    //             $data[$field] = is_numeric($value) ? (float)$value : $value;
    //         }
    //     }
        
    //     try {
    //         if ($this->pesananModel->updateOrder($id, $data)) {
    //             return $this->response->setJSON([
    //                 'success' => true,
    //                 'message' => 'Pesanan berhasil diperbarui'
    //             ]);
    //         }
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Gagal memperbarui pesanan'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         log_message('error', 'Error in Pesanan::update: ' . $e->getMessage());
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat memperbarui pesanan'
    //         ]);
    //     }
    // }
    
    // Ajax: Get detail for modal
    public function getDetail($id)
    {
        try {
            $pesanan = $this->pesananModel->getPesananWithRelations($id);
            
            if (!$pesanan) {
                return '<div class="text-center py-5"><i class="bi bi-exclamation-triangle text-danger fs-1"></i><p class="mt-2">Pesanan tidak ditemukan</p></div>';
            }
            
            return view('admin/pesanan/modal_detail', ['pesanan' => $pesanan]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getDetail: ' . $e->getMessage());
            
            return '<div class="text-center py-5"><i class="bi bi-exclamation-triangle text-danger fs-1"></i><p class="mt-2">Terjadi kesalahan saat memuat data</p></div>';
        }
    }
    
    // Ajax: Update status
    public function updateStatus()
    {

        
        $id = $this->request->getPost('pesanan_id');
        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan') ?? '';
        
        $allowedStatus = ['pending', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan'];
        
        if (!in_array($status, $allowedStatus)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid.'
            ]);
        }
        
        try {
            if ($this->pesananModel->updateStatus($id, $status, $catatan)) {
                return redirect()->back();
            }
            
            return redirect()->back();
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::updateStatus: ' . $e->getMessage());
            
            return redirect()->back();
        }
        return redirect()->back();
    }
    
    // Ajax: Delete pesanan
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        try {
            if ($this->pesananModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pesanan berhasil dihapus.'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus pesanan.'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::delete: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus pesanan'
            ]);
        }
    }
    
    // Export Excel
   // Export Excel
public function export($type = 'excel')
{
    $search = $this->request->getGet('search');
    $status = $this->request->getGet('status');
    $date = $this->request->getGet('date');
    $layanan = $this->request->getGet('layanan');
    
    try {
        // Get data dari model
        $pesanan = $this->pesananModel->getForReport($search, $status, $date, $layanan);
        
        if ($type == 'excel') {
            return $this->exportExcel($pesanan);
        }
        
        // Untuk tipe lain (PDF, CSV) bisa ditambahkan di sini
        return redirect()->back()->with('error', 'Format export tidak didukung');
        
    } catch (\Exception $e) {
        log_message('error', 'Error in Pesanan::export: ' . $e->getMessage());
        
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
    }
}
    

    
    // Calendar View
    public function calendar()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');
        
        try {
            $events = $this->pesananModel->getByMonth($year, $month);
            
            $calendarEvents = [];
            foreach ($events as $event) {
                $calendarEvents[] = [
                    'title' => $event['nama_lengkap'] . ' - ' . ($event['paket_nama'] ?? $event['kostum_nama'] ?? 'Layanan'),
                    'start' => $event['tanggal_acara'] . 'T08:00:00',
                    'end' => $event['tanggal_acara'] . 'T20:00:00',
                    'color' => $this->getStatusColor($event['status']),
                    'url' => base_url('admin/pesanan/detail/' . $event['id'])
                ];
            }
            
            $data = [
                'title' => 'Kalendar Acara',
                'events' => json_encode($calendarEvents),
                'current_year' => $year,
                'current_month' => $month
            ];
            
            return view('admin/pesanan/calendar', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::calendar: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat kalendar');
        }
    }
    
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ffc107',
            'dikonfirmasi' => '#17a2b8',
            'diproses' => '#007bff',
            'selesai' => '#28a745',
            'dibatalkan' => '#dc3545'
        ];
        
        return $colors[$status] ?? '#6c757d';
    }
    
    // Ajax: Get package details
    public function getPaketDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        try {
            $paket = $this->paketModel->find($id);
            
            if (!$paket) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $paket['id'],
                    'nama' => $paket['nama_paket'],
                    'harga' => $paket['harga'],
                    'deskripsi' => $paket['deskripsi'],
                    'durasi' => $paket['durasi'],
                    'features' => json_decode($paket['features'] ?? '[]', true)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getPaketDetail: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data paket'
            ]);
        }
    }
    
    // Ajax: Get costume details
    public function getKostumDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        try {
            $kostum = $this->kostumModel->find($id);
            
            if (!$kostum) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kostum tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $kostum['id'],
                    'nama' => $kostum['nama_kostum'],
                    'harga' => $kostum['harga_sewa'],
                    'deskripsi' => $kostum['deskripsi'],
                    'durasi' => $kostum['durasi_sewa'],
                    'kategori' => $kostum['kategori'],
                    'ukuran' => $kostum['ukuran'],
                    'stok_tersedia' => $kostum['stok_tersedia']
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getKostumDetail: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kostum'
            ]);
        }
    }
    
    // Ajax: Get area details
    public function getAreaDetail($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        try {
            $area = $this->areaModel->find($id);
            
            if (!$area) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Area tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $area['id'],
                    'nama' => $area['nama_area'],
                    'jenis' => $area['jenis_area'],
                    'biaya_tambahan' => $area['biaya_tambahan'],
                    'keterangan' => $area['keterangan']
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Pesanan::getAreaDetail: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data area'
            ]);
        }
    }
    // Tambahkan method store untuk menyimpan data
public function store()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'no_whatsapp' => 'required|min_length[10]|max_length[20]',
        'email' => 'permit_empty|valid_email',
        'jenis_layanan' => 'required|in_list[makeup,kostum,keduanya]',
        'tanggal_acara' => 'required|valid_date',
        'lokasi_acara' => 'required|min_length[5]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validation->getErrors()
        ]);
    }

    try {
        // Prepare data
        $data = [
            'kode_pesanan' => $this->request->getPost('kode_pesanan'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'no_whatsapp' => $this->request->getPost('no_whatsapp'),
            'email' => $this->request->getPost('email') ?? '',
            'jenis_layanan' => $this->request->getPost('jenis_layanan'),
            'tanggal_acara' => $this->request->getPost('tanggal_acara'),
            'lokasi_acara' => $this->request->getPost('lokasi_acara'),
            'informasi_tambahan' => $this->request->getPost('informasi_tambahan') ?? '',
            'status' => $this->request->getPost('status') ?? 'pending',
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran') ?? '',
            'catatan_admin' => $this->request->getPost('catatan_admin') ?? '',
            'dp_dibayar' => $this->request->getPost('dp_dibayar') ? (float)$this->request->getPost('dp_dibayar') : 0,
            'subtotal' => $this->request->getPost('subtotal') ? (float)$this->request->getPost('subtotal') : 0,
            'diskon_nominal' => $this->request->getPost('diskon_nominal') ? (float)$this->request->getPost('diskon_nominal') : 0,
            'pajak_nominal' => $this->request->getPost('pajak_nominal') ? (float)$this->request->getPost('pajak_nominal') : 0,
            'total_akhir' => $this->request->getPost('total_akhir') ? (float)$this->request->getPost('total_akhir') : 0,
            'total_harga' => $this->request->getPost('total_akhir') ? (float)$this->request->getPost('total_akhir') : 0
        ];

        // Handle foreign key fields - convert empty strings to null
        $foreignKeyFields = ['paket_id', 'kostum_id', 'area_id'];
        
        foreach ($foreignKeyFields as $field) {
            $value = $this->request->getPost($field);
            if ($value === '' || $value === null) {
                $data[$field] = null; // Set ke null jika kosong
            } elseif (is_numeric($value)) {
                $data[$field] = (int)$value; // Convert ke integer
            } else {
                $data[$field] = $value;
            }
        }

        // Handle numeric optional fields
        $numericOptionalFields = [
            'biaya_transport' => 0,
            'diskon_persen' => 0,
            'pajak_persen' => 0,
            'lama_sewa' => 1
        ];

        foreach ($numericOptionalFields as $field => $default) {
            $value = $this->request->getPost($field);
            if ($value === '' || $value === null) {
                $data[$field] = $default;
            } else {
                $data[$field] = is_numeric($value) ? (float)$value : $default;
            }
        }

        // Set created_by
        $session = \Config\Services::session();
        $data['created_by'] = $session->get('id') ?? 1;

        // Debug data sebelum insert
        log_message('debug', 'Store data: ' . print_r($data, true));

        // Save to database
        if ($this->pesananModel->insert($data)) {
            $insertId = $this->pesananModel->getInsertID();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'id' => $insertId,
                'kode_pesanan' => $data['kode_pesanan']
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan pesanan ke database'
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error in store: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
}

// Method untuk update
public function update($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'no_whatsapp' => 'required|min_length[10]|max_length[20]',
        'email' => 'permit_empty|valid_email',
        'jenis_layanan' => 'required|in_list[makeup,kostum,keduanya]',
        'tanggal_acara' => 'required|valid_date',
        'lokasi_acara' => 'required|min_length[5]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validation->getErrors()
        ]);
    }

    try {
        // Cek apakah pesanan ada
        $existing = $this->pesananModel->find($id);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ]);
        }

        // Prepare data untuk update
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'no_whatsapp' => $this->request->getPost('no_whatsapp'),
            'email' => $this->request->getPost('email') ?? '',
            'jenis_layanan' => $this->request->getPost('jenis_layanan'),
            'tanggal_acara' => $this->request->getPost('tanggal_acara'),
            'lokasi_acara' => $this->request->getPost('lokasi_acara'),
            'informasi_tambahan' => $this->request->getPost('informasi_tambahan') ?? '',
            'status' => $this->request->getPost('status') ?? 'pending',
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran') ?? '',
            'catatan_admin' => $this->request->getPost('catatan_admin') ?? '',
            'dp_dibayar' => $this->request->getPost('dp_dibayar') ? (float)$this->request->getPost('dp_dibayar') : 0,
            'subtotal' => $this->request->getPost('subtotal') ? (float)$this->request->getPost('subtotal') : 0,
            'diskon_nominal' => $this->request->getPost('diskon_nominal') ? (float)$this->request->getPost('diskon_nominal') : 0,
            'pajak_nominal' => $this->request->getPost('pajak_nominal') ? (float)$this->request->getPost('pajak_nominal') : 0,
            'total_akhir' => $this->request->getPost('total_akhir') ? (float)$this->request->getPost('total_akhir') : 0,
            'total_harga' => $this->request->getPost('total_akhir') ? (float)$this->request->getPost('total_akhir') : 0
        ];

        // Handle foreign key fields - convert empty strings to null
        $foreignKeyFields = ['paket_id', 'kostum_id', 'area_id'];
        
        foreach ($foreignKeyFields as $field) {
            $value = $this->request->getPost($field);
            if ($value === '' || $value === null) {
                $data[$field] = null; // Set ke null jika kosong
            } elseif (is_numeric($value)) {
                $data[$field] = (int)$value; // Convert ke integer
            } else {
                $data[$field] = $value;
            }
        }

        // Handle numeric optional fields
        $numericOptionalFields = [
            'biaya_transport' => 0,
            'diskon_persen' => 0,
            'pajak_persen' => 0,
            'lama_sewa' => 1
        ];

        foreach ($numericOptionalFields as $field => $default) {
            $value = $this->request->getPost($field);
            if ($value === '' || $value === null) {
                $data[$field] = $default;
            } else {
                $data[$field] = is_numeric($value) ? (float)$value : $default;
            }
        }

        // Debug data sebelum update
        log_message('debug', 'Update data: ' . print_r($data, true));

        // Update data
        if ($this->pesananModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui pesanan'
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error in update: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
}
    public function detail($id)
{
    $pesanan = $this->pesananModel->getPesananWithRelations($id);
    
    if (!$pesanan) {
        return redirect()->back()->with('error', 'Pesanan tidak ditemukan');
    }
    
    // Handle null values untuk field yang mungkin null
    $pesanan['paket_nama'] = $pesanan['paket_nama'] ?? null;
    $pesanan['paket_deskripsi'] = $pesanan['paket_deskripsi'] ?? null;
    $pesanan['paket_harga'] = $pesanan['paket_harga'] ?? 0;
    $pesanan['kostum_nama'] = $pesanan['kostum_nama'] ?? null;
    $pesanan['kostum_deskripsi'] = $pesanan['kostum_deskripsi'] ?? null;
    $pesanan['kostum_harga'] = $pesanan['kostum_harga'] ?? 0;
    $pesanan['kostum_kategori'] = $pesanan['kostum_kategori'] ?? null;
    
    // Get payment summary
    $paymentSummary = $this->pembayaranModel->getPaymentSummary($id);
    
    $data = [
        'title' => 'Detail Pesanan',
        'pesanan' => $pesanan,
        'paymentSummary' => $paymentSummary
    ];
    
    return view('admin/pesanan/detail_full', $data);
}
     public function addPayment($pesanan_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'jumlah' => 'required|numeric|greater_than[0]',
            'metode' => 'required|in_list[transfer,cash,qris,lainnya]',
            'jenis_pembayaran' => 'required|in_list[dp,pelunasan,lunas,custom]',
            'tanggal_pembayaran' => 'required|valid_date'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }
        
        try {
            $data = [
                'pesanan_id' => $pesanan_id,
                'kode_pembayaran' => $this->pembayaranModel->generateKodePembayaran(),
                'jumlah' => $this->request->getPost('jumlah'),
                'metode' => $this->request->getPost('metode'),
                'jenis_pembayaran' => $this->request->getPost('jenis_pembayaran'),
                'tanggal_pembayaran' => $this->request->getPost('tanggal_pembayaran'),
                'catatan' => $this->request->getPost('catatan') ?? '',
                'status' => 'diterima', // Auto approve if added by admin
                'tanggal_verifikasi' => date('Y-m-d H:i:s'),
                'verified_by' => session()->get('id')
            ];
            
            // Handle file upload
            $buktiFile = $this->request->getFile('bukti');
            if ($buktiFile && $buktiFile->isValid() && !$buktiFile->hasMoved()) {
                $newName = $buktiFile->getRandomName();
                $buktiFile->move(ROOTPATH . 'public/uploads/pembayaran', $newName);
                $data['bukti'] = $newName;
            }
            
            if ($this->pembayaranModel->insert($data)) {
                // Update order payment status
                $this->updateOrderPaymentStatus($pesanan_id);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil ditambahkan'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error adding payment: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    // Verify payment
    public function verifyPayment($payment_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $payment = $this->pembayaranModel->find($payment_id);
        if (!$payment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pembayaran tidak ditemukan']);
        }
        
        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan') ?? '';
        
        if ($this->pembayaranModel->verifyPayment($payment_id, $status, session()->get('id'), $catatan)) {
            // Update order payment status
            $this->updateOrderPaymentStatus($payment['pesanan_id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui'
            ]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui status']);
    }
    
    // Print invoice
    public function printInvoice($pesanan_id)
    {
        $pesanan = $this->pesananModel->getPesananWithRelations($pesanan_id);
        $paymentSummary = $this->pembayaranModel->getPaymentSummary($pesanan_id);
        
        if (!$pesanan) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan');
        }
        
        $data = [
            'title' => 'Invoice #' . $pesanan['kode_pesanan'],
            'pesanan' => $pesanan,
            'paymentSummary' => $paymentSummary,
            'pengaturan' => $this->pengaturanModel->findAll()
        ];
        
        return view('admin/pesanan/invoice_print', $data);
    }
    
    // Generate PDF invoice
    public function generatePdf($pesanan_id)
    {
        $pesanan = $this->pesananModel->getPesananWithRelations($pesanan_id);
        $paymentSummary = $this->pembayaranModel->getPaymentSummary($pesanan_id);
        
        $dompdf = new \Dompdf\Dompdf();
        
        $html = view('admin/pesanan/invoice_pdf', [
            'pesanan' => $pesanan,
            'paymentSummary' => $paymentSummary,
            'pengaturan' => $this->pengaturanModel->findAll()
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream('invoice-' . $pesanan['kode_pesanan'] . '.pdf', ['Attachment' => false]);
    }
    
    private function updateOrderPaymentStatus($pesanan_id)
    {
        $paymentSummary = $this->pembayaranModel->getPaymentSummary($pesanan_id);
        
        $data = [
            'dp_dibayar' => $paymentSummary['total_dibayar']
        ];
        
        // Auto update order status if fully paid
        if ($paymentSummary['status_pembayaran'] === 'lunas') {
            $data['status'] = 'dikonfirmasi';
        }
        
        $this->pesananModel->update($pesanan_id, $data);
    }
    // Dalam class PesananModel, tambahkan method ini
public function safeUpdate($id, $data)
{
    // Bersihkan data dari nilai yang tidak valid untuk foreign keys
    $foreignKeys = ['paket_id', 'kostum_id', 'area_id'];
    
    foreach ($foreignKeys as $key) {
        if (isset($data[$key]) && ($data[$key] === '' || $data[$key] <= 0)) {
            $data[$key] = null;
        }
    }
    
    return $this->update($id, $data);
}
private function exportExcel($data)
{
    if (empty($data)) {
        return redirect()->back()->with('error', 'Tidak ada data untuk diexport');
    }
    
    // Buat instance Spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set judul file
    $sheet->setTitle('Data Pesanan');
    
    // Header kolom
    $headers = [
        'A' => 'No',
        'B' => 'Kode Pesanan',
        'C' => 'Nama Lengkap',
        'D' => 'No WhatsApp',
        'E' => 'Email',
        'F' => 'Jenis Layanan',
        'G' => 'Tanggal Acara',
        'H' => 'Lokasi Acara',
        'I' => 'Paket Makeup',
        'J' => 'Kostum',
        'K' => 'Area Layanan',
        'L' => 'Subtotal',
        'M' => 'Diskon',
        'N' => 'Pajak',
        'O' => 'Total Akhir',
        'P' => 'DP Dibayar',
        'Q' => 'Status',
        'R' => 'Metode Pembayaran',
        'S' => 'Tanggal Pesan',
        'T' => 'Catatan Admin'
    ];
    
    // Tulis header
    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . '1', $header);
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
    }
    
    // Tulis data
    $row = 2;
    $no = 1;
    
    foreach ($data as $pesanan) {
        // Pastikan $pesanan adalah array
        if (!is_array($pesanan)) {
            continue;
        }
        
        // Get nama paket dan kostum
        $paketNama = '';
        if (!empty($pesanan['paket_id'])) {
            $paket = $this->paketModel->find($pesanan['paket_id']);
            $paketNama = $paket ? $paket['nama_paket'] : '';
        }
        
        $kostumNama = '';
        if (!empty($pesanan['kostum_id'])) {
            $kostum = $this->kostumModel->find($pesanan['kostum_id']);
            $kostumNama = $kostum ? $kostum['nama_kostum'] : '';
        }
        
        $areaNama = '';
        if (!empty($pesanan['area_id'])) {
            $area = $this->areaModel->find($pesanan['area_id']);
            $areaNama = $area ? $area['nama_area'] : '';
        }
        
        // Isi data ke sel
        $sheet->setCellValue('A' . $row, $no);
        $sheet->setCellValue('B' . $row, $pesanan['kode_pesanan'] ?? '');
        $sheet->setCellValue('C' . $row, $pesanan['nama_lengkap'] ?? '');
        $sheet->setCellValue('D' . $row, $pesanan['no_whatsapp'] ?? '');
        $sheet->setCellValue('E' . $row, $pesanan['email'] ?? '');
        $sheet->setCellValue('F' . $row, ucfirst($pesanan['jenis_layanan'] ?? ''));
        
        // Format tanggal acara
        $tanggalAcara = '';
        if (!empty($pesanan['tanggal_acara'])) {
            try {
                $dateObj = new \DateTime($pesanan['tanggal_acara']);
                $tanggalAcara = $dateObj->format('d/m/Y');
            } catch (\Exception $e) {
                $tanggalAcara = $pesanan['tanggal_acara'];
            }
        }
        $sheet->setCellValue('G' . $row, $tanggalAcara);
        
        $sheet->setCellValue('H' . $row, $pesanan['lokasi_acara'] ?? '');
        $sheet->setCellValue('I' . $row, $paketNama);
        $sheet->setCellValue('J' . $row, $kostumNama);
        $sheet->setCellValue('K' . $row, $areaNama);
        $sheet->setCellValue('L' . $row, $pesanan['subtotal'] ?? 0);
        $sheet->setCellValue('M' . $row, $pesanan['diskon_nominal'] ?? 0);
        $sheet->setCellValue('N' . $row, $pesanan['pajak_nominal'] ?? 0);
        $sheet->setCellValue('O' . $row, $pesanan['total_akhir'] ?? $pesanan['total_harga'] ?? 0);
        $sheet->setCellValue('P' . $row, $pesanan['dp_dibayar'] ?? 0);
        $sheet->setCellValue('Q' . $row, ucfirst($pesanan['status'] ?? ''));
        $sheet->setCellValue('R' . $row, $pesanan['metode_pembayaran'] ?? '');
        
        // Format tanggal pesan
        $tanggalPesan = '';
        if (!empty($pesanan['created_at'])) {
            try {
                $dateObj = new \DateTime($pesanan['created_at']);
                $tanggalPesan = $dateObj->format('d/m/Y H:i');
            } catch (\Exception $e) {
                $tanggalPesan = $pesanan['created_at'];
            }
        }
        $sheet->setCellValue('S' . $row, $tanggalPesan);
        
        $sheet->setCellValue('T' . $row, $pesanan['catatan_admin'] ?? '');
        
        $row++;
        $no++;
    }
    
    // Auto size columns
    foreach (range('A', 'T') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Set style untuk angka
    $lastRow = $row - 1;
    $sheet->getStyle('L2:O' . $lastRow)
          ->getNumberFormat()
          ->setFormatCode('#,##0');
    
    // Set style untuk kolom DP
    $sheet->getStyle('P2:P' . $lastRow)
          ->getNumberFormat()
          ->setFormatCode('#,##0');
    
    // Buat nama file
    $filename = 'Data_Pesanan_' . date('Ymd_His') . '.xlsx';
    
    // Set header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Buat writer dan output
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}
}