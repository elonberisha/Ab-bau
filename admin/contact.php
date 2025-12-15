<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => sanitize($_POST['title']),
        'subtitle' => sanitize($_POST['subtitle']),
        'address_line1' => sanitize($_POST['address_line1']),
        'address_line2' => sanitize($_POST['address_line2']),
        'phone1' => sanitize($_POST['phone1']),
        'phone2' => sanitize($_POST['phone2']),
        'email' => sanitize($_POST['email']),
        'facebook_link' => sanitize($_POST['facebook_link']),
        'instagram_link' => sanitize($_POST['instagram_link']),
        'linkedin_link' => sanitize($_POST['linkedin_link']),
        'whatsapp_number' => sanitize($_POST['whatsapp_number']),
        'map_embed_code' => $_POST['map_embed_code'] // Allow HTML for map
    ];

    if (updateSectionData('contact_section', $data)) {
        $message = 'Të dhënat e kontaktit u përditësuan me sukses!';
        $messageType = 'success';
    } else {
        $message = 'Gabim gjatë përditësimit.';
        $messageType = 'error';
    }
}

// Get Data
$contact = getSectionData('contact_section');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Kontaktin - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-address-book mr-2 text-primary"></i>Kontakt
                </h1>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?> flex items-center shadow-sm">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3 text-xl"></i>
                        <span class="font-medium"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6 max-w-4xl mx-auto">
                    
                    <!-- General Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-heading mr-2 text-blue-500"></i>Titujt dhe Përshkrimi
                        </h2>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Titulli i Faqes</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($contact['title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nëntitulli / Përshkrimi</label>
                                <textarea name="subtitle" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20"><?php echo htmlspecialchars($contact['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-info-circle mr-2 text-green-500"></i>Detajet e Kontaktit
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Adresa Rreshti 1</label>
                                <input type="text" name="address_line1" value="<?php echo htmlspecialchars($contact['address_line1'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Adresa Rreshti 2</label>
                                <input type="text" name="address_line2" value="<?php echo htmlspecialchars($contact['address_line2'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-phone text-gray-400 mr-1"></i> Telefoni 1</label>
                                <input type="text" name="phone1" value="<?php echo htmlspecialchars($contact['phone1'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-phone text-gray-400 mr-1"></i> Telefoni 2</label>
                                <input type="text" name="phone2" value="<?php echo htmlspecialchars($contact['phone2'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-envelope text-gray-400 mr-1"></i> Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-whatsapp text-gray-400 mr-1"></i> WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($contact['whatsapp_number'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-share-alt mr-2 text-purple-500"></i>Rrjetet Sociale
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook Link</label>
                                <input type="text" name="facebook_link" value="<?php echo htmlspecialchars($contact['facebook_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram text-pink-600 mr-1"></i> Instagram Link</label>
                                <input type="text" name="instagram_link" value="<?php echo htmlspecialchars($contact['instagram_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-linkedin text-blue-700 mr-1"></i> LinkedIn Link</label>
                                <input type="text" name="linkedin_link" value="<?php echo htmlspecialchars($contact['linkedin_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Map Embed -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-map mr-2 text-orange-500"></i>Google Maps Embed
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kodi i Embed (Iframe)</label>
                            <textarea name="map_embed_code" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-xs"><?php echo htmlspecialchars($contact['map_embed_code'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Shkoni në Google Maps -> Share -> Embed a map -> Copy HTML.</p>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-4 pb-8">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-8 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i> Ruaj Ndryshimet
                        </button>
                    </div>

                </form>
            </main>
        </div>
    </div>
</body>
</html>