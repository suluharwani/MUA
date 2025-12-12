<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pesanan_id', 'kode_pembayaran', 'jumlah', 'metode', 'status',
        'bukti', 'catatan', 'tanggal_pembayaran', 'tanggal_verifikasi',
        'verified_by', 'jenis_pembayaran'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Generate kode pembayaran
    public function generateKodePembayaran()
    {
        $prefix = 'PAY';
        $date = date('ymd'); // 241212 untuk 12 Des 2024
        $random = mt_rand(1000, 9999);
        
        return $prefix . $date . $random;
    }

    // Get all payments for an order
    public function getByPesananId($pesanan_id)
    {
        $builder = $this->db->table('pembayaran p');
        $builder->select('p.*, u.nama as verifikator_nama');
        $builder->join('users u', 'u.id = p.verified_by', 'left');
        $builder->where('p.pesanan_id', $pesanan_id);
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    // Get total paid for an order
    public function getTotalPaid($pesanan_id)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('jumlah', 'total');
        $builder->where('pesanan_id', $pesanan_id);
        $builder->whereIn('status', ['diterima', 'lunas']);
        
        $result = $builder->get()->getRow();
        return $result ? (float)$result->total : 0;
    }

    // Verify payment
    public function verifyPayment($id, $status, $verified_by, $catatan = null)
    {
        $data = [
            'status' => $status,
            'tanggal_verifikasi' => date('Y-m-d H:i:s'),
            'verified_by' => $verified_by
        ];
        
        if ($catatan) {
            $data['catatan'] = $catatan;
        }
        
        return $this->update($id, $data);
    }

    // Get payment summary
    public function getPaymentSummary($pesanan_id)
    {
        $totalHarga = $this->db->table('pesanan')
            ->select('total_harga')
            ->where('id', $pesanan_id)
            ->get()
            ->getRow()
            ->total_harga ?? 0;
        
        $totalPaid = $this->getTotalPaid($pesanan_id);
        $remaining = $totalHarga - $totalPaid;
        
        // Get payment history
        $payments = $this->getByPesananId($pesanan_id);
        
        // Calculate DP percentage
        $dpPercentage = 50; // Default dari pengaturan
        $pengaturanModel = new PengaturanModel();
        $dpSetting = $pengaturanModel->where('key_name', 'dp_percentage')->first();
        if ($dpSetting) {
            $dpPercentage = (float)$dpSetting['value'];
        }
        
        $dpMinimum = $totalHarga * ($dpPercentage / 100);
        
        return [
            'total_harga' => $totalHarga,
            'total_dibayar' => $totalPaid,
            'sisa_pembayaran' => $remaining > 0 ? $remaining : 0,
            'dp_minimum' => $dpMinimum,
            'dp_percentage' => $dpPercentage,
            'riwayat' => $payments,
            'status_pembayaran' => $this->getPaymentStatus($totalHarga, $totalPaid, $dpMinimum)
        ];
    }

    private function getPaymentStatus($totalHarga, $totalPaid, $dpMinimum)
    {
        if ($totalPaid >= $totalHarga) {
            return 'lunas';
        } elseif ($totalPaid >= $dpMinimum) {
            return 'dp_lunas';
        } elseif ($totalPaid > 0) {
            return 'dp_sebagian';
        } else {
            return 'belum_dp';
        }
    }

    // Get payment by code
    public function getByKode($kode)
    {
        return $this->where('kode_pembayaran', $kode)->first();
    }

    // Get recent payments for dashboard
    public function getRecentPayments($limit = 10)
    {
        $builder = $this->db->table('pembayaran p');
        $builder->select('p.*, ps.kode_pesanan, ps.nama_lengkap, u.nama as verifikator_nama');
        $builder->join('pesanan ps', 'ps.id = p.pesanan_id', 'left');
        $builder->join('users u', 'u.id = p.verified_by', 'left');
        $builder->orderBy('p.created_at', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
}