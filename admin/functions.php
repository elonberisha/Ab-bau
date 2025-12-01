<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Get data directory path
function getDataPath($file) {
    return dirname(__DIR__) . '/data/' . $file;
}

// Read JSON file
function readJson($file) {
    $path = getDataPath($file);
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

// Write JSON file
function writeJson($file, $data) {
    $path = getDataPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Verify password
function verifyPassword($password) {
    $config = readJson('config.json');
    if (isset($config['password_hash']) && !empty($config['password_hash'])) {
        $verified = password_verify($password, $config['password_hash']);
        if ($verified) {
            return true;
        }
    }
    // Fallback for default password (if hash verification fails or no hash exists)
    if (isset($config['default_password'])) {
        return $password === $config['default_password'];
    }
    // Final fallback
    return $password === 'admin123';
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Update password
function updatePassword($newPassword) {
    $config = readJson('config.json');
    $config['password_hash'] = hashPassword($newPassword);
    return writeJson('config.json', $config);
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Upload image
function uploadImage($file, $folder = 'uploads') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowed)) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = dirname(__DIR__) . '/' . $folder . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $folder . '/' . $filename;
    }
    
    return false;
}

// Delete image file
function deleteImage($path) {
    $fullPath = dirname(__DIR__) . '/' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

// Get statistics
function getStats() {
    $gallery = readJson('gallery.json');
    $services = readJson('services.json');
    $reviews = readJson('reviews.json');
    $activities = readJson('activities.json');
    $catalogs = readJson('catalogs.json');
    
    $totalActivityServices = 0;
    foreach ($activities as $activity) {
        $totalActivityServices += count($activity['services'] ?? []);
    }
    
    return [
        'home_images' => count($gallery['home'] ?? []),
        'portfolio_images' => count($gallery['portfolio'] ?? []),
        'services' => count($services),
        'activities' => count(array_filter($activities, function($a) { return $a['active'] ?? false; })),
        'activity_services' => $totalActivityServices,
        'catalogs' => count($catalogs['catalogs'] ?? []),
        'pending_reviews' => count($reviews['pending'] ?? []),
        'approved_reviews' => count($reviews['approved'] ?? [])
    ];
}

