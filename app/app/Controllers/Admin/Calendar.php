<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CalendarModel;

class Calendar extends BaseController
{
    protected $calendarModel;
    
    public function __construct()
    {
        $this->calendarModel = new CalendarModel();
    }
    
    public function index()
    {
        // Get current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Get selected year and month from request or use current
        $selectedYear = $this->request->getGet('year') ?? $currentYear;
        $selectedMonth = $this->request->getGet('month') ?? $currentMonth;
        
        // Validate year and month
        $selectedYear = is_numeric($selectedYear) ? (int)$selectedYear : $currentYear;
        $selectedMonth = is_numeric($selectedMonth) ? (int)$selectedMonth : $currentMonth;
        
        // Ensure month is between 1-12
        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = $currentMonth;
        }
        
        // Ensure year is reasonable
        if ($selectedYear < 2020 || $selectedYear > 2100) {
            $selectedYear = $currentYear;
        }
        
        // Get months list for dropdown
        $months = $this->getMonthsList();
        
        // Get years list for dropdown
        $years = $this->getYearsList();
        
        // Calculate start and end date for the selected month
        $startDate = date("{$selectedYear}-{$selectedMonth}-01");
        $endDate = date("{$selectedYear}-{$selectedMonth}-t", strtotime($startDate));
        
        // Get events for the selected month
        $events = $this->calendarModel->getEventsByDateRange($startDate, $endDate);
        if (!empty($events)) {
    foreach ($events as &$event) {
        // Set default values for missing keys
        $event['status'] = $event['status'] ?? 'pending';
        $event['jenis_layanan'] = $event['jenis_layanan'] ?? 'makeup';
        $event['nama_lengkap'] = $event['nama_lengkap'] ?? 'Tidak diketahui';
        $event['lokasi_acara'] = $event['lokasi_acara'] ?? 'Belum ditentukan';
        $event['kode_pesanan'] = $event['kode_pesanan'] ?? '';
    }
    unset($event); // Unset reference
}
        // Get monthly summary
        $monthlySummary = $this->calendarModel->getMonthlySummary($selectedYear, $selectedMonth);
        
        $data = [
            'title' => 'Kalender Acara',
            'stats' => $this->calendarModel->getCalendarStats(),
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'months' => $months,
            'years' => $years,
            'events' => $events,
            'monthlySummary' => $monthlySummary,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        return view('admin/calendar/index', $data);
    }
    
    // Get events for fullCalendar (AJAX)
    public function getEvents()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $status = $this->request->getGet('status');
        
        try {
            $events = $this->calendarModel->getCalendarEvents($start, $end, $status);
            
            return $this->response->setJSON($events);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Calendar::getEvents: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data acara'
            ]);
        }
    }
    
    // Get events by date
    public function getEventsByDate($date = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $date = $date ?: $this->request->getGet('date');
        
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        try {
            $events = $this->calendarModel->getEventsByDate($date);
            
            return $this->response->setJSON([
                'success' => true,
                'date' => $date,
                'events' => $events,
                'count' => count($events)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Calendar::getEventsByDate: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }
    
    // Get monthly summary
    public function getMonthlySummary($year = null, $month = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $year = $year ?: date('Y');
        $month = $month ?: date('m');
        
        try {
            $summary = $this->calendarModel->getMonthlySummary($year, $month);
            
            return $this->response->setJSON([
                'success' => true,
                'year' => $year,
                'month' => $month,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Calendar::getMonthlySummary: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data bulanan'
            ]);
        }
    }
    
    // Helper: Get list of months
    private function getMonthsList()
    {
        return [
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
    }
    
    // Helper: Get list of years
    private function getYearsList()
    {
        $currentYear = date('Y');
        $years = [];
        
        // Generate 5 years back and 2 years forward
        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
            $years[$i] = $i;
        }
        
        return $years;
    }
    
    // Redirect to selected month
    public function changeMonth()
    {
        $year = $this->request->getPost('year') ?? date('Y');
        $month = $this->request->getPost('month') ?? date('m');
        
        return redirect()->to(base_url("admin/calendar?year={$year}&month={$month}"));
    }
}