<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarModel extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    
    // Get events for calendar
    public function getCalendarEvents($start = null, $end = null, $status = null)
    {
        $builder = $this->db->table('pesanan p');
        $builder->select('p.*, pm.nama_paket, k.nama_kostum, 
                         CONCAT(p.nama_lengkap, " - ", 
                         COALESCE(pm.nama_paket, k.nama_kostum, "Layanan")) as title');
        
        // Join dengan tabel terkait
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        
        // Filter berdasarkan tanggal jika ada
        if ($start && $end) {
            $builder->where('p.tanggal_acara >=', $start);
            $builder->where('p.tanggal_acara <=', $end);
        }
        
        // Filter status jika ada
        if ($status) {
            if (is_array($status)) {
                $builder->whereIn('p.status', $status);
            } else {
                $builder->where('p.status', $status);
            }
        } else {
            // Default hanya tampilkan status aktif
            $builder->whereIn('p.status', ['dikonfirmasi', 'diproses', 'selesai']);
        }
        
        $builder->orderBy('p.tanggal_acara', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Format untuk fullCalendar
        $events = [];
        foreach ($results as $event) {
            $events[] = $this->formatEventForCalendar($event);
        }
        
        return $events;
    }
    
    // Get events by date range
    public function getEventsByDateRange($startDate, $endDate)
{
    $builder = $this->db->table('pesanan p');
    $builder->select('p.*, pm.nama_paket, k.nama_kostum');
    $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
    $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
    
    // Perbaiki filter tanggal - pastikan menggunakan DATE()
    $builder->where("DATE(p.tanggal_acara) >=", $startDate);
    $builder->where("DATE(p.tanggal_acara) <=", $endDate);
    
    // Tampilkan semua status atau filter tertentu
    // $builder->whereIn('p.status', ['dikonfirmasi', 'diproses', 'selesai', 'pending']);
    
    $builder->orderBy('p.tanggal_acara', 'ASC');
    
    $results = $builder->get()->getResultArray();
    
    log_message('debug', "CalendarModel - getEventsByDateRange: " . count($results) . " events found");
    
    return $results;
}
    
    // Get events for specific date
    public function getEventsByDate($date)
    {
        $builder = $this->db->table('pesanan p');
        $builder->select('p.*, pm.nama_paket, k.nama_kostum');
        $builder->join('paket_makeup pm', 'pm.id = p.paket_id', 'left');
        $builder->join('kostum k', 'k.id = p.kostum_id', 'left');
        $builder->where('DATE(p.tanggal_acara)', $date);
        $builder->whereIn('p.status', ['dikonfirmasi', 'diproses', 'selesai']);
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    // Get monthly summary
    public function getMonthlySummary($year, $month)
{
    $startDate = date("{$year}-{$month}-01");
    $endDate = date("{$year}-{$month}-t", strtotime($startDate));
    
    log_message('debug', "CalendarModel - getMonthlySummary for: {$year}-{$month}, Start: {$startDate}, End: {$endDate}");
    
    $builder = $this->db->table('pesanan');
    $builder->select("DATE(tanggal_acara) as date, 
                     COUNT(*) as total_events,
                     SUM(CASE WHEN status = 'dikonfirmasi' THEN 1 ELSE 0 END) as confirmed,
                     SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as processed,
                     SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as completed");
    $builder->where("DATE(tanggal_acara) >=", $startDate);
    $builder->where("DATE(tanggal_acara) <=", $endDate);
    // Pastikan filter status sama dengan getEventsByDateRange
    // $builder->whereIn('status', ['dikonfirmasi', 'diproses', 'selesai', 'pending']);
    $builder->groupBy("DATE(tanggal_acara)");
    
    $results = $builder->get()->getResultArray();
    
    log_message('debug', "CalendarModel - Monthly summary results: " . json_encode($results));
    
    $summary = [];
    foreach ($results as $row) {
        $summary[$row['date']] = $row;
    }
    
    return $summary;
}
    
    // Format event untuk fullCalendar
    private function formatEventForCalendar($event)
    {
        // Tentukan warna berdasarkan status
        $color = $this->getStatusColor($event['status']);
        $textColor = $this->getTextColor($event['status']);
        
        // Build description
        $description = $event['nama_lengkap'] . " - ";
        if ($event['paket_id']) {
            $description .= $event['nama_paket'];
        } elseif ($event['kostum_id']) {
            $description .= $event['nama_kostum'];
        } else {
            $description .= "Layanan";
        }
        
        // Build detail untuk tooltip
        $details = [];
        $details[] = "<strong>Pelanggan:</strong> " . $event['nama_lengkap'];
        $details[] = "<strong>WhatsApp:</strong> " . $event['no_whatsapp'];
        
        if ($event['paket_id']) {
            $details[] = "<strong>Paket:</strong> " . $event['nama_paket'];
        }
        
        if ($event['kostum_id']) {
            $details[] = "<strong>Kostum:</strong> " . $event['nama_kostum'];
        }
        
        $details[] = "<strong>Lokasi:</strong> " . $event['lokasi_acara'];
        $details[] = "<strong>Status:</strong> <span class='badge' style='background-color: {$color}; color: {$textColor};'>" . 
                    ucfirst($event['status']) . "</span>";
        
        return [
            'id' => $event['id'],
            'title' => $event['title'] ?? $event['nama_lengkap'],
            'start' => $event['tanggal_acara'] . 'T08:00:00',
            'end' => $event['tanggal_acara'] . 'T20:00:00',
            'color' => $color,
            'textColor' => $textColor,
            'extendedProps' => [
                'description' => $description,
                'details' => implode('<br>', $details),
                'customer' => $event['nama_lengkap'],
                'phone' => $event['no_whatsapp'],
                'location' => $event['lokasi_acara'],
                'status' => $event['status'],
                'service_type' => $event['jenis_layanan']
            ],
            'url' => base_url('admin/pesanan/detail/' . $event['id'])
        ];
    }
    
    // Get color berdasarkan status
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ffc107',      // Kuning
            'dikonfirmasi' => '#17a2b8', // Biru muda
            'diproses' => '#007bff',     // Biru
            'selesai' => '#28a745',      // Hijau
            'dibatalkan' => '#dc3545'    // Merah
        ];
        
        return $colors[$status] ?? '#6c757d'; // Abu-abu default
    }
    
    // Get text color berdasarkan status
    private function getTextColor($status)
    {
        $lightStatuses = ['pending', 'selesai']; // Status yang membutuhkan teks gelap
        return in_array($status, $lightStatuses) ? '#212529' : '#ffffff';
    }
    
    // Get statistics
    public function getCalendarStats()
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $next7Days = date('Y-m-d', strtotime('+7 days'));
        
        $builder = $this->db->table('pesanan');
        
        // Total events
        $total = $builder->countAllResults();
        
        // Today's events
        $builder->where('DATE(tanggal_acara)', $today);
        $builder->whereIn('status', ['dikonfirmasi', 'diproses']);
        $todayEvents = $builder->countAllResults();
        
        // Tomorrow's events
        $builder->resetQuery();
        $builder->where('DATE(tanggal_acara)', $tomorrow);
        $builder->whereIn('status', ['dikonfirmasi', 'diproses']);
        $tomorrowEvents = $builder->countAllResults();
        
        // Next 7 days events
        $builder->resetQuery();
        $builder->where('DATE(tanggal_acara) >=', $today);
        $builder->where('DATE(tanggal_acara) <=', $next7Days);
        $builder->whereIn('status', ['dikonfirmasi', 'diproses']);
        $next7DaysEvents = $builder->countAllResults();
        
        return [
            'total' => $total,
            'today' => $todayEvents,
            'tomorrow' => $tomorrowEvents,
            'next_7_days' => $next7DaysEvents
        ];
    }
}