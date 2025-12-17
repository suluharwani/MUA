<?php

if (!function_exists('upload_multiple_files')) {
    /**
     * Upload multiple files
     *
     * @param array $files Array of uploaded files
     * @param string $path Upload directory path
     * @param array $allowedTypes Allowed file types
     * @param int $maxSize Maximum file size in bytes
     * @return array Array of uploaded file names
     */
    function upload_multiple_files($files, $path, $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'], $maxSize = 5242880)
    {
        $uploadedFiles = [];
        
        if (!is_array($files)) {
            return $uploadedFiles;
        }
        
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                // Validate file type
                $fileType = $file->getClientExtension();
                if (!in_array(strtolower($fileType), $allowedTypes)) {
                    continue;
                }
                
                // Validate file size
                if ($file->getSize() > $maxSize) {
                    continue;
                }
                
                // Generate new name
                $newName = $file->getRandomName();
                
                // Move file
                if ($file->move($path, $newName)) {
                    $uploadedFiles[] = $newName;
                }
            }
        }
        
        return $uploadedFiles;
    }
}

if (!function_exists('delete_file')) {
    /**
     * Delete file from server
     *
     * @param string $filePath Full path to file
     * @return bool True if deleted successfully
     */
    function delete_file($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}