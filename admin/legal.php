<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'impressum_content' => $_POST['impressum_content'], // Allow HTML
        'privacy_content' => $_POST['privacy_content'],     // Allow HTML
        'agb_content' => $_POST['agb_content']              // Allow HTML
    ];

    if (updateSectionData('legal_section', $data)) {
        $message = 'Të dhënat ligjore u përditësuan me sukses!';
        $messageType = 'success';
    } else {
        $message = 'Gabim gjatë përditësimit.';
        $messageType = 'error';
    }
}

// Get Data
$legal = getSectionData('legal_section');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxho Legal - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <!-- Optional: Add a rich text editor like TinyMCE or CKEditor here if needed -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: 'textarea.rich-editor',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 400
      });
    </script>
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
                    <i class="fas fa-balance-scale mr-2 text-primary"></i>Legal (Impressum, Privacy, AGB)
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

                <form method="POST" class="space-y-8 max-w-5xl mx-auto">
                    
                    <!-- Impressum -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-file-contract mr-2 text-blue-500"></i>Impressum
                        </h2>
                        <textarea name="impressum_content" class="rich-editor w-full border border-gray-300 rounded-lg"><?php echo htmlspecialchars($legal['impressum_content'] ?? ''); ?></textarea>
                    </div>

                    <!-- Privacy Policy -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-user-shield mr-2 text-green-500"></i>Privacy Policy (Datenschutz)
                        </h2>
                        <textarea name="privacy_content" class="rich-editor w-full border border-gray-300 rounded-lg"><?php echo htmlspecialchars($legal['privacy_content'] ?? ''); ?></textarea>
                    </div>

                    <!-- AGB -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-gavel mr-2 text-red-500"></i>AGB (Terms & Conditions)
                        </h2>
                        <textarea name="agb_content" class="rich-editor w-full border border-gray-300 rounded-lg"><?php echo htmlspecialchars($legal['agb_content'] ?? ''); ?></textarea>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-4 pb-8">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-8 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i> Ruaj Të Gjitha Ndryshimet
                        </button>
                    </div>

                </form>
            </main>
        </div>
    </div>
</body>
</html>