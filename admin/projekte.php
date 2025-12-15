<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $title = sanitize($_POST['title']);
            $description = sanitize($_POST['description']);
            $image = sanitize($_POST['image']);
            $type = sanitize($_POST['type']); // e.g. 'residential', 'commercial'
            $date = sanitize($_POST['date']);
            $active = isset($_POST['active']) ? 1 : 0;
            
            if (empty($date)) $date = date('Y-m-d');

            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, type, date, active) VALUES (:title, :desc, :img, :type, :date, :active)");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'img' => $image, 'type' => $type, 'date' => $date, 'active' => $active])) {
                    $message = 'Projekti u shtua!';
                    $messageType = 'success';
                }
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE projects SET title = :title, description = :desc, image = :img, type = :type, date = :date, active = :active WHERE id = :id");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'img' => $image, 'type' => $type, 'date' => $date, 'active' => $active, 'id' => $id])) {
                    $message = 'Projekti u përditësua!';
                    $messageType = 'success';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                $message = 'Projekti u fshi!';
                $messageType = 'success';
            }
        }
    }
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Portfolion - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <script>
        function openModal(mode, data = null) {
            const modal = document.getElementById('projectModal');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');
            const btn = document.getElementById('modalBtn');
            
            modal.classList.remove('hidden');
            
            if (mode === 'edit' && data) {
                title.textContent = 'Ndrysho Projektin';
                btn.textContent = 'Ruaj Ndryshimet';
                form.elements['action'].value = 'update';
                form.elements['id'].value = data.id;
                form.elements['title'].value = data.title;
                form.elements['description'].value = data.description;
                form.elements['type'].value = data.type;
                form.elements['date'].value = data.date;
                form.elements['image'].value = data.image;
                form.elements['active'].checked = data.active == 1;
                
                document.getElementById('image_preview').src = data.image ? '../' + data.image : 'assets/img/placeholder.png';
            } else {
                title.textContent = 'Shto Projekt të Ri';
                btn.textContent = 'Krijo Projekt';
                form.reset();
                form.elements['action'].value = 'create';
                form.elements['id'].value = '';
                form.elements['date'].value = new Date().toISOString().split('T')[0];
                document.getElementById('image_preview').src = 'assets/img/placeholder.png';
            }
        }

        function closeModal() {
            document.getElementById('projectModal').classList.add('hidden');
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
                        <span class="bg-indigo-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-briefcase text-indigo-600"></i>
                        </span>
                        Portfolio (Projekte)
                    </h1>
                </div>
                <button onclick="openModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all text-sm">
                    <i class="fas fa-plus mr-2"></i> Shto Projekt
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

                <!-- Projects Grid -->
                <?php if (empty($projects)): ?>
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-folder-open text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Nuk ka projekte</h3>
                        <p class="text-gray-500 mt-1">Shtoni projektet tuaja të realizuara.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($projects as $proj): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group relative flex flex-col">
                                <div class="relative h-48 bg-gray-100">
                                    <img src="../<?php echo !empty($proj['image']) ? htmlspecialchars($proj['image']) : 'assets/img/placeholder.png'; ?>" 
                                         class="w-full h-full object-cover">
                                    
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-white/90 text-gray-800 text-xs font-bold px-2 py-1 rounded shadow-sm uppercase tracking-wide">
                                            <?php echo htmlspecialchars($proj['type']); ?>
                                        </span>
                                    </div>

                                    <?php if (!$proj['active']): ?>
                                        <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-bold border border-gray-300">Jo Aktiv</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-4 flex-1 flex flex-col">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($proj['title']); ?></h3>
                                    <div class="text-xs text-gray-500 mb-3 flex items-center">
                                        <i class="far fa-calendar-alt mr-1"></i> 
                                        <?php echo date('d.m.Y', strtotime($proj['date'])); ?>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-4 flex-1"><?php echo htmlspecialchars($proj['description']); ?></p>
                                    
                                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-auto">
                                        <button onclick='openModal("edit", <?php echo json_encode($proj); ?>)' class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-edit mr-1"></i>
                                        </button>
                                        <form method="POST" onsubmit="return confirm('Fshi projektin?');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $proj['id']; ?>">
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
    <div id="projectModal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Shto Projekt</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="projectForm" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titulli i Projektit</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Përshkrimi</label>
                        <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="residential">Rezidenciale</option>
                            <option value="commercial">Komerciale</option>
                            <option value="renovation">Renovim</option>
                            <option value="other">Tjetër</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data e Përfundimit</label>
                        <input type="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto e Projektit</label>
                        <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-primary transition-colors bg-gray-50 p-1" onclick="openMediaPicker('modal_image')">
                            <img src="assets/img/placeholder.png" id="image_preview" class="w-full h-48 object-cover rounded">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10">
                                <span class="bg-white text-gray-800 text-xs font-bold px-2 py-1 rounded shadow">Zgjidh</span>
                            </div>
                        </div>
                        <input type="hidden" id="modal_image" name="image">
                    </div>
                    
                    <div class="col-span-2 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-gray-700 font-medium">Projekt Aktiv</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Anulo
                    </button>
                    <button type="submit" id="modalBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Krijo Projekt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/media-picker.js"></script>
</body>
</html>