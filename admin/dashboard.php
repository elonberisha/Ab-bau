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
    <title>Dashboard - AB Bau Admin</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Layout Wrapper -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar (Fixed Width) -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content (Flexible) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">Paneli Kryesor</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.html" target="_blank" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:border-primary/30">
                        <i class="fas fa-external-link-alt mr-2 text-xs"></i> Website Live
                    </a>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Stat Card: Projects -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-project-diagram text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Projekte</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['projects']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Services -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Shërbime</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['services']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Catalogs -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-book-open text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Katalogje</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['catalogs']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Reviews -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Reviews</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['reviews_pending']; ?> <span class="text-xs font-normal text-gray-400">/ <?php echo $stats['reviews_total']; ?></span></p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Title -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Meny e Shpejtë</h2>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Action: Add Project -->
                    <a href="projekte.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-plus text-6xl text-indigo-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-briefcase text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-indigo-600 transition-colors">Menaxho Portfolion</h3>
                            <p class="text-sm text-gray-500">Shto ose ndrysho projektet e realizuara.</p>
                        </div>
                    </a>

                    <!-- Action: Reviews -->
                    <a href="reviews.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-comments text-6xl text-orange-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-orange-50 rounded-lg text-orange-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-orange-600 transition-colors">Vlerësimet e Klientëve</h3>
                            <p class="text-sm text-gray-500">Aprovo <?php echo $stats['reviews_pending']; ?> komente të reja në pritje.</p>
                        </div>
                    </a>

                    <!-- Action: Hero -->
                    <a href="hero.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-pink-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-image text-6xl text-pink-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-pink-50 rounded-lg text-pink-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-edit text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-pink-600 transition-colors">Ndrysho Ballinën</h3>
                            <p class="text-sm text-gray-500">Përditëso tekstet dhe foton kryesore.</p>
                        </div>
                    </a>
                </div>
                
                <!-- System Info Box -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-start space-x-4">
                    <i class="fas fa-database text-blue-500 text-xl mt-1"></i>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800">Statusi i Sistemit</h3>
                        <p class="text-sm text-blue-600 mt-1 leading-relaxed">
                            Paneli është lidhur me sukses me databazën <strong>MySQL</strong>. Të gjitha ndryshimet ruhen në mënyrë të sigurt dhe shfaqen direkt në faqe.
                        </p>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Mobile Sidebar Toggle Logic
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.w-64'); // Select sidebar container
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                // Toggle logic depending on implementation (usually class based)
                // For this layout, we might want to slide it in
                alert('Mobile menu toggle to be implemented fully');
            });
        }
    </script>
</body>
</html>
