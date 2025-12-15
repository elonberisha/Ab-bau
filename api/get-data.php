<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../admin/includes/db_connect.php';

$type = $_GET['type'] ?? '';

try {
    switch ($type) {
        case 'gallery':
            $stmt = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
            $gallery = ['home' => [], 'portfolio' => [], 'details' => []];
            while ($row = $stmt->fetch()) {
                $item = ['image' => $row['image'], 'title' => $row['title']];
                if (isset($gallery[$row['category']])) {
                    $gallery[$row['category']][] = $item;
                }
            }
            echo json_encode($gallery);
            break;
        
        case 'services':
            $stmt = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY sort_order ASC");
            echo json_encode($stmt->fetchAll());
            break;
        
        case 'reviews':
            $stmt = $pdo->query("SELECT name, message, rating, DATE_FORMAT(date, '%Y-%m-%d') as date FROM reviews WHERE status = 'approved' ORDER BY date DESC");
            echo json_encode($stmt->fetchAll());
            break;
        
        case 'catalogs':
            $stmt = $pdo->query("SELECT * FROM catalogs WHERE active = 1 ORDER BY sort_order ASC");
            $catalogs = $stmt->fetchAll();
            foreach ($catalogs as &$catalog) {
                $pStmt = $pdo->prepare("SELECT * FROM catalog_products WHERE catalog_id = :cid AND active = 1 ORDER BY sort_order ASC");
                $pStmt->execute(['cid' => $catalog['id']]);
                $catalog['products'] = $pStmt->fetchAll();
            }
            echo json_encode($catalogs);
            break;
    
        case 'portfolio':
            $stmt = $pdo->query("SELECT id, title, description, image as path, type, DATE_FORMAT(date, '%d.%m.%Y') as date FROM projects WHERE active = 1 ORDER BY date DESC");
            echo json_encode($stmt->fetchAll());
            break;
    
        case 'customization':
            $response = [];

            // 1. HERO SECTION
            $hero = $pdo->query("SELECT * FROM hero_section LIMIT 1")->fetch();
            if ($hero) {
                $response['hero'] = [
                    'title' => $hero['title'],
                    'subtitle' => $hero['subtitle'],
                    'image' => $hero['image'],
                    'button1_text' => $hero['button1_text'],
                    'button1_link' => $hero['button1_link'],
                    'button2_text' => $hero['button2_text'],
                    'button2_link' => $hero['button2_link'],
                    'stats_bar' => [
                        'stat1_number' => $hero['stat1_number'],
                        'stat1_text' => $hero['stat1_text'],
                        'stat2_number' => $hero['stat2_number'],
                        'stat2_text' => $hero['stat2_text'],
                        'stat3_number' => $hero['stat3_number'],
                        'stat3_text' => $hero['stat3_text']
                    ]
                ];
            }

            // 2. ABOUT SECTION
            $about = $pdo->query("SELECT * FROM about_section LIMIT 1")->fetch();
            if ($about) {
                $response['about'] = [
                    'title' => $about['title'],
                    'description1' => $about['description1'],
                    'description2' => $about['description2'],
                    'shop_title' => $about['shop_title'],
                    'shop_text' => $about['shop_text'],
                    'processing_title' => $about['processing_title'],
                    'processing_text' => $about['processing_text'],
                    'image1' => $about['image1'],
                    'image2' => $about['image2'],
                    'image3' => $about['image3'],
                    
                    // Fields added for update_about_table
                    'page_hero_image' => $about['page_hero_image'] ?? '',
                    'page_hero_title' => $about['page_hero_title'] ?? '',
                    'page_hero_subtitle' => $about['page_hero_subtitle'] ?? '',
                    'full_content' => [
                        'title' => $about['full_title'] ?? '',
                        'description1' => $about['full_desc1'] ?? '',
                        'description2' => $about['full_desc2'] ?? '',
                        'description3' => $about['full_desc3'] ?? ''
                    ],
                    'story_title' => $about['story_title'] ?? '',
                    'story_paragraph1' => $about['story_p1'] ?? '',
                    'story_paragraph2' => $about['story_p2'] ?? '',
                    'story_paragraph3' => $about['story_p3'] ?? '',
                    'card1_title' => $about['card1_title'] ?? '',
                    'card1_text' => $about['card1_text'] ?? '',
                    'card2_title' => $about['card2_title'] ?? '',
                    'card2_text' => $about['card2_text'] ?? '',
                    'card3_title' => $about['card3_title'] ?? '',
                    'card3_text' => $about['card3_text'] ?? '',
                    'stats' => [
                        'stat1_number' => $about['stat1_num'] ?? '',
                        'stat1_text' => $about['stat1_text'] ?? '',
                        'stat2_number' => $about['stat2_num'] ?? '',
                        'stat2_text' => $about['stat2_text'] ?? '',
                        'stat3_number' => $about['stat3_num'] ?? '',
                        'stat3_text' => $about['stat3_text'] ?? ''
                    ],
                    'show_in_index' => (bool)($about['show_in_index'] ?? 1)
                ];
            }

            // 3. CONTACT SECTION
            $contact = $pdo->query("SELECT * FROM contact_section LIMIT 1")->fetch();
            if ($contact) {
                $response['contact'] = [
                    'section_title' => $contact['title'],
                    'section_subtitle' => $contact['subtitle'],
                    'address_line1' => $contact['address_line1'],
                    'address_line2' => $contact['address_line2'],
                    'phone1' => $contact['phone1'],
                    'phone2' => $contact['phone2'],
                    'email' => $contact['email'],
                    'facebook_link' => $contact['facebook_link'],
                    'instagram_link' => $contact['instagram_link'],
                    'linkedin_link' => $contact['linkedin_link'],
                    'whatsapp_number' => $contact['whatsapp_number']
                ];
            }
            
            // 4. LEGAL (Optional, usually loaded separately or page specific)
            // But we can include it if frontend expects it
            $legal = $pdo->query("SELECT * FROM legal_section LIMIT 1")->fetch();
            if ($legal) {
                $response['legal'] = [
                    'impressum' => $legal['impressum_content'],
                    'privacy' => $legal['privacy_content'],
                    'agb' => $legal['agb_content']
                ];
            }

            echo json_encode($response);
            break;
        
        default:
            echo json_encode(['error' => 'Invalid type']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>