<?php
namespace App\Helpers;
if (!function_exists('handle_kostum_image')) {
    function handle_kostum_image($fieldName = 'gambar', $existingImage = null)
    {
        $gambar = service('request')->getFile($fieldName);
        
        if (!$gambar || !$gambar->isValid()) {
            return $existingImage;
        }
        
        // Validasi
        if ($gambar->getSize() > 2097152) {
            throw new \RuntimeException('Ukuran gambar maksimal 2MB');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($gambar->getMimeType(), $allowedTypes)) {
            throw new \RuntimeException('Format gambar tidak didukung');
        }
        
        // Generate nama unik
        $newName = $gambar->getRandomName();
        $gambar->move(ROOTPATH . 'public/uploads/kostum', $newName);
        
        // Hapus gambar lama jika ada
        if ($existingImage && file_exists(ROOTPATH . 'public/uploads/kostum/' . $existingImage)) {
            unlink(ROOTPATH . 'public/uploads/kostum/' . $existingImage);
        }
        
        return $newName;
    }
}