<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function getDataPath($file) {
    return dirname(__DIR__) . '/data/' . $file;
}

function readJson($file) {
    $path = getDataPath($file);
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

function writeJson($file, $data) {
    $path = getDataPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    
    if (empty($name) || empty($message) || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Të gjitha fushat duhen plotësuar!']);
        exit;
    }
    
    $reviews = readJson('reviews.json');
    
    $newReview = [
        'id' => uniqid(),
        'name' => $name,
        'message' => $message,
        'rating' => $rating,
        'date' => date('Y-m-d H:i:s')
    ];
    
    $reviews['pending'][] = $newReview;
    writeJson('reviews.json', $reviews);
    
    echo json_encode(['success' => true, 'message' => 'Review u dërgua me sukses! Do të shqyrtrohet nga admin.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Metodë e gabuar']);
}
?>

