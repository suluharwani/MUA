<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    protected $paketModel;
    protected $pesananModel;
    protected $kostumModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->paketModel = new \App\Models\PaketModel();
        $this->pesananModel = new \App\Models\PesananModel();
        $this->kostumModel = new \App\Models\KostumModel();
        $this->userModel = new \App\Models\UserModel();
    }
    
    public function index()
    {
        // Ambil data statistik dari masing-masing model
        $paketStats = $this->paketModel->getStatistics();
        $pesananStats = $this->pesananModel->getStatistics();
        $kostumStats = $this->kostumModel->getStatistics();
        
        // Data untuk chart
        $monthlyOrders = $this->pesananModel->getMonthlyCount(date('Y'));
        
        // Pesanan terbaru
        $latestOrders = $this->pesananModel->getLatest(5);
        
        // Kostum dengan stok menipis
        $lowStockCostumes = $this->kostumModel->where('stok_tersedia >', 0)
                                            ->where('stok_tersedia <=', 2)
                                            ->where('is_active', 1)
                                            ->orderBy('stok_tersedia', 'ASC')
                                            ->limit(5)
                                            ->findAll();
        
        // Acara mendatang
        $upcomingEvents = $this->pesananModel->getUpcomingEvents(7);
        
        $data = [
            'title' => 'Dashboard Admin',
            'paketStats' => $paketStats,
            'pesananStats' => $pesananStats,
            'kostumStats' => $kostumStats,
            'userCount' => $this->userModel->countUsers(),
            'monthlyOrders' => $monthlyOrders,
            'latestOrders' => $latestOrders,
            'lowStockCostumes' => $lowStockCostumes,
            'upcomingEvents' => $upcomingEvents,
            'systemInfo' => $this->getSystemInfo()
        ];
        
        return view('admin/dashboard/index', $data);
    }
    
    private function getSystemInfo()
    {
        return [
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'php_version' => phpversion(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'environment' => ENVIRONMENT,
            'timezone' => app_timezone(),
            'base_url' => base_url()
        ];
    }
    
    public function getChartData($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $data = $this->pesananModel->getMonthlyCount($year);
        
        return $this->response->setJSON($data);
    }
}