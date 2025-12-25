<?php

namespace App\Controllers;

class Home extends BaseController
{
    // Property untuk menyimpan instance model
    protected $paketModel;
    protected $kostumModel;
    protected $pengaturanModel;
    protected $mitraModel;
    protected $galleryModel;
    protected $areaModel;

    public function __construct()
    {
        // Inisialisasi model di constructor
        $this->paketModel = new \App\Models\PaketModel();
        $this->kostumModel = new \App\Models\KostumModel();
        $this->pengaturanModel = new \App\Models\PengaturanModel();
        $this->mitraModel = new \App\Models\MitraModel();
        $this->galleryModel = new \App\Models\GalleryModel();
        $this->areaModel = new \App\Models\AreaLayananModel();
    }

    public function index()
    {
        // Ambil warna dari database
        $colors = $this->getColors();

        $data = [
            'title' => 'Maulia - Professional Wedding Make Up Artist & Kostum Sewa - Grobogan',
            'paket_makeup' => $this->paketModel->where('is_active', 1)->findAll(),
            'kostum' => $this->kostumModel->where('is_active', 1)->findAll(),
            'hero_active' => true,
            // Kirim warna ke view
            'colors' => $colors
        ];

        return view('home', $data);
    }

    /**
     * Fungsi untuk mendapatkan semua warna yang dibutuhkan
     * Digunakan di semua method controller
     */
    private function getColors()
    {
        // Ambil warna dasar dari database
        $primary_color = $this->pengaturanModel->getByKey('primary_color', '#d4b8a3');
        $secondary_color = $this->pengaturanModel->getByKey('secondary_color', '#b8a7c8');
        
        // Generate warna lain dari warna utama
        $accent_color = $this->lightenColor($primary_color, 20);
        $accent_dark = $this->darkenColor($primary_color, 10);

        return [
            'primary' => $primary_color,
            'secondary' => $secondary_color,
            'accent' => $accent_color,
            'accent_dark' => $accent_dark,
            'costume' => $secondary_color,
            'text' => '#5a5a5a',
            'heading' => '#333333',
            'white' => '#ffffff',
            'shadow' => 'rgba(149, 157, 165, 0.1)',
            'success' => '#a8c8b8'
        ];
    }

    // Helper function untuk mencerahkan warna
    private function lightenColor($hex, $percent)
    {
        return $this->adjustColor($hex, abs($percent));
    }

    // Helper function untuk menggelapkan warna
    private function darkenColor($hex, $percent)
    {
        return $this->adjustColor($hex, -abs($percent));
    }

    // Helper function untuk mengatur kecerahan warna
    private function adjustColor($hex, $percent)
    {
        // Remove # jika ada
        $hex = str_replace('#', '', $hex);
        
        // Parse color jika format pendek
        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        
        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Adjust brightness
        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));
        
        // Convert back to hex
        return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                  .str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                  .str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    public function paketMakeup()
    {
        // Get settings
        $pengaturan = $this->pengaturanModel->getContactInfo();

        $data = [
            'title' => 'Paket Makeup Pernikahan - ' . ($pengaturan['nama_toko'] ?? 'Maulia Wedding'),
            'paket_makeup' => $this->paketModel->where('is_active', 1)
                ->orderBy('urutan', 'ASC')
                ->orderBy('harga', 'ASC')
                ->findAll(),
            'pengaturan' => $pengaturan,
            'meta_description' => $this->pengaturanModel->getByKey('meta_description', 'Pilih paket makeup pernikahan terbaik di Grobogan. Berbagai pilihan paket dari Basic hingga Royal dengan harga terjangkau.'),
            'meta_keywords' => $this->pengaturanModel->getByKey('meta_keywords', 'paket makeup pernikahan, harga makeup pengantin, makeup wedding grobogan, makeup pengantin murah'),
            // Kirim warna ke view
            'colors' => $this->getColors()
        ];

        // Decode features untuk setiap paket
        foreach ($data['paket_makeup'] as &$paket) {
            $paket['features'] = json_decode($paket['features'] ?? '[]', true);
        }

        return view('paket_makeup', $data);
    }

    public function sewaKostum()
    {
        // Get kategori filter jika ada
        $kategori = $this->request->getGet('kategori');

        $data = [
            'title' => 'Sewa Kostum Pernikahan - ' . ($this->pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
            'kostum' => $kategori ?
                $this->kostumModel->getByKategori($kategori) :
                $this->kostumModel->getAvailable(),
            'all_kostum' => $this->kostumModel->getAllWithFilter(null, 'active'),
            'kategori_aktif' => $kategori,
            'kategori_options' => $this->kostumModel->getKategoriOptions(),
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => $this->pengaturanModel->getByKey('meta_description', 'Sewa kostum pernikahan lengkap di Grobogan. Gaun pengantin, setelan pria, dan kostum keluarga dengan harga terjangkau.'),
            'meta_keywords' => $this->pengaturanModel->getByKey('meta_keywords', 'sewa kostum pengantin, gaun pengantin grobogan, setelan pengantin pria, sewa kebaya pengantin'),
            // Kirim warna ke view
            'colors' => $this->getColors()
        ];

        return view('sewa_kostum', $data);
    }

    public function detailKostum($slug)
    {
        $kostum = $this->kostumModel->getBySlug($slug);

        if (!$kostum) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get related kostum
        $related_kostum = $this->kostumModel->getRelated($kostum['id'], $kostum['kategori'], 4);

        $data = [
            'title' => $kostum['nama_kostum'] . ' - Sewa Kostum',
            'kostum' => $kostum,
            'related_kostum' => $related_kostum,
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => $this->character_limiter($kostum['deskripsi'], 160),
            'meta_keywords' => $kostum['nama_kostum'] . ', sewa kostum, ' . $kostum['kategori']
        ];

        return view('detail_kostum', $data);
    }

public function lokasi()
{
    // Get all settings
    $pengaturan = $this->pengaturanModel->getAllAsArray();
    
    // Get area layanan
    $area_layanan = $this->areaModel->where('is_active', 1)
                          ->orderBy('jenis_area', 'DESC')
                          ->orderBy('urutan', 'ASC')
                          ->findAll();
    
    $data = [
        'title' => 'Lokasi & Area Layanan - ' . ($pengaturan['nama_toko'] ?? 'Maulia Wedding'),
        'pengaturan' => $pengaturan,
        'area_layanan' => $area_layanan,
        'meta_description' => 'Lokasi studio Maulia di Grobogan dan area layanan makeup serta sewa kostum pernikahan di Jawa Tengah.',
        'meta_keywords' => 'lokasi makeup grobogan, area layanan wedding, studio makeup klambu, jangkauan layanan pernikahan',
        'latitude' => $pengaturan['latitude'] ?? '-7.0069338',
        'longitude' => $pengaturan['longitude'] ?? '110.7955922',
        // Tambahkan colors ke data
        'colors' => $this->getColors()
    ];
    
    return view('lokasi', $data);
}

    public function kirimPesan()
    {
        $pesananModel = new \App\Models\PesananModel();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_lengkap' => 'required',
            'no_whatsapp' => 'required',
            'jenis_layanan' => 'required',
            'tanggal_acara' => 'required|valid_date'
        ]);

        if ($validation->withRequest($this->request)->run()) {
            // Generate kode pesanan
            $kodePesanan = 'ORDER-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            $data = [
                'kode_pesanan' => $kodePesanan,
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'no_whatsapp' => $this->request->getPost('no_whatsapp'),
                'email' => $this->request->getPost('email'),
                'jenis_layanan' => $this->request->getPost('jenis_layanan'),
                'paket_id' => $this->request->getPost('paket_id'),
                'kostum_id' => $this->request->getPost('kostum_id'),
                'tanggal_acara' => $this->request->getPost('tanggal_acara'),
                'lokasi_acara' => $this->request->getPost('lokasi_acara'),
                'informasi_tambahan' => $this->request->getPost('informasi_tambahan'),
                'status' => 'pending'
            ];

            if ($pesananModel->save($data)) {
                // Kirim WhatsApp
                $this->kirimWhatsApp($data);

                return redirect()->to('/')->with('success', 'Pesanan berhasil dikirim! Kami akan menghubungi Anda via WhatsApp.');
            }
        }

        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    private function kirimWhatsApp($data)
    {
        // Logika pengiriman WhatsApp
        $message = "Halo Maulia, saya {$data['nama_lengkap']} ingin booking layanan pernikahan...";
        // Implementasi API WhatsApp
    }

    function character_limiter($text, $max_length, $ellipsis = true) {
        // Konversi ke string jika bukan string
        if (!is_string($text)) {
            $text = (string) $text;
        }
        
        // Jika teks sudah lebih pendek dari batas, langsung return
        if (mb_strlen($text) <= $max_length) {
            return $text;
        }
        
        // Potong teks
        if ($ellipsis) {
            return mb_substr($text, 0, $max_length - 3) . '...';
        } else {
            return mb_substr($text, 0, $max_length);
        }
    }

    public function mitra()
    {
        // Get filter parameters
        $kategori = $this->request->getGet('kategori');
        $search = $this->request->getGet('search');
        $spesialisasi = $this->request->getGet('spesialisasi');
        
        // Gunakan parameter array
        $filterParams = [
            'status' => 'active',
            'search' => $search,
            'spesialisasi' => $spesialisasi,
            'kategori' => $kategori
        ];
        
        $data = [
            'title' => 'Mitra Pernikahan - ' . ($this->pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
            'mitra' => $this->mitraModel->getAllWithFilter($filterParams),
            'kategori_options' => $this->mitraModel->getKategoriOptions(),
            'spesialisasi_options' => $this->mitraModel->getSpesialisasiOptions(),
            'kategori_aktif' => $kategori,
            'search_term' => $search,
            'spesialisasi_aktif' => $spesialisasi,
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => 'Temukan mitra pernikahan terpercaya di Grobogan. Fotografer, WO, catering, percetakan undangan, dekorasi, dan berbagai vendor pernikahan lainnya.',
            'meta_keywords' => 'mitra pernikahan, fotografer wedding grobogan, catering pernikahan, wo wedding, cetak undangan, dekorasi pernikahan',
            'colors' => $this->getColors()
        ];
        
        return view('mitra', $data);
    }

    public function detailMitra($slug)
    {
        $mitra = $this->mitraModel->getBySlug($slug);
        
        if (!$mitra) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => $mitra['nama_mitra'] . ' - Mitra Pernikahan',
            'mitra' => $mitra,
            'related_mitra' => $this->mitraModel->getRelated($mitra['id'], 4),
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => $this->character_limiter($mitra['deskripsi'], 160),
            'meta_keywords' => $mitra['nama_mitra'] . ', ' . $mitra['kategori'] . ', mitra pernikahan Grobogan'
        ];
        
        return view('detail_mitra', $data);
    }

    public function gallery()
    {
        // Get all filter parameters
        $kategori = $this->request->getGet('kategori');
        $search = $this->request->getGet('search');
        $style = $this->request->getGet('style');
        
        // Get gallery data with filters
        $gallery = $this->galleryModel->getAllWithFilter($kategori, 'active', $search, $style);
        
        // Prepare data for view
        $data = [
            'title' => 'Gallery Makeup & Kostum - ' . ($this->pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
            'gallery' => $gallery,
            'kategori_options' => $this->galleryModel->getKategoriOptions(),
            'style_options' => $this->galleryModel->getStyleOptions(),
            'kategori_aktif' => $kategori,
            'style_aktif' => $style,
            'search_term' => $search,
            'featured_gallery' => $this->galleryModel->getFeatured(8),
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => 'Gallery makeup pernikahan dan kostum dari Maulia Wedding. Lihat berbagai style makeup tradisional, modern, dan kontemporer untuk inspirasi pernikahan Anda.',
            'meta_keywords' => 'gallery makeup pengantin, foto makeup wedding, style makeup tradisional, makeup budaya Jawa, portfolio makeup artist',
            'colors' => $this->getColors()
        ];
        
        return view('gallery', $data);
    }

    public function detailGallery($id)
    {
        $gallery = $this->galleryModel->getWithRelated($id);
        
        if (!$gallery) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => $gallery['judul'] . ' - Gallery',
            'gallery' => $gallery,
            'related_gallery' => $this->galleryModel->getRelated($id, 6),
            'pengaturan' => $this->pengaturanModel->getContactInfo(),
            'meta_description' => $this->character_limiter($gallery['deskripsi'], 160),
            'meta_keywords' => $gallery['judul'] . ', ' . $gallery['kategori'] . ', ' . $gallery['style'] . ', makeup gallery'
        ];
        
        return view('detail_gallery', $data);
    }
}