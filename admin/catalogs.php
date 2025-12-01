<?php
require_once 'functions.php';
requireLogin();

$catalogs = readJson('catalogs.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Katalogje';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $newCatalog = [
            'id' => uniqid(),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'cover_image' => sanitize($_POST['cover_image'] ?? ''),
            'category' => sanitize($_POST['category'] ?? ''),
            'products' => [],
            'active' => isset($_POST['active']),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if (!isset($catalogs['catalogs'])) {
            $catalogs['catalogs'] = [];
        }
        $catalogs['catalogs'][] = $newCatalog;
        writeJson('catalogs.json', $catalogs);
        $message = 'Katalogu u shtua me sukses!';
        $messageType = 'success';
        $catalogs = readJson('catalogs.json');
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $id) {
                    $catalogs['catalogs'][$key]['title'] = sanitize($_POST['title'] ?? '');
                    $catalogs['catalogs'][$key]['description'] = sanitize($_POST['description'] ?? '');
                    $catalogs['catalogs'][$key]['cover_image'] = sanitize($_POST['cover_image'] ?? '');
                    $catalogs['catalogs'][$key]['category'] = sanitize($_POST['category'] ?? '');
                    $catalogs['catalogs'][$key]['active'] = isset($_POST['active']);
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Katalogu u përditësua me sukses!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $id) {
                    unset($catalogs['catalogs'][$key]);
                    $catalogs['catalogs'] = array_values($catalogs['catalogs']);
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Katalogu u fshi me sukses!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'add_product') {
        $catalogId = $_POST['catalog_id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $catalogId) {
                    $newProduct = [
                        'id' => uniqid(),
                        'name' => sanitize($_POST['name'] ?? ''),
                        'description' => sanitize($_POST['product_description'] ?? ''),
                        'image' => sanitize($_POST['product_image'] ?? ''),
                        'specifications' => sanitize($_POST['specifications'] ?? ''),
                        'active' => isset($_POST['product_active'])
                    ];
                    
                    if (!isset($catalogs['catalogs'][$key]['products'])) {
                        $catalogs['catalogs'][$key]['products'] = [];
                    }
                    $catalogs['catalogs'][$key]['products'][] = $newProduct;
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Produkti u shtua me sukses!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'delete_product') {
        $catalogId = $_POST['catalog_id'] ?? '';
        $productId = $_POST['product_id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $catalogId && isset($catalog['products'])) {
                    foreach ($catalog['products'] as $pKey => $product) {
                        if ($product['id'] === $productId) {
                            unset($catalogs['catalogs'][$key]['products'][$pKey]);
                            $catalogs['catalogs'][$key]['products'] = array_values($catalogs['catalogs'][$key]['products']);
                            writeJson('catalogs.json', $catalogs);
                            $message = 'Produkti u fshi me sukses!';
                            $messageType = 'success';
                            $catalogs = readJson('catalogs.json');
                            break 2;
                        }
                    }
                }
            }
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

        <!-- Add New Catalog Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Shto Katalog të Ri
            </h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                    <input type="text" name="title" required placeholder="P.sh. Katalogu i Qeramikës 2024"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategoria</label>
                    <input type="text" name="category" required placeholder="P.sh. Qeramikë, Mermer, Granit"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi i Kopertinës (path)</label>
                    <input type="text" name="cover_image" required placeholder="P.sh. uploads/katalog-cover.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <a href="media-library.php" target="_blank" class="text-xs text-primary hover:underline mt-1 block">
                        <i class="fas fa-external-link-alt mr-1"></i>Shiko Media Library
                    </a>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                    <textarea name="description" required rows="3" placeholder="Përshkrimi i katalogut"
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
                        <i class="fas fa-save mr-2"></i>Ruaj Katalog
                    </button>
                </div>
            </form>
        </div>

        <!-- Catalogs List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-book text-primary mr-2"></i>
                Lista e Katalogjeve (<?php echo count($catalogs['catalogs'] ?? []); ?>)
            </h2>
            
            <?php if (empty($catalogs['catalogs'])): ?>
                <p class="text-gray-500 text-center py-8">Nuk ka katalogje për momentin.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($catalogs['catalogs'] as $catalog): ?>
                        <div class="border rounded-lg p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start space-x-4 flex-1">
                                    <?php if ($catalog['cover_image']): ?>
                                        <img src="../<?php echo htmlspecialchars($catalog['cover_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($catalog['title']); ?>"
                                             class="w-24 h-24 object-cover rounded">
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($catalog['title']); ?></h3>
                                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($catalog['description']); ?></p>
                                        <div class="flex items-center space-x-4 text-sm">
                                            <span class="text-gray-500">Kategoria: <strong><?php echo htmlspecialchars($catalog['category']); ?></strong></span>
                                            <span class="text-gray-500">Produkte: <strong><?php echo count($catalog['products'] ?? []); ?></strong></span>
                                            <span class="px-2 py-1 rounded text-xs <?php echo $catalog['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                                <?php echo $catalog['active'] ? 'Aktiv' : 'Jo Aktiv'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editCatalog(<?php echo htmlspecialchars(json_encode($catalog)); ?>)" 
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $catalog['id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Add Product Form -->
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold mb-3">Shto Produkt të Ri</h4>
                                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="hidden" name="action" value="add_product">
                                    <input type="hidden" name="catalog_id" value="<?php echo $catalog['id']; ?>">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Emri i Produktit</label>
                                        <input type="text" name="name" required placeholder="P.sh. Qeramikë Premium"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi (path)</label>
                                        <input type="text" name="product_image" required placeholder="P.sh. uploads/produkt.png"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                                        <textarea name="product_description" required rows="2" placeholder="Përshkrimi i produktit"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Specifikimet</label>
                                        <input type="text" name="specifications" placeholder="P.sh. 30x30cm, Anti-slip"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="product_active" checked>
                                            <span class="text-sm">Aktiv</span>
                                        </label>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark font-semibold text-sm">
                                            <i class="fas fa-plus mr-2"></i>Shto Produkt
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Products List -->
                            <?php if (!empty($catalog['products'])): ?>
                                <div class="mt-4">
                                    <h4 class="font-semibold mb-3">Produktet (<?php echo count($catalog['products']); ?>)</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <?php foreach ($catalog['products'] as $product): ?>
                                            <div class="border rounded-lg p-2 hover:shadow-md transition-all">
                                                <?php if ($product['image']): ?>
                                                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                         class="w-full h-24 object-cover rounded mb-2">
                                                <?php endif; ?>
                                                <h5 class="font-semibold text-sm mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="catalog_id" value="<?php echo $catalog['id']; ?>">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="w-full bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">
                                                        <i class="fas fa-trash mr-1"></i>Fshi
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Catalog Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Ndrysho Katalogun</h2>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategoria</label>
                        <input type="text" name="category" id="editCategory" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi i Kopertinës</label>
                        <input type="text" name="cover_image" id="editCoverImage" required
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
        function editCatalog(catalog) {
            document.getElementById('editId').value = catalog.id;
            document.getElementById('editTitle').value = catalog.title;
            document.getElementById('editDescription').value = catalog.description;
            document.getElementById('editCoverImage').value = catalog.cover_image;
            document.getElementById('editCategory').value = catalog.category;
            document.getElementById('editActive').checked = catalog.active;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>

