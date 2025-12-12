<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketMakeupModel extends Model
{
    protected $table = 'paket_makeup';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_paket', 'slug', 'deskripsi', 'harga', 'durasi', 'features', 'urutan', 'is_active', 'is_featured', 'meta_keywords', 'meta_description'];
    protected $useTimestamps = true;
}