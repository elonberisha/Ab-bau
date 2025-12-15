<?php
require_once 'admin/includes/db_connect.php';

// Helper to read JSON
function getJsonData($file) {
    $path = __DIR__ . '/data/' . $file;
    if (file_exists($path)) {
        return json_decode(file_get_contents($path), true) ?: [];
    }
    return [];
}

echo "<h1>Fillimi i Importimit nga JSON në MySQL...</h1>";

try {
    $pdo->beginTransaction();

    // 1. Import HERO, ABOUT, CONTACT (from customization.json)
    $cust = getJsonData('customization.json');
    
    if (!empty($cust)) {
        // Hero
        if (isset($cust['hero'])) {
            $h = $cust['hero'];
            // Pastrojmë tabelën hero_section
            $pdo->exec("TRUNCATE TABLE hero_section");
            $stmt = $pdo->prepare("INSERT INTO hero_section (title, subtitle, image, button1_text, button1_link, button2_text, button2_link, stat1_number, stat1_text, stat2_number, stat2_text, stat3_number, stat3_text) VALUES (:title, :sub, :img, :b1t, :b1l, :b2t, :b2l, :s1n, :s1t, :s2n, :s2t, :s3n, :s3t)");
            $stmt->execute([
                'title' => $h['title'] ?? '',
                'sub' => $h['subtitle'] ?? '',
                'img' => $h['image'] ?? '',
                'b1t' => $h['button1_text'] ?? '',
                'b1l' => $h['button1_link'] ?? '',
                'b2t' => $h['button2_text'] ?? '',
                'b2l' => $h['button2_link'] ?? '',
                's1n' => $h['stats_bar']['stat1_number'] ?? '',
                's1t' => $h['stats_bar']['stat1_text'] ?? '',
                's2n' => $h['stats_bar']['stat2_number'] ?? '',
                's2t' => $h['stats_bar']['stat2_text'] ?? '',
                's3n' => $h['stats_bar']['stat3_number'] ?? '',
                's3t' => $h['stats_bar']['stat3_text'] ?? ''
            ]);
            echo "<p style='color:green'>Hero u importua.</p>";
        }

        // About
        if (isset($cust['about'])) {
            $a = $cust['about'];
            $pdo->exec("TRUNCATE TABLE about_section");
            $stmt = $pdo->prepare("INSERT INTO about_section (title, description1, description2, image1, image2, image3, shop_title, shop_text, processing_title, processing_text) VALUES (:title, :d1, :d2, :img1, :img2, :img3, :st, :stx, :pt, :ptx)");
            $stmt->execute([
                'title' => $a['title'] ?? '',
                'd1' => $a['description1'] ?? '',
                'd2' => $a['description2'] ?? '',
                'img1' => $a['image1'] ?? '',
                'img2' => $a['image2'] ?? '',
                'img3' => $a['image3'] ?? '',
                'st' => $a['shop_title'] ?? '',
                'stx' => $a['shop_text'] ?? '',
                'pt' => $a['processing_title'] ?? '',
                'ptx' => $a['processing_text'] ?? ''
            ]);
            echo "<p style='color:green'>About u importua.</p>";
        }

        // Contact
        if (isset($cust['contact'])) {
            $c = $cust['contact'];
            $pdo->exec("TRUNCATE TABLE contact_section");
            $stmt = $pdo->prepare("INSERT INTO contact_section (title, subtitle, address_line1, address_line2, phone1, phone2, email, facebook_link, instagram_link, linkedin_link, whatsapp_number) VALUES (:title, :sub, :a1, :a2, :p1, :p2, :e, :fb, :ig, :li, :wa)");
            $stmt->execute([
                'title' => $c['section_title'] ?? '',
                'sub' => $c['section_subtitle'] ?? '',
                'a1' => $c['address_line1'] ?? '',
                'a2' => $c['address_line2'] ?? '',
                'p1' => $c['phone1'] ?? '',
                'p2' => $c['phone2'] ?? '',
                'e' => $c['email'] ?? '',
                'fb' => $c['facebook_link'] ?? '',
                'ig' => $c['instagram_link'] ?? '',
                'li' => $c['linkedin_link'] ?? '',
                'wa' => $c['whatsapp_number'] ?? ''
            ]);
            echo "<p style='color:green'>Contact u importua.</p>";
        }
    }

    // 2. Import SERVICES
    $services = getJsonData('services.json');
    if (!empty($services)) {
        $pdo->exec("TRUNCATE TABLE services");
        $stmt = $pdo->prepare("INSERT INTO services (title, description, image, icon, active) VALUES (:title, :desc, :img, :icon, :active)");
        foreach ($services as $s) {
            $stmt->execute([
                'title' => $s['title'] ?? '',
                'desc' => $s['description'] ?? '',
                'img' => $s['image'] ?? '',
                'icon' => $s['icon'] ?? 'fa-tools',
                'active' => isset($s['active']) && $s['active'] ? 1 : 0
            ]);
        }
        echo "<p style='color:green'>Services u importuan (" . count($services) . ").</p>";
    }

    // 3. Import CATALOGS
    $catalogsData = getJsonData('catalogs.json');
    if (!empty($catalogsData) && isset($catalogsData['catalogs'])) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE catalog_products");
        $pdo->exec("TRUNCATE TABLE catalogs");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        $stmtCat = $pdo->prepare("INSERT INTO catalogs (title, description, cover_image, category, pdf_file, active) VALUES (:title, :desc, :img, :cat, :pdf, :active)");
        $stmtProd = $pdo->prepare("INSERT INTO catalog_products (catalog_id, name, description, image, price, active) VALUES (:cid, :name, :desc, :img, :price, :active)");

        foreach ($catalogsData['catalogs'] as $c) {
            $stmtCat->execute([
                'title' => $c['title'] ?? '',
                'desc' => $c['description'] ?? '',
                'img' => $c['cover_image'] ?? '',
                'cat' => $c['category'] ?? '',
                'pdf' => $c['pdf_file'] ?? '',
                'active' => isset($c['active']) && $c['active'] ? 1 : 0
            ]);
            $catId = $pdo->lastInsertId();

            if (isset($c['products']) && is_array($c['products'])) {
                foreach ($c['products'] as $p) {
                    $stmtProd->execute([
                        'cid' => $catId,
                        'name' => $p['name'] ?? '',
                        'desc' => $p['description'] ?? '',
                        'img' => $p['image'] ?? '',
                        'price' => $p['price'] ?? '',
                        'active' => isset($p['active']) && $p['active'] ? 1 : 0
                    ]);
                }
            }
        }
        echo "<p style='color:green'>Katalogët u importuan.</p>";
    }

    // 4. Import PROJECTS
    $projectsData = getJsonData('projects.json');
    if (!empty($projectsData) && isset($projectsData['projects'])) {
        $pdo->exec("TRUNCATE TABLE projects");
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, type, date, active) VALUES (:title, :desc, :img, :type, :date, :active)");
        
        foreach ($projectsData['projects'] as $p) {
            // Convert date format if needed (assuming YYYY-MM-DD or similar)
            $date = $p['date'] ?? date('Y-m-d');
            
            $stmt->execute([
                'title' => $p['title'] ?? '',
                'desc' => $p['description'] ?? '',
                'img' => $p['image'] ?? $p['path'] ?? '', // Handle old structure 'path'
                'type' => $p['type'] ?? 'portfolio',
                'date' => $date,
                'active' => isset($p['active']) && $p['active'] ? 1 : 0
            ]);
        }
        echo "<p style='color:green'>Projekte u importuan.</p>";
    }

    // 5. Import REVIEWS
    $reviewsData = getJsonData('reviews.json');
    if (!empty($reviewsData)) {
        $pdo->exec("TRUNCATE TABLE reviews");
        $stmt = $pdo->prepare("INSERT INTO reviews (name, message, rating, status, date) VALUES (:name, :msg, :rating, :status, :date)");
        
        // Approved
        if (isset($reviewsData['approved'])) {
            foreach ($reviewsData['approved'] as $r) {
                $stmt->execute([
                    'name' => $r['name'] ?? '',
                    'msg' => $r['message'] ?? '',
                    'rating' => $r['rating'] ?? 5,
                    'status' => 'approved',
                    'date' => $r['date'] ?? date('Y-m-d H:i:s')
                ]);
            }
        }
        // Pending
        if (isset($reviewsData['pending'])) {
            foreach ($reviewsData['pending'] as $r) {
                $stmt->execute([
                    'name' => $r['name'] ?? '',
                    'msg' => $r['message'] ?? '',
                    'rating' => $r['rating'] ?? 5,
                    'status' => 'pending',
                    'date' => $r['date'] ?? date('Y-m-d H:i:s')
                ]);
            }
        }
        echo "<p style='color:green'>Reviews u importuan.</p>";
    }

    $pdo->commit();
    echo "<h1>Importimi Përfundoi me Sukses!</h1>";
    echo "<a href='admin/dashboard.php'>Shko tek Dashboard</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h1 style='color:red'>Gabim gjatë importimit:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
