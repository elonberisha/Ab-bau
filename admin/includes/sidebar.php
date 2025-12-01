<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="bg-gray-800 text-white w-64 min-h-screen fixed left-0 top-0 pt-16">
    <div class="p-4">
        <div class="flex items-center space-x-3 mb-6 pb-6 border-b border-gray-700">
            <i class="fas fa-tachometer-alt text-2xl text-primary"></i>
            <h2 class="text-xl font-bold">Admin Panel</h2>
        </div>
        
        <nav class="space-y-2">
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'dashboard.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="media-library.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'media-library.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-folder-open w-5"></i>
                <span>Media Library</span>
            </a>
            
            <a href="gallery.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'gallery.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-images w-5"></i>
                <span>Galeri</span>
            </a>
            
            <a href="activities.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'activities.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-tasks w-5"></i>
                <span>Veprimtari</span>
            </a>
            
            <a href="services.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'services.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-tools w-5"></i>
                <span>Shërbimet</span>
            </a>
            
            <a href="catalogs.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'catalogs.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-book w-5"></i>
                <span>Katalogje</span>
            </a>
            
            <a href="reviews.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'reviews.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-star w-5"></i>
                <span>Reviews</span>
            </a>
            
            <a href="change-password.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all <?php echo $currentPage === 'change-password.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-key w-5"></i>
                <span>Ndrysho Fjalëkalimin</span>
            </a>
        </nav>
        
        <div class="mt-8 pt-6 border-t border-gray-700">
            <a href="../index.html" target="_blank" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700 transition-all">
                <i class="fas fa-external-link-alt w-5"></i>
                <span>Shiko Faqen</span>
            </a>
            
            <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-900 hover:bg-opacity-20 transition-all mt-2">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Dil</span>
            </a>
        </div>
    </div>
</div>

