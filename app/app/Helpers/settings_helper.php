<?php

use App\Models\PengaturanModel;

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     */
    function setting($key, $default = null)
    {
        static $settings = null;
        
        if ($settings === null) {
            $model = new PengaturanModel();
            $settings = $model->getAllAsArray();
        }
        
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('settings')) {
    /**
     * Get multiple settings
     */
    function settings(array $keys)
    {
        $model = new PengaturanModel();
        return $model->getByKeys($keys);
    }
}

if (!function_exists('setting_categories')) {
    /**
     * Get setting categories
     */
    function setting_categories()
    {
        $model = new PengaturanModel();
        return $model->getCategories();
    }
}