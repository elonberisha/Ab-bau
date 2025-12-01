<?php
require_once 'functions.php';
requireLogin();

$pageTitle = 'Media Library';
$message = '';
$messageType = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
        $imagePath = uploadImage($_FILES['upload_file']);
        if ($imagePath) {
            $message = 'Fotoja u uploadua me sukses! Path: ' . $imagePath;
            $messageType = 'success';
        } else {
            $message = 'Gabim në upload të fotos!';
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $path = $_POST['path'] ?? '';
        if ($path && deleteImage($path)) {
            $message = 'Fotoja u fshi me sukses!';
            $messageType = 'success';
        } else {
            $message = 'Gabim në fshirjen e fotos!';
            $messageType = 'error';
        }
    }
}

// Get all uploaded images
$uploadDir = dirname(__DIR__) . '/uploads/';
$images = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $filePath = 'uploads/' . $file;
            $fullPath = $uploadDir . $file;
            $images[] = [
                'name' => $file,
                'path' => $filePath,
                'size' => filesize($fullPath),
                'date' => date('Y-m-d H:i:s', filemtime($fullPath))
            ];
        }
    }
    // Sort by date, newest first
    usort($images, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="ml-64 pt-16 p-6">
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Upload Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-upload text-primary mr-2"></i>
                Upload Foto të Re
            </h2>
            <form method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zgjidh Foto</label>
                    <input type="file" name="upload_file" accept="image/*" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <button type="submit" class="bg-white text-gray-900 border-2 border-gray-300 px-6 py-2 rounded-lg hover:bg-gray-50 hover:border-gray-400 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </form>
        </div>

        <!-- Media Library -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-images text-primary mr-2"></i>
                Media Library (<?php echo count($images); ?> foto)
            </h2>
            
            <?php if (empty($images)): ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 text-lg mb-2">Nuk ka foto të uploaduara</p>
                    <p class="text-gray-500 text-sm">Uploado foto duke përdorur formularin e sipërm</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php foreach ($images as $image): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-all bg-white">
                            <div class="relative">
                                <img src="../<?php echo htmlspecialchars($image['path']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['name']); ?>"
                                     class="w-full h-48 object-cover">
                                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                    <?php echo formatFileSize($image['size']); ?>
                                </div>
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium mb-2 text-gray-800 truncate" title="<?php echo htmlspecialchars($image['name']); ?>">
                                    <?php echo htmlspecialchars($image['name']); ?>
                                </p>
                                <p class="text-xs text-gray-500 mb-3"><?php echo htmlspecialchars($image['date']); ?></p>
                                
                                <!-- Path Input (Copyable) -->
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Path:</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
                                               value="<?php echo htmlspecialchars($image['path']); ?>" 
                                               readonly
                                               class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded bg-gray-50"
                                               id="path-<?php echo md5($image['path']); ?>">
                                        <button onclick="copyPath('<?php echo md5($image['path']); ?>')" 
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="flex space-x-2">
                                    <a href="gallery.php?use_image=<?php echo urlencode($image['path']); ?>" 
                                       class="flex-1 bg-primary text-white px-3 py-2 rounded text-sm hover:bg-primary-dark text-center">
                                        <i class="fas fa-plus mr-1"></i>Përdor në Galeri
                                    </a>
                                    <button onclick="deleteImage('<?php echo htmlspecialchars($image['path']); ?>', '<?php echo htmlspecialchars($image['name']); ?>')" 
                                            class="bg-red-500 text-white px-3 py-2 rounded text-sm hover:bg-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function copyPath(id) {
            const input = document.getElementById('path-' + id);
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            
            // Show feedback
            const button = input.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-blue-500');
            button.classList.add('bg-green-500');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500');
            }, 2000);
        }
        
        function deleteImage(path, name) {
            if (confirm('A jeni të sigurt që dëshironi ta fshini këtë foto?\n\n' + name)) {
                // Create a form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'media-library.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);
                
                const pathInput = document.createElement('input');
                pathInput.type = 'hidden';
                pathInput.name = 'path';
                pathInput.value = path;
                form.appendChild(pathInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

