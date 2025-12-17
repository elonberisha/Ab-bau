<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // --- PARTNER CRUD ---
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $name = sanitize($_POST['name']);
            $logo = sanitize($_POST['logo']);
            $website = sanitize($_POST['website']);
            $description = sanitize($_POST['description']);
            $active = isset($_POST['active']) ? 1 : 0;
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO partners (name, logo, website, description, active, sort_order) VALUES (:name, :logo, :website, :desc, :active, :sort_order)");
                if ($stmt->execute(['name' => $name, 'logo' => $logo, 'website' => $website, 'desc' => $description, 'active' => $active, 'sort_order' => $sort_order])) {
                    $_SESSION['message'] = 'Partner wurde erfolgreich hinzugefügt!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Fehler beim Hinzufügen.';
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE partners SET name = :name, logo = :logo, website = :website, description = :desc, active = :active, sort_order = :sort_order WHERE id = :id");
                if ($stmt->execute(['name' => $name, 'logo' => $logo, 'website' => $website, 'desc' => $description, 'active' => $active, 'sort_order' => $sort_order, 'id' => $id])) {
                    $_SESSION['message'] = 'Partner wurde erfolgreich aktualisiert!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Fehler beim Aktualisieren.';
                    $_SESSION['message_type'] = 'error';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM partners WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                $_SESSION['message'] = 'Partner wurde erfolgreich gelöscht!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Fehler beim Löschen.';
                $_SESSION['message_type'] = 'error';
            }
        }
        
        header("Location: partners.php");
        exit;
    }
}

// Fetch Partners
$partners = $pdo->query("SELECT * FROM partners ORDER BY sort_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <script>
        function openMediaPicker(fieldId) {
            window.open('media-library.php?select=1&field=' + fieldId, 'MediaPicker', 'width=900,height=600,scrollbars=yes');
        }

        function openModal(mode, data = null) {
            const modal = document.getElementById('partnerModal');
            const form = document.getElementById('partnerForm');
            const title = document.getElementById('modalTitle');
            const btn = document.getElementById('modalBtn');
            
            modal.classList.remove('hidden');
            
            if (mode === 'edit' && data) {
                title.textContent = 'Partner bearbeiten';
                btn.textContent = 'Änderungen speichern';
                form.elements['action'].value = 'update';
                form.elements['id'].value = data.id;
                form.elements['name'].value = data.name || '';
                form.elements['logo'].value = data.logo || '';
                form.elements['website'].value = data.website || '';
                form.elements['description'].value = data.description || '';
                form.elements['active'].checked = data.active == 1;
                form.elements['sort_order'].value = data.sort_order || 0;
                
                // Update preview
                document.getElementById('logo_preview').src = data.logo ? '../' + data.logo : '../assets/img/placeholder.png';
            } else {
                title.textContent = 'Neuer Partner hinzufügen';
                btn.textContent = 'Partner erstellen';
                form.reset();
                form.elements['action'].value = 'create';
                form.elements['id'].value = '';
                form.elements['sort_order'].value = 0;
                document.getElementById('logo_preview').src = '../assets/img/placeholder.png';
            }
        }

        function closeModal() {
            document.getElementById('partnerModal').classList.add('hidden');
        }

        // Update logo preview when input changes
        function updateLogoPreview(input) {
            const preview = document.getElementById('logo_preview');
            if (input.value) {
                preview.src = '../' + input.value;
            } else {
                preview.src = '../assets/img/placeholder.png';
            }
        }

        // Handle media picker callback
        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'media-selected') {
                const fieldId = e.data.field;
                const imagePath = e.data.path;
                document.getElementById(fieldId).value = imagePath;
                updateLogoPreview(document.getElementById(fieldId));
            }
        });
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
                        <span class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-handshake text-blue-600"></i>
                        </span>
                        Partner
                    </h1>
                </div>
                <button onclick="openModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i> Partner hinzufügen
                </button>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Success/Error Messages -->
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?> animate-fade-in">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                                <span><?php echo htmlspecialchars($message); ?></span>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Partners List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Partner Liste</h2>
                        <p class="text-sm text-gray-500 mt-1">Verwalten Sie Ihre Partner und Kooperationspartner</p>
                    </div>
                    
                    <div class="p-6">
                        <?php if (empty($partners)): ?>
                            <div class="text-center py-12">
                                <i class="fas fa-handshake text-gray-300 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">Noch keine Partner vorhanden</p>
                                <p class="text-gray-400 text-sm mb-6">Fügen Sie Ihren ersten Partner hinzu</p>
                                <button onclick="openModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                                    <i class="fas fa-plus mr-2"></i> Partner hinzufügen
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($partners as $partner): ?>
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5 hover:shadow-md transition-all">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-lg text-gray-900 mb-2"><?php echo htmlspecialchars($partner['name']); ?></h3>
                                                <?php if ($partner['logo']): ?>
                                                    <div class="mb-3">
                                                        <img src="../<?php echo htmlspecialchars($partner['logo']); ?>" 
                                                             alt="<?php echo htmlspecialchars($partner['name']); ?>" 
                                                             class="max-h-20 max-w-full object-contain">
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($partner['website']): ?>
                                                    <a href="<?php echo htmlspecialchars($partner['website']); ?>" 
                                                       target="_blank" 
                                                       class="text-sm text-primary hover:underline flex items-center mb-2">
                                                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                                        Website besuchen
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($partner['description']): ?>
                                                    <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($partner['description']); ?></p>
                                                <?php endif; ?>
                                                <div class="flex items-center space-x-2 mt-3">
                                                    <span class="text-xs px-2 py-1 rounded <?php echo $partner['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'; ?>">
                                                        <?php echo $partner['active'] ? 'Aktiv' : 'Inaktiv'; ?>
                                                    </span>
                                                    <span class="text-xs text-gray-500">Sortierung: <?php echo $partner['sort_order']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 mt-4 pt-4 border-t border-gray-200">
                                            <button onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($partner)); ?>)" 
                                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Bearbeiten
                                            </button>
                                            <button onclick="if(confirm('Möchten Sie diesen Partner wirklich löschen?')) { document.getElementById('deleteForm<?php echo $partner['id']; ?>').submit(); }" 
                                                    class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="deleteForm<?php echo $partner['id']; ?>" method="POST" class="hidden">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $partner['id']; ?>">
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for Add/Edit Partner -->
    <div id="partnerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Neuer Partner</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="partnerForm" method="POST" class="p-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" value="">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Partner Name *</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
                        <div class="flex gap-2">
                            <input type="text" id="logo" name="logo" 
                                   onchange="updateLogoPreview(this)"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" 
                                   placeholder="uploads/partner-logo.png">
                            <button type="button" onclick="openMediaPicker('logo')" 
                                    class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg border border-gray-300">
                                <i class="fas fa-image"></i>
                            </button>
                        </div>
                        <div class="mt-3">
                            <img id="logo_preview" src="../assets/img/placeholder.png" 
                                 alt="Logo Preview" 
                                 class="max-h-32 max-w-full object-contain border border-gray-200 rounded-lg p-2 bg-gray-50">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Website URL</label>
                        <input type="url" name="website" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" 
                               placeholder="https://example.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Beschreibung</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" 
                                  placeholder="Kurze Beschreibung des Partners..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Sortierung</label>
                            <input type="number" name="sort_order" value="0" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="flex items-center pt-8">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="active" checked 
                                       class="form-checkbox h-5 w-5 text-primary rounded">
                                <span class="ml-2 text-sm font-medium text-gray-700">Aktiv</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit" id="modalBtn" 
                            class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Partner erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

