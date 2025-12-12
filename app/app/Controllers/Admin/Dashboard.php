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
        $currentYear = date('Y');
        
        // Ambil data statistik dari masing-masing model
        $paketStats = $this->paketModel->getStatistics();
        $pesananStats = $this->pesananModel->getStatistics();
        $kostumStats = $this->kostumModel->getStatistics();
        
        // Data untuk chart - tahun saat ini (2025)
        $monthlyOrders = $this->pesananModel->getMonthlyCount($currentYear);
        
        // Format data untuk chart
        $chartData = $this->formatChartData($monthlyOrders);
        
        // Statistik tahunan
        $yearlyStats = $this->pesananModel->getYearlySummary($currentYear);
        
        // Statistik per bulan tahun ini
        $monthlyStats = $this->getMonthlyStats($currentYear);
        
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
        $upcomingEvents = $this->pesananModel->getUpcomingEvents(7, 5);
        
        $data = [
            'title' => 'Dashboard Admin',
            'current_year' => $currentYear,
            'paketStats' => $paketStats,
            'pesananStats' => array_merge($pesananStats, [
                'yearly_summary' => $yearlyStats
            ]),
            'kostumStats' => $kostumStats,
            'userCount' => $this->userModel->countUsers(),
            'monthlyOrders' => $chartData,
            'monthlyStats' => $monthlyStats,
            'latestOrders' => $latestOrders,
            'lowStockCostumes' => $lowStockCostumes,
            'upcomingEvents' => $upcomingEvents,
            'systemInfo' => $this->getSystemInfo()
        ];
        
        return view('admin/dashboard/index', $data);
    }
    
    private function formatChartData($monthlyOrders)
    {
        $months = [];
        $orderCounts = [];
        $revenues = [];
        $completedOrders = [];
        
        foreach ($monthlyOrders as $monthData) {
            $months[] = $monthData['nama_bulan'];
            $orderCounts[] = $monthData['jumlah_pesanan'];
            $revenues[] = $monthData['total_pendapatan'];
            $completedOrders[] = $monthData['pesanan_selesai'];
        }
        
        return [
            'months' => $months,
            'order_counts' => $orderCounts,
            'revenues' => $revenues,
            'completed_orders' => $completedOrders
        ];
    }
    
    private function getMonthlyStats($year)
    {
        $monthlyData = $this->pesananModel->getMonthlyCount($year);
        
        $stats = [];
        foreach ($monthlyData as $month) {
            $stats[$month['bulan']] = [
                'nama_bulan' => $month['nama_bulan'],
                'total_pesanan' => $month['jumlah_pesanan'],
                'total_pendapatan' => $month['total_pendapatan'],
                'pesanan_selesai' => $month['pesanan_selesai'],
                'pesanan_dibatalkan' => $month['pesanan_dibatalkan'],
                'konversi_rate' => $month['jumlah_pesanan'] > 0 ? 
                    round(($month['pesanan_selesai'] / $month['jumlah_pesanan']) * 100, 1) : 0
            ];
        }
        
        return $stats;
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
        
        $monthlyOrders = $this->pesananModel->getMonthlyCount($year);
        
        // Format data untuk chart.js
        $data = $this->formatChartData($monthlyOrders);
        
        return $this->response->setJSON($data);
    }
    
    // Method baru untuk statistik tahunan
    public function getYearlyStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $yearlyStats = $this->pesananModel->getYearlySummary($year);
        $monthlyStats = $this->getMonthlyStats($year);
        
        $data = [
            'yearly' => $yearlyStats,
            'monthly' => $monthlyStats,
            'top_months' => $this->getTopMonths($year)
        ];
        
        return $this->response->setJSON($data);
    }
    
    private function getTopMonths($year)
    {
        $monthlyData = $this->pesananModel->getMonthlyCount($year);
        
        // Urutkan berdasarkan total pesanan
        usort($monthlyData, function($a, $b) {
            return $b['jumlah_pesanan'] - $a['jumlah_pesanan'];
        });
        
        // Ambil top 3 bulan
        return array_slice($monthlyData, 0, 3);
    }
}