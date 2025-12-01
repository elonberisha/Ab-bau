<?php
require_once 'functions.php';
requireLogin();

$activities = readJson('activities.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Veprimtari';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_service') {
        $activityType = $_POST['activity_type'] ?? '';
        if (isset($activities[$activityType])) {
            $newService = [
                'id' => uniqid(),
                'title' => sanitize($_POST['title'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'image' => sanitize($_POST['image'] ?? ''),
                'active' => isset($_POST['active'])
            ];
            
            if (!isset($activities[$activityType]['services'])) {
                $activities[$activityType]['services'] = [];
            }
            $activities[$activityType]['services'][] = $newService;
            writeJson('activities.json', $activities);
            $message = 'Shërbimi u shtua me sukses!';
            $messageType = 'success';
            $activities = readJson('activities.json');
        }
    } elseif ($action === 'edit_service') {
        $activityType = $_POST['activity_type'] ?? '';
        $serviceId = $_POST['service_id'] ?? '';
        
        if (isset($activities[$activityType]['services'])) {
            foreach ($activities[$activityType]['services'] as $key => $service) {
                if ($service['id'] === $serviceId) {
                    $activities[$activityType]['services'][$key]['title'] = sanitize($_POST['title'] ?? '');
                    $activities[$activityType]['services'][$key]['description'] = sanitize($_POST['description'] ?? '');
                    $activities[$activityType]['services'][$key]['image'] = sanitize($_POST['image'] ?? '');
                    $activities[$activityType]['services'][$key]['active'] = isset($_POST['active']);
                    writeJson('activities.json', $activities);
                    $message = 'Shërbimi u përditësua me sukses!';
                    $messageType = 'success';
                    $activities = readJson('activities.json');
                    break;
                }
            }
        }
    } elseif ($action === 'delete_service') {
        $activityType = $_POST['activity_type'] ?? '';
        $serviceId = $_POST['service_id'] ?? '';
        
        if (isset($activities[$activityType]['services'])) {
            foreach ($activities[$activityType]['services'] as $key => $service) {
                if ($service['id'] === $serviceId) {
                    unset($activities[$activityType]['services'][$key]);
                    $activities[$activityType]['services'] = array_values($activities[$activityType]['services']);
                    writeJson('activities.json', $activities);
                    $message = 'Shërbimi u fshi me sukses!';
                    $messageType = 'success';
                    $activities = readJson('activities.json');
                    break;
                }
            }
        }
    } elseif ($action === 'toggle_activity') {
        $activityType = $_POST['activity_type'] ?? '';
        if (isset($activities[$activityType])) {
            $activities[$activityType]['active'] = !$activities[$activityType]['active'];
            writeJson('activities.json', $activities);
            $message = 'Statusi i veprimtarisë u përditësua!';
            $messageType = 'success';
            $activities = readJson('activities.json');
        }
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
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h1 class="text-3xl font-bold mb-6">Menaxho Veprimtari</h1>

        <?php foreach ($activities as $key => $activity): ?>
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <i class="<?php echo htmlspecialchars($activity['icon']); ?> text-3xl text-primary"></i>
                        <div>
                            <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($activity['title']); ?></h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($activity['description']); ?></p>
                        </div>
                    </div>
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="toggle_activity">
                        <input type="hidden" name="activity_type" value="<?php echo $key; ?>">
                        <button type="submit" class="px-4 py-2 rounded-lg <?php echo $activity['active'] ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700'; ?>">
                            <?php echo $activity['active'] ? 'Aktiv' : 'Jo Aktiv'; ?>
                        </button>
                    </form>
                </div>

                <!-- Add Service Form -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Shto Shërbim të Ri</h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="action" value="add_service">
                        <input type="hidden" name="activity_type" value="<?php echo $key; ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                            <input type="text" name="title" required placeholder="P.sh. Shtimi i qeramikës"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi (path)</label>
                            <input type="text" name="image" required placeholder="P.sh. uploads/foto.png"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <a href="media-library.php" target="_blank" class="text-xs text-primary hover:underline mt-1 block">
                                <i class="fas fa-external-link-alt mr-1"></i>Shiko Media Library
                            </a>
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
                            <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark font-semibold shadow-lg hover:shadow-xl transition-all">
                                <i class="fas fa-save mr-2"></i>Ruaj Shërbim
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Services List -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Shërbimet (<?php echo count($activity['services'] ?? []); ?>)</h3>
                    <?php if (empty($activity['services'])): ?>
                        <p class="text-gray-500 text-center py-4">Nuk ka shërbime për këtë veprimtari.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($activity['services'] as $service): ?>
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-all">
                                    <?php if ($service['image']): ?>
                                        <img src="../<?php echo htmlspecialchars($service['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($service['title']); ?>"
                                             class="w-full h-32 object-cover rounded mb-3">
                                    <?php endif; ?>
                                    <h4 class="font-bold mb-2"><?php echo htmlspecialchars($service['title']); ?></h4>
                                    <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($service['description']); ?></p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs px-2 py-1 rounded <?php echo $service['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $service['active'] ? 'Aktiv' : 'Jo Aktiv'; ?>
                                        </span>
                                        <div class="flex space-x-2">
                                            <button onclick="editService('<?php echo $key; ?>', <?php echo htmlspecialchars(json_encode($service)); ?>)" 
                                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                                <input type="hidden" name="action" value="delete_service">
                                                <input type="hidden" name="activity_type" value="<?php echo $key; ?>">
                                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Ndrysho Shërbimin</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit_service">
                <input type="hidden" name="activity_type" id="editActivityType">
                <input type="hidden" name="service_id" id="editServiceId">
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
        function editService(activityType, service) {
            document.getElementById('editActivityType').value = activityType;
            document.getElementById('editServiceId').value = service.id;
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

