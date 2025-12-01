<?php
require_once 'functions.php';
requireLogin();

$services = readJson('services.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Shërbimet';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $newService = [
            'id' => uniqid(),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'image' => sanitize($_POST['image'] ?? ''),
            'active' => isset($_POST['active'])
        ];
        
        $services[] = $newService;
        writeJson('services.json', $services);
        $message = 'Shërbimi u shtua me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
        $services = readJson('services.json');
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        foreach ($services as $key => $service) {
            if ($service['id'] === $id) {
                $services[$key]['title'] = sanitize($_POST['title'] ?? '');
                $services[$key]['description'] = sanitize($_POST['description'] ?? '');
                $services[$key]['image'] = sanitize($_POST['image'] ?? '');
                $services[$key]['active'] = isset($_POST['active']);
                writeJson('services.json', $services);
                $message = 'Shërbimi u përditësua me sukses! Ndryshimet reflektohen në index.html';
                $messageType = 'success';
                break;
            }
        }
        $services = readJson('services.json');
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        foreach ($services as $key => $service) {
            if ($service['id'] === $id) {
                unset($services[$key]);
                $services = array_values($services);
                writeJson('services.json', $services);
                $message = 'Shërbimi u fshi me sukses! Ndryshimet reflektohen në index.html';
                $messageType = 'success';
                break;
            }
        }
        $services = readJson('services.json');
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
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span>
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                </span>
                <a href="../index.html" target="_blank" class="text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 hover:underline font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i>Shiko Faqen
                </a>
            </div>
        <?php endif; ?>

        <!-- Add New Service Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Shto Shërbim të Ri
            </h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                    <input type="text" name="title" required placeholder="P.sh. Keramik"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi (path)</label>
                    <input type="text" name="image" required placeholder="P.sh. kiramika.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                    <textarea name="description" required rows="3" placeholder="Përshkrimi i shërbimit"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="active" checked>
                        <span>Aktiv</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Ruaj Shërbim
                    </button>
                </div>
            </form>
        </div>

        <!-- Services List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-list text-primary mr-2"></i>
                Lista e Shërbimeve (<?php echo count($services); ?>)
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Titulli</th>
                            <th class="px-4 py-3 text-left">Përshkrimi</th>
                            <th class="px-4 py-3 text-left">Imazhi</th>
                            <th class="px-4 py-3 text-left">Statusi</th>
                            <th class="px-4 py-3 text-left">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars($service['title']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($service['description']); ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($service['image']): ?>
                                        <img src="../<?php echo htmlspecialchars($service['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($service['title']); ?>"
                                             class="w-16 h-16 object-cover rounded">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($service['active']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Aktiv</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">Jo Aktiv</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)" 
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Informacion</h3>
                    <p class="text-blue-800 mb-3">
                        Shërbimet që shtoni ose ndryshoni këtu reflektohen automatikisht në <strong>index.html</strong> përmes API-s. 
                        Vetëm shërbimet aktive shfaqen në faqen publike.
                    </p>
                    <a href="../index.html" target="_blank" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-external-link-alt mr-2"></i>Shiko Faqen Publike
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Ndrysho Shërbimin</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                        <input type="text" name="title" id="editTitle" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi</label>
                        <input type="text" name="image" id="editImage" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                        <textarea name="description" id="editDescription" required rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="active" id="editActive">
                            <span>Aktiv</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Anulo
                    </button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark font-semibold shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Ruaj Ndryshimet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editService(service) {
            document.getElementById('editId').value = service.id;
            document.getElementById('editTitle').value = service.title;
            document.getElementById('editDescription').value = service.description;
            document.getElementById('editImage').value = service.image;
            document.getElementById('editActive').checked = service.active;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
