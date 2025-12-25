<?php
// app/Helpers/logo_helper.php

if (!function_exists('get_logo')) {
    function get_logo($type = 'logo', $default = null)
    {
        $model = new \App\Models\PengaturanModel();
        $logo = $model->getByKey($type);
        
        if (!$logo && $default) {
            return base_url($default);
        }
        
        return $logo ? base_url($logo) : null;
    }
}

if (!function_exists('get_logo_html')) {
    function get_logo_html($class = '', $style = '', $dark = false)
    {
        $type = $dark ? 'logo_dark' : 'logo';
        $model = new \App\Models\PengaturanModel();
        $logo = $model->getByKey($type);
        
        if (!$logo) {
            $logo = $model->getByKey('logo'); // Fallback ke logo utama
        }
        
        if ($logo) {
            $width = $model->getByKey('logo_width', '150');
            $height = $model->getByKey('logo_height', 'auto');
            
            $style_attr = "width: {$width}px; height: {$height};";
            if ($style) {
                $style_attr .= ' ' . $style;
            }
            
            return '<img src="' . base_url($logo) . '" 
                    alt="' . $model->getByKey('nama_toko', 'Logo') . '" 
                    class="' . $class . '" 
                    style="' . $style_attr . '">';
        }
        
        return '<span class="' . $class . '">' . $model->getByKey('nama_toko', 'Logo') . '</span>';
    }
}