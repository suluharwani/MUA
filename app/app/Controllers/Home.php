<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $paketModel = new \App\Models\PaketModel();
        $kostumModel = new \App\Models\KostumModel();

        $data = [
            'title' => 'Maulia - Professional Wedding Make Up Artist & Kostum Sewa - Grobogan',
            'paket_makeup' => $paketModel->where('is_active', 1)->findAll(),
            'kostum' => $kostumModel->where('is_active', 1)->findAll(),
            'hero_active' => true
        ];

        return view('home', $data);
    }


    public function paketMakeup()
    {
        $paketModel = new \App\Models\PaketModel();
        $pengaturanModel = new \App\Models\PengaturanModel();

        // Get settings
        $pengaturan = $pengaturanModel->getContactInfo();

        $data = [
            'title' => 'Paket Makeup Pernikahan - ' . ($pengaturan['nama_toko'] ?? 'Maulia Wedding'),
            'paket_makeup' => $paketModel->where('is_active', 1)
                ->orderBy('urutan', 'ASC')
                ->orderBy('harga', 'ASC')
                ->findAll(),
            'pengaturan' => $pengaturan,
            'meta_description' => $pengaturanModel->getByKey('meta_description', 'Pilih paket makeup pernikahan terbaik di Grobogan. Berbagai pilihan paket dari Basic hingga Royal dengan harga terjangkau.'),
            'meta_keywords' => $pengaturanModel->getByKey('meta_keywords', 'paket makeup pernikahan, harga makeup pengantin, makeup wedding grobogan, makeup pengantin murah')
        ];

        // Decode features untuk setiap paket
        foreach ($data['paket_makeup'] as &$paket) {
            $paket['features'] = json_decode($paket['features'] ?? '[]', true);
        }

        return view('paket_makeup', $data);
    }

    public function sewaKostum()
    {
        $kostumModel = new \App\Models\KostumModel();
        $pengaturanModel = new \App\Models\PengaturanModel();

        // Get kategori filter jika ada
        $kategori = $this->request->getGet('kategori');

        $data = [
            'title' => 'Sewa Kostum Pernikahan - ' . ($pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
            'kostum' => $kategori ?
                $kostumModel->getByKategori($kategori) :
                $kostumModel->getAvailable(),
            'all_kostum' => $kostumModel->getAllWithFilter(null, 'active'),
            'kategori_aktif' => $kategori,
            'kategori_options' => $kostumModel->getKategoriOptions(),
            'pengaturan' => $pengaturanModel->getContactInfo(),
            'meta_description' => $pengaturanModel->getByKey('meta_description', 'Sewa kostum pernikahan lengkap di Grobogan. Gaun pengantin, setelan pria, dan kostum keluarga dengan harga terjangkau.'),
            'meta_keywords' => $pengaturanModel->getByKey('meta_keywords', 'sewa kostum pengantin, gaun pengantin grobogan, setelan pengantin pria, sewa kebaya pengantin')
        ];

        return view('sewa_kostum', $data);
    }

    public function detailKostum($slug)
    {
        $kostumModel = new \App\Models\KostumModel();
        $pengaturanModel = new \App\Models\PengaturanModel();

        $kostum = $kostumModel->getBySlug($slug);

        if (!$kostum) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $kostum['nama_kostum'] . ' - Sewa Kostum',
            'kostum' => $kostum,
            'related_kostum' => $kostumModel->getRelated($kostum['id'], 4),
            'pengaturan' => $pengaturanModel->getContactInfo(),
            'meta_description' => $this->character_limiter($kostum['deskripsi'], 160),
            'meta_keywords' => $kostum['nama_kostum'] . ', sewa kostum, ' . $kostum['kategori']
        ];

        return view('detail_kostum', $data);
    }
public function lokasi()
{
    $pengaturanModel = new \App\Models\PengaturanModel();
    $areaModel = new \App\Models\AreaLayananModel();
    
    // Get all settings
    $pengaturan = $pengaturanModel->getAllAsArray();
    
    // Get area layanan
    $area_layanan = $areaModel->where('is_active', 1)
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
        'longitude' => $pengaturan['longitude'] ?? '110.7955922'
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
    $mitraModel = new \App\Models\MitraModel();
    $pengaturanModel = new \App\Models\PengaturanModel();
    
    // Get kategori filter jika ada
    $kategori = $this->request->getGet('kategori');
    $search = $this->request->getGet('search');
    
    $data = [
        'title' => 'Mitra Pernikahan - ' . ($pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
        'mitra' => $mitraModel->getAllWithFilter($kategori, 'active', $search),
        'kategori_options' => $mitraModel->getKategoriOptions(),
        'kategori_aktif' => $kategori,
        'search_term' => $search,
        'pengaturan' => $pengaturanModel->getContactInfo(),
        'meta_description' => 'Temukan mitra pernikahan terpercaya di Grobogan. Fotografer, WO, catering, percetakan undangan, dekorasi, dan berbagai vendor pernikahan lainnya.',
        'meta_keywords' => 'mitra pernikahan, fotografer wedding grobogan, catering pernikahan, wo wedding, cetak undangan, dekorasi pernikahan'
    ];
    
    return view('mitra', $data);
}

public function detailMitra($slug)
{
    $mitraModel = new \App\Models\MitraModel();
    $pengaturanModel = new \App\Models\PengaturanModel();
    
    $mitra = $mitraModel->getBySlug($slug);
    
    if (!$mitra) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    
    $data = [
        'title' => $mitra['nama_mitra'] . ' - Mitra Pernikahan',
        'mitra' => $mitra,
        'related_mitra' => $mitraModel->getRelated($mitra['id'], 4),
        'pengaturan' => $pengaturanModel->getContactInfo(),
        'meta_description' => character_limiter($mitra['deskripsi'], 160),
        'meta_keywords' => $mitra['nama_mitra'] . ', ' . $mitra['kategori'] . ', mitra pernikahan Grobogan'
    ];
    
    return view('detail_mitra', $data);
}
// app/Controllers/Home.php (Tambahkan method)

public function gallery()
{
    $galleryModel = new \App\Models\GalleryModel();
    $pengaturanModel = new \App\Models\PengaturanModel();
    
    // Get all filter parameters
    $kategori = $this->request->getGet('kategori');
    $search = $this->request->getGet('search');
    $style = $this->request->getGet('style');
    
    // Get gallery data with filters
    $gallery = $galleryModel->getAllWithFilter($kategori, 'active', $search, $style);
    
    // Get active style from request
    $style_aktif = $style;
    
    // Prepare data for view
    $data = [
        'title' => 'Gallery Makeup & Kostum - ' . ($pengaturanModel->getByKey('nama_toko') ?? 'Maulia Wedding'),
        'gallery' => $gallery,
        'kategori_options' => $galleryModel->getKategoriOptions(),
        'style_options' => $galleryModel->getStyleOptions(),
        'kategori_aktif' => $kategori,
        'style_aktif' => $style_aktif,
        'search_term' => $search,
        'featured_gallery' => $galleryModel->getFeatured(8),
        'pengaturan' => $pengaturanModel->getContactInfo(),
        'meta_description' => 'Gallery makeup pernikahan dan kostum dari Maulia Wedding. Lihat berbagai style makeup tradisional, modern, dan kontemporer untuk inspirasi pernikahan Anda.',
        'meta_keywords' => 'gallery makeup pengantin, foto makeup wedding, style makeup tradisional, makeup budaya Jawa, portfolio makeup artist'
    ];
    
    return view('gallery', $data);
}

public function detailGallery($id)
{
    $galleryModel = new \App\Models\GalleryModel();
    $pengaturanModel = new \App\Models\PengaturanModel();
    
    $gallery = $galleryModel->getWithRelated($id);
    
    if (!$gallery) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    
    $data = [
        'title' => $gallery['judul'] . ' - Gallery',
        'gallery' => $gallery,
        'related_gallery' => $galleryModel->getRelated($id, 6),
        'pengaturan' => $pengaturanModel->getContactInfo(),
        'meta_description' => character_limiter($gallery['deskripsi'], 160),
        'meta_keywords' => $gallery['judul'] . ', ' . $gallery['kategori'] . ', ' . $gallery['style'] . ', makeup gallery'
    ];
    
    return view('detail_gallery', $data);
}
}
