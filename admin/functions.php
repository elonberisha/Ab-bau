<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
require_once __DIR__ . '/includes/db_connect.php';

// --- AUTHENTICATION FUNCTIONS ---

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

function verifyUserCredentials($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        $hash = isset($user['password_hash']) ? $user['password_hash'] : ($user['password'] ?? null);
        if ($hash && password_verify($password, $hash)) {
            return $user;
        }
    }
    return false;
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

function updateUserPassword($userId, $newPassword) {
    global $pdo;
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $result = $stmt->execute(['hash' => $hash, 'id' => $userId]);
    } catch (PDOException $e) {
        $stmt = $pdo->prepare("UPDATE users SET password = :hash WHERE id = :id");
        $result = $stmt->execute(['hash' => $hash, 'id' => $userId]);
    }
    return $result;
}

// --- DATA MANAGEMENT FUNCTIONS ---

function getSectionData($table) {
    global $pdo;
    $allowedTables = ['hero_section', 'about_section', 'contact_section', 'legal_section'];
    if (!in_array($table, $allowedTables)) return [];

    try {
        $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
        $data = $stmt->fetch();
        return $data ? $data : [];
    } catch (PDOException $e) {
        return [];
    }
}

function updateSectionData($table, $data) {
    global $pdo;
    $allowedTables = ['hero_section', 'about_section', 'contact_section', 'legal_section'];
    if (!in_array($table, $allowedTables)) return false;

    $stmt = $pdo->query("SELECT id FROM $table LIMIT 1");
    $exists = $stmt->fetch();

    if ($exists) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        $params['id'] = $exists['id'];
        $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } else {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function uploadImage($file, $folder = 'uploads', $customName = '') {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) return ['success' => false, 'error' => 'Format i palejuar.'];

    $targetDir = dirname(__DIR__) . '/' . $folder . '/';
    if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = !empty($customName) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $customName) . '.' . $extension : uniqid() . '_' . time() . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        return ['success' => true, 'path' => $folder . '/' . $filename];
    }
    return ['success' => false, 'error' => 'Gabim gjatë ngarkimit.'];
}

function deleteImage($path) {
    if (empty($path)) return true;
    $realPath = realpath(dirname(__DIR__) . '/' . $path);
    if ($realPath && file_exists($realPath) && strpos($realPath, realpath(dirname(__DIR__))) === 0) {
        return unlink($realPath);
    }
    return false;
}

// --- UPDATED STATS FUNCTION ---

function getStats() {
    global $pdo;
    
    // Initialize default values to avoid "Undefined array key" errors
    $stats = [
        'projects' => 0,
        'services' => 0,
        'catalogs' => 0,
        'reviews_total' => 0,
        'reviews_pending' => 0
    ];

    try {
        // Projects
        $stats['projects'] = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

        // Services
        $stats['services'] = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
        
        // Catalogs
        $stats['catalogs'] = $pdo->query("SELECT COUNT(*) FROM catalogs")->fetchColumn();

        // Reviews Total
        $stats['reviews_total'] = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
        
        // Reviews Pending
        $stats['reviews_pending'] = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();
        
    } catch (PDOException $e) {
        // In case of error (e.g. table missing), values remain 0
    }

    return $stats;
}
?>