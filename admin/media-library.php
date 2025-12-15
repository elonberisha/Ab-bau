<?php
require_once 'functions.php';
requireLogin();

$pageTitle = 'Media Library';
$message = '';
$messageType = '';

// Get current folder
$currentFolder = isset($_GET['folder']) ? sanitize($_GET['folder']) : '';
$currentFolderPath = $currentFolder ? 'uploads/' . $currentFolder : 'uploads';

// Handle folder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_folder') {
    $folderName = sanitize($_POST['folder_name'] ?? '');
    if (empty($folderName)) {
        $message = 'Emri i folderit nuk mund të jetë bosh!';
        $messageType = 'error';
    } else {
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $folderName);
        $folderPath = dirname(__DIR__) . '/' . $currentFolderPath . '/' . $folderName;
        
        if (file_exists($folderPath)) {
            $message = 'Ky folder ekziston tashmë!';
            $messageType = 'error';
        } else {
            if (mkdir($folderPath, 0755, true)) {
                $redirectUrl = 'media-library.php?success=folder_created';
                if ($currentFolder) $redirectUrl .= '&folder=' . urlencode($currentFolder);
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                $message = 'Gabim në krijimin e folderit!';
                $messageType = 'error';
            }
        }
    }
}

// Handle folder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_folder') {
    $folderToDelete = sanitize($_POST['folder_path'] ?? '');
    // Security checks...
    $folderPath = str_replace('\\', '/', $folderToDelete);
    $folderPath = ltrim($folderPath, './\\');
    
    // Ensure relative to root
    $fullPath = dirname(__DIR__) . '/uploads/' . $folderPath;
    
    if (is_dir($fullPath)) {
        $files = array_diff(scandir($fullPath), ['.', '..']);
        if (empty($files)) {
            if (@rmdir($fullPath)) {
                header('Location: media-library.php?success=folder_deleted');
                exit;
            }
        } else {
            $message = 'Folderi nuk është bosh! Fshini fillimisht fotot.';
            $messageType = 'error';
        }
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
        $customFilename = sanitize($_POST['custom_filename'] ?? '');
        // Determine upload folder relative to uploads/
        $targetSubFolder = $currentFolder ?: ''; 
        $targetFolder = $targetSubFolder ? 'uploads/' . $targetSubFolder : 'uploads';
        
        $result = uploadImage($_FILES['upload_file'], $targetFolder, $customFilename);
        
        if ($result['success']) {
            $redirectUrl = 'media-library.php?success=uploaded&path=' . urlencode($result['path']);
            if ($currentFolder) $redirectUrl .= '&folder=' . urlencode($currentFolder);
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $message = $result['error'];
            $messageType = 'error';
        }
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $path = sanitize($_POST['path'] ?? '');
    if (deleteImage($path)) {
        $redirectUrl = 'media-library.php?success=deleted';
        if ($currentFolder) $redirectUrl .= '&folder=' . urlencode($currentFolder);
        header('Location: ' . $redirectUrl);
        exit;
    } else {
        $message = 'Gabim në fshirjen e fotos ose foto nuk u gjet.';
        $messageType = 'error';
    }
}

// Get directory contents
$currentDir = dirname(__DIR__) . '/' . $currentFolderPath;
$folders = [];
$images = [];

if (is_dir($currentDir)) {
    $items = scandir($currentDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $itemPath = $currentDir . '/' . $item;
        $relativePath = $currentFolderPath . '/' . $item;
        
        if (is_dir($itemPath)) {
            $imageCount = 0; // Simplified count
            $folders[] = [
                'name' => $item,
                'path' => $currentFolder ? $currentFolder . '/' . $item : $item,
                'image_count' => $imageCount
            ];
        } elseif (is_file($itemPath)) {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $images[] = [
                    'name' => $item,
                    'path' => $relativePath, // e.g. uploads/folder/img.jpg
                    'size' => filesize($itemPath),
                    'date' => date('Y-m-d H:i:s', filemtime($itemPath))
                ];
            }
        }
    }
}

// Breadcrumbs
$breadcrumbs = [];
if ($currentFolder) {
    $parts = explode('/', $currentFolder);
    $path = '';
    foreach ($parts as $part) {
        $path .= ($path ? '/' : '') . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $path];
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' bytes';
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
</head>
<body class="bg-gray-100">
    <?php 
    $isPickerMode = isset($_GET['picker']) && $_GET['picker'] === 'true';
    
    // Layout Structure
    if (!$isPickerMode): ?>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">Media Library</h1>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
    <?php else: ?>
    <!-- Picker Mode Layout -->
    <div class="h-screen flex flex-col bg-gray-50">
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800">Zgjidhni një Foto</h1>
            <button onclick="window.close()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </header>
        <main class="flex-1 overflow-y-auto p-6">
    <?php endif; ?>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Breadcrumbs & Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center text-sm font-medium">
                <a href="media-library.php<?php echo $isPickerMode ? '?picker=true' : ''; ?>" class="text-gray-500 hover:text-primary flex items-center">
                    <i class="fas fa-home mr-1"></i> Home
                </a>
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <span class="text-gray-300 mx-2">/</span>
                    <a href="media-library.php?folder=<?php echo urlencode($crumb['path']); ?><?php echo $isPickerMode ? '&picker=true' : ''; ?>" class="text-primary hover:underline">
                        <?php echo htmlspecialchars($crumb['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="text-xs text-gray-400">
                <?php echo count($folders); ?> Folders &bull; <?php echo count($images); ?> Images
            </div>
        </div>

        <!-- Actions Toolbar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Create Folder Form -->
                <form method="POST" class="flex-1 flex gap-2">
                    <input type="hidden" name="action" value="create_folder">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-folder-plus text-gray-400"></i>
                        </div>
                        <input type="text" name="folder_name" placeholder="Krijo Folder të Ri..." required 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors border border-gray-200">
                        Krijo
                    </button>
                </form>
                
                <!-- Separator -->
                <div class="hidden md:block w-px bg-gray-200"></div>
                
                <!-- Upload Form -->
                <form method="POST" enctype="multipart/form-data" class="flex-[2] flex gap-2 items-center">
                    <div class="flex-1">
                        <input type="file" name="upload_file" accept="image/*" required 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm flex items-center">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload
                    </button>
                </form>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            <!-- Folders List -->
            <?php foreach ($folders as $folder): ?>
                <div class="group relative">
                    <a href="media-library.php?folder=<?php echo urlencode($folder['path']); ?><?php echo $isPickerMode ? '&picker=true' : ''; ?>" 
                       class="block bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:shadow-md hover:border-yellow-400 transition-all h-full flex flex-col items-center justify-center">
                        <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-folder text-5xl text-yellow-400 drop-shadow-sm"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700 truncate w-full px-2" title="<?php echo htmlspecialchars($folder['name']); ?>">
                            <?php echo htmlspecialchars($folder['name']); ?>
                        </p>
                        <span class="text-[10px] text-gray-400 mt-1"><?php echo $folder['image_count']; ?> items</span>
                    </a>
                    
                    <!-- Delete Folder Button (Only visible on hover if empty) -->
                    <?php if ($folder['image_count'] == 0): ?>
                    <form method="POST" onsubmit="return confirm('Fshi folderin?');" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <input type="hidden" name="action" value="delete_folder">
                        <input type="hidden" name="folder_path" value="<?php echo htmlspecialchars($folder['path']); ?>">
                        <button type="submit" class="text-gray-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Images List -->
            <?php foreach ($images as $image): ?>
                <div class="group relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-50 relative overflow-hidden">
                        <img src="../<?php echo htmlspecialchars($image['path']); ?>" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        
                        <!-- Actions Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                            <?php if ($isPickerMode): ?>
                                <button onclick="selectImage('<?php echo htmlspecialchars($image['path']); ?>')" class="bg-green-500 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-600 shadow-lg transform hover:scale-110 transition-all" title="Zgjidh">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="copyUrl('<?php echo htmlspecialchars($image['path']); ?>')" class="bg-white text-gray-700 w-10 h-10 rounded-full flex items-center justify-center hover:text-blue-600 hover:bg-blue-50 shadow-lg transition-all" title="Kopjo URL">
                                    <i class="fas fa-link"></i>
                                </button>
                                <form method="POST" onsubmit="return confirm('A jeni i sigurt që doni ta fshini këtë foto?');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="path" value="<?php echo htmlspecialchars($image['path']); ?>">
                                    <button type="submit" class="bg-white text-gray-700 w-10 h-10 rounded-full flex items-center justify-center hover:text-red-600 hover:bg-red-50 shadow-lg transition-all" title="Fshi">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-3 bg-white border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-700 truncate mb-1" title="<?php echo htmlspecialchars($image['name']); ?>">
                            <?php echo htmlspecialchars($image['name']); ?>
                        </p>
                        <p class="text-[10px] text-gray-400"><?php echo formatFileSize($image['size']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($folders) && empty($images)): ?>
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Folderi është bosh</h3>
                <p class="text-sm text-gray-500 mt-1">Krijoni një folder të ri ose ngarkoni foto.</p>
            </div>
        <?php endif; ?>

        <!-- End Main Content -->
        </main>
    </div>

    <script>
        function copyUrl(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('URL u kopjua në clipboard!');
            });
        }

        function selectImage(path) {
            if (window.opener && window.opener.handleMediaSelect) {
                window.opener.handleMediaSelect(path);
                window.close();
            } else {
                alert('Gabim: Dritarja kryesore nuk u gjet!');
            }
        }
    </script>
</body>
</html>