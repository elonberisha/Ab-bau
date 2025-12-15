<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

// Handle Create/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $title = sanitize($_POST['title']);
            $description = sanitize($_POST['description']);
            $icon = sanitize($_POST['icon']);
            $image = sanitize($_POST['image']);
            $active = isset($_POST['active']) ? 1 : 0;
            
            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image, active) VALUES (:title, :desc, :icon, :img, :active)");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'icon' => $icon, 'img' => $image, 'active' => $active])) {
                    $message = 'Shërbimi u shtua me sukses!';
                    $messageType = 'success';
                } else {
                    $message = 'Gabim gjatë shtimit.';
                    $messageType = 'error';
                }
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE services SET title = :title, description = :desc, icon = :icon, image = :img, active = :active WHERE id = :id");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'icon' => $icon, 'img' => $image, 'active' => $active, 'id' => $id])) {
                    $message = 'Shërbimi u përditësua me sukses!';
                    $messageType = 'success';
                } else {
                    $message = 'Gabim gjatë përditësimit.';
                    $messageType = 'error';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                $message = 'Shërbimi u fshi me sukses!';
                $messageType = 'success';
            } else {
                $message = 'Gabim gjatë fshirjes.';
                $messageType = 'error';
            }
        }
    }
}

// Fetch Services
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Shërbimet - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <script>
        function openModal(mode, data = null) {
            const modal = document.getElementById('serviceModal');
            const form = document.getElementById('serviceForm');
            const title = document.getElementById('modalTitle');
            const btn = document.getElementById('modalBtn');
            
            modal.classList.remove('hidden');
            
            if (mode === 'edit' && data) {
                title.textContent = 'Ndrysho Shërbimin';
                btn.textContent = 'Ruaj Ndryshimet';
                form.elements['action'].value = 'update';
                form.elements['id'].value = data.id;
                form.elements['title'].value = data.title;
                form.elements['description'].value = data.description;
                form.elements['icon'].value = data.icon;
                form.elements['image'].value = data.image;
                form.elements['active'].checked = data.active == 1;
                
                // Update previews
                document.getElementById('image_preview').src = data.image ? '../' + data.image : 'assets/img/placeholder.png';
                document.getElementById('icon_preview').className = 'fas ' + (data.icon || 'fa-tools');
            } else {
                title.textContent = 'Shto Shërbim të Ri';
                btn.textContent = 'Krijo Shërbim';
                form.reset();
                form.elements['action'].value = 'create';
                form.elements['id'].value = '';
                document.getElementById('image_preview').src = 'assets/img/placeholder.png';
                document.getElementById('icon_preview').className = 'fas fa-tools';
            }
        }

        function closeModal() {
            document.getElementById('serviceModal').classList.add('hidden');
        }
        
        // Update icon preview on input change
        function updateIconPreview(input) {
            document.getElementById('icon_preview').className = 'fas ' + (input.value || 'fa-tools');
        }
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0 bg-white border-r border-gray-200">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-tools text-orange-600"></i>
                        </span>
                        Shërbimet (Services)
                    </h1>
                </div>
                <button onclick="openModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i> Shto Shërbim
                </button>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?> flex items-center shadow-sm animate-fade-in">
                        <div class="flex-shrink-0 mr-3">
                            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl"></i>
                        </div>
                        <span class="font-medium"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Services Grid -->
                <?php if (empty($services)): ?>
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-box-open text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Nuk ka shërbime të regjistruara</h3>
                        <p class="text-gray-500 mt-1">Filloni duke shtuar një shërbim të ri.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($services as $service): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group relative">
                                <div class="relative h-48 bg-gray-100">
                                    <?php if ($service['image']): ?>
                                        <img src="../<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-sm">
                                        <i class="fas <?php echo htmlspecialchars($service['icon']); ?> text-primary text-xl"></i>
                                    </div>
                                    
                                    <?php if (!$service['active']): ?>
                                        <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-bold border border-gray-300">Jo Aktiv</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 truncate"><?php echo htmlspecialchars($service['title']); ?></h3>
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-4 h-10"><?php echo htmlspecialchars($service['description']); ?></p>
                                    
                                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                                        <button onclick='openModal("edit", <?php echo json_encode($service); ?>)' class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-edit mr-1"></i> Ndrysho
                                        </button>
                                        <form method="POST" onsubmit="return confirm('A jeni i sigurt?');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modal -->
    <div id="serviceModal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Shto Shërbim</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="serviceForm" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titulli i Shërbimit</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Përshkrimi</label>
                        <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                    </div>

                    <!-- Icon Picker -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ikona (FontAwesome class)</label>
                        <div class="flex gap-2">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200">
                                <i id="icon_preview" class="fas fa-tools text-gray-600"></i>
                            </div>
                            <input type="text" name="icon" oninput="updateIconPreview(this)" placeholder="fa-tools" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Shembuj: fa-hammer, fa-paint-roller, fa-truck</p>
                    </div>

                    <!-- Image Picker -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imazhi Kryesor</label>
                        <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-primary transition-colors bg-gray-50 p-1" onclick="openMediaPicker('modal_image')">
                            <img src="assets/img/placeholder.png" id="image_preview" class="w-full h-32 object-cover rounded">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10">
                                <span class="bg-white text-gray-800 text-xs font-bold px-2 py-1 rounded shadow">Zgjidh</span>
                            </div>
                        </div>
                        <input type="hidden" id="modal_image" name="image">
                    </div>
                    
                    <div class="col-span-2 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-gray-700 font-medium">Shërbim Aktiv (shfaqet në faqe)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Anulo
                    </button>
                    <button type="submit" id="modalBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Krijo Shërbim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/media-picker.js"></script>
</body>
</html>