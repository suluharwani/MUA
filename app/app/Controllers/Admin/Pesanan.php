<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pesanan extends BaseController
{
    protected $pesananModel;
    
    public function __construct()
    {
        $this->pesananModel = new \App\Models\PesananModel();
    }
    
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        
        $data = [];
        
        if ($search) {
            $data['pesanan'] = $this->pesananModel->search($search);
        } elseif ($status) {
            $data['pesanan'] = $this->pesananModel->getByStatus($status);
        } else {
            $data['pesanan'] = $this->pesananModel->getPesananWithRelations();
        }
        
        $data['title'] = 'Kelola Pesanan';
        $data['stats'] = $this->pesananModel->getStatistics();
        $data['status_filter'] = $status;
        $data['search_term'] = $search;
        
        return view('admin/pesanan/index', $data);
    }
    
    public function detail($id)
    {
        $pesanan = $this->pesananModel->getPesananWithRelations($id);
        
        if (!$pesanan) {
            return redirect()->to('/admin/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Detail Pesanan #' . $pesanan['kode_pesanan'],
            'pesanan' => $pesanan
        ];
        
        return view('admin/pesanan/detail', $data);
    }
    
    public function ubahStatus($id, $status)
    {
        $allowedStatus = ['pending', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan'];
        
        if (!in_array($status, $allowedStatus)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }
        
        $catatan = $this->request->getPost('catatan');
        
        if ($this->pesananModel->updateStatus($id, $status, $catatan)) {
            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
        }
        
        return redirect()->back()->with('error', 'Gagal memperbarui status pesanan.');
    }
    
    public function updatePembayaran($id)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'dp_dibayar' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'metode_pembayaran' => 'required'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $dp_dibayar = $this->request->getPost('dp_dibayar');
        $total_harga = $this->request->getPost('total_harga');
        $metode = $this->request->getPost('metode_pembayaran');
        
        // Handle upload bukti pembayaran
        $buktiPembayaran = null;
        $file = $this->request->getFile('bukti_pembayaran');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/bukti-pembayaran', $newName);
            $buktiPembayaran = $newName;
        }
        
        if ($this->pesananModel->updatePembayaran($id, $dp_dibayar, $total_harga, $metode, $buktiPembayaran)) {
            return redirect()->back()->with('success', 'Informasi pembayaran berhasil diperbarui.');
        }
        
        return redirect()->back()->with('error', 'Gagal memperbarui informasi pembayaran.');
    }
    
    public function tambahCatatan($id)
    {
        $catatan = $this->request->getPost('catatan_admin');
        
        if ($this->pesananModel->update($id, ['catatan_admin' => $catatan])) {
            return redirect()->back()->with('success', 'Catatan berhasil disimpan.');
        }
        
        return redirect()->back()->with('error', 'Gagal menyimpan catatan.');
    }
    
    public function export($type = 'excel')
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        
        $pesanan = $this->pesananModel->getForReport($startDate, $endDate, $status);
        
        if ($type == 'excel') {
            return $this->exportExcel($pesanan, $startDate, $endDate);
        } else {
            return $this->exportPDF($pesanan, $startDate, $endDate);
        }
    }
    
    private function exportExcel($data, $startDate, $endDate)
    {
        // Implementasi export Excel menggunakan library
        // Contoh menggunakan PhpSpreadsheet
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="laporan_pesanan.xlsx"');
        
        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'Laporan Pesanan Maulia Wedding');
        $sheet->setCellValue('A2', 'Periode: ' . ($startDate ? $startDate : 'Semua') . ' - ' . ($endDate ? $endDate : 'Semua'));
        $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i'));
        
        // Tabel header
        $headers = ['No', 'Kode', 'Nama', 'WhatsApp', 'Layanan', 'Paket', 'Kostum', 'Tanggal Acara', 'Total', 'DP', 'Status', 'Tanggal Pesan'];
        $col = 'A';
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        
        // Data
        $row = 6;
        $no = 1;
        
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_pesanan']);
            $sheet->setCellValue('C' . $row, $item['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $item['no_whatsapp']);
            $sheet->setCellValue('E' . $row, $item['jenis_layanan']);
            $sheet->setCellValue('F' . $row, $item['paket_nama'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['kostum_nama'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['tanggal_acara']);
            $sheet->setCellValue('I' . $row, $item['total_harga'] ?? 0);
            $sheet->setCellValue('J' . $row, $item['dp_dibayar'] ?? 0);
            $sheet->setCellValue('K' . $row, $item['status']);
            $sheet->setCellValue('L' . $row, $item['created_at']);
            
            $row++;
        }
        
        // Format kolom
        $sheet->getStyle('A5:L5')->getFont()->setBold(true);
        $sheet->getStyle('I6:J' . ($row-1))->getNumberFormat()->setFormatCode('#,##0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    private function exportPDF($data, $startDate, $endDate)
    {
        // Implementasi export PDF
        // Contoh menggunakan Dompdf
        $html = view('admin/pesanan/export_pdf', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_pesanan.pdf', ['Attachment' => true]);
        exit;
    }
    
    public function delete($id)
    {
        if ($this->pesananModel->delete($id)) {
            return redirect()->to('/admin/pesanan')->with('success', 'Pesanan berhasil dihapus.');
        }
        
        return redirect()->to('/admin/pesanan')->with('error', 'Gagal menghapus pesanan.');
    }
    
    public function calendar()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');
        
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
    }
    
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ffc107', // Kuning
            'dikonfirmasi' => '#17a2b8', // Biru
            'diproses' => '#007bff', // Biru tua
            'selesai' => '#28a745', // Hijau
            'dibatalkan' => '#dc3545' // Merah
        ];
        
        return $colors[$status] ?? '#6c757d';
    }
}