<?php
require_once 'functions.php';
requireLogin();

$stats = getStats();
$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #004499;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>
    
    <div class="ml-64 pt-16 p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Fotot Kryesore</p>
                        <p class="text-3xl font-bold text-primary"><?php echo $stats['home_images']; ?></p>
                    </div>
                    <i class="fas fa-home text-4xl text-primary opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Fotot Portfolio</p>
                        <p class="text-3xl font-bold text-primary"><?php echo $stats['portfolio_images']; ?></p>
                    </div>
                    <i class="fas fa-images text-4xl text-primary opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Veprimtari Aktive</p>
                        <p class="text-3xl font-bold text-primary"><?php echo $stats['activities']; ?></p>
                    </div>
                    <i class="fas fa-tasks text-4xl text-primary opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Katalogje</p>
                        <p class="text-3xl font-bold text-primary"><?php echo $stats['catalogs']; ?></p>
                    </div>
                    <i class="fas fa-book text-4xl text-primary opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Reviews Pending</p>
                        <p class="text-3xl font-bold text-orange-500"><?php echo $stats['pending_reviews']; ?></p>
                    </div>
                    <i class="fas fa-clock text-4xl text-orange-500 opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="gallery.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-lg">
                        <i class="fas fa-images text-3xl text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Menaxho Galerinë</h3>
                        <p class="text-gray-600 text-sm">Shto, ndrysho ose fshi fotot</p>
                    </div>
                </div>
            </a>
            
            <a href="activities.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-lg">
                        <i class="fas fa-tasks text-3xl text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Menaxho Veprimtari</h3>
                        <p class="text-gray-600 text-sm">Shtimi dhe Punimi</p>
                    </div>
                </div>
            </a>
            
            <a href="services.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-lg">
                        <i class="fas fa-tools text-3xl text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Menaxho Shërbimet</h3>
                        <p class="text-gray-600 text-sm">Menaxho cards e shërbimeve</p>
                    </div>
                </div>
            </a>
            
            <a href="catalogs.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-lg">
                        <i class="fas fa-book text-3xl text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Menaxho Katalogje</h3>
                        <p class="text-gray-600 text-sm">Katalogje produktesh</p>
                    </div>
                </div>
            </a>
            
            <a href="reviews.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-lg">
                        <i class="fas fa-star text-3xl text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Menaxho Reviews</h3>
                        <p class="text-gray-600 text-sm">Aprovo ose refuzo komente</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Informacion</h3>
                    <p class="text-blue-800">
                        Të gjitha ndryshimet që bëni këtu reflektohen automatikisht në faqen publike (<a href="../index.html" target="_blank" class="underline font-semibold">index.html</a>). 
                        Fotot, shërbimet dhe reviews lexohen dinamikisht nga API.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
