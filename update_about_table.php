<?php
require_once 'admin/includes/db_connect.php';

echo "<h1>Përditësimi i Tabelës About...</h1>";

try {
    // Add new columns to about_section
    $columns = [
        "ADD COLUMN page_hero_image VARCHAR(255) DEFAULT ''",
        "ADD COLUMN page_hero_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN page_hero_subtitle TEXT DEFAULT NULL",
        "ADD COLUMN full_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN full_desc1 TEXT DEFAULT NULL",
        "ADD COLUMN full_desc2 TEXT DEFAULT NULL",
        "ADD COLUMN full_desc3 TEXT DEFAULT NULL",
        "ADD COLUMN story_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN story_p1 TEXT DEFAULT NULL",
        "ADD COLUMN story_p2 TEXT DEFAULT NULL",
        "ADD COLUMN story_p3 TEXT DEFAULT NULL",
        "ADD COLUMN card1_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN card1_text TEXT DEFAULT NULL",
        "ADD COLUMN card2_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN card2_text TEXT DEFAULT NULL",
        "ADD COLUMN card3_title VARCHAR(255) DEFAULT ''",
        "ADD COLUMN card3_text TEXT DEFAULT NULL",
        "ADD COLUMN stat1_num VARCHAR(50) DEFAULT ''",
        "ADD COLUMN stat1_text VARCHAR(100) DEFAULT ''",
        "ADD COLUMN stat2_num VARCHAR(50) DEFAULT ''",
        "ADD COLUMN stat2_text VARCHAR(100) DEFAULT ''",
        "ADD COLUMN stat3_num VARCHAR(50) DEFAULT ''",
        "ADD COLUMN stat3_text VARCHAR(100) DEFAULT ''",
        "ADD COLUMN show_in_index TINYINT(1) DEFAULT 1"
    ];

    foreach ($columns as $col) {
        try {
            $pdo->exec("ALTER TABLE about_section $col");
            echo "<p style='color:green'>U shtua kolona: $col</p>";
        } catch (PDOException $e) {
            // Ignore if column already exists
            echo "<p style='color:orange'>Kolona ndoshta ekziston: " . $e->getMessage() . "</p>";
        }
    }
    
    // Now re-import the extended data from JSON
    $jsonPath = __DIR__ . '/data/customization.json';
    if (file_exists($jsonPath)) {
        $cust = json_decode(file_get_contents($jsonPath), true);
        if (isset($cust['about'])) {
            $a = $cust['about'];
            $stmt = $pdo->prepare("UPDATE about_section SET 
                page_hero_image = :phi, page_hero_title = :pht, page_hero_subtitle = :phs,
                full_title = :ft, full_desc1 = :fd1, full_desc2 = :fd2, full_desc3 = :fd3,
                story_title = :st, story_p1 = :sp1, story_p2 = :sp2, story_p3 = :sp3,
                card1_title = :c1t, card1_text = :c1x,
                card2_title = :c2t, card2_text = :c2x,
                card3_title = :c3t, card3_text = :c3x,
                stat1_num = :s1n, stat1_text = :s1t,
                stat2_num = :s2n, stat2_text = :s2t,
                stat3_num = :s3n, stat3_text = :s3t,
                show_in_index = :sii
                WHERE id = 1
            "); // Assuming id 1 is the single row
            
            // Check if row exists first, if not insert
            $check = $pdo->query("SELECT id FROM about_section LIMIT 1")->fetch();
            if (!$check) {
                $pdo->exec("INSERT INTO about_section (title) VALUES ('Initial')");
            }

            $stmt->execute([
                'phi' => $a['page_hero_image'] ?? '',
                'pht' => $a['page_hero_title'] ?? '',
                'phs' => $a['page_hero_subtitle'] ?? '',
                'ft' => $a['full_content']['title'] ?? '',
                'fd1' => $a['full_content']['description1'] ?? '',
                'fd2' => $a['full_content']['description2'] ?? '',
                'fd3' => $a['full_content']['description3'] ?? '',
                'st' => $a['story_title'] ?? '',
                'sp1' => $a['story_paragraph1'] ?? '',
                'sp2' => $a['story_paragraph2'] ?? '',
                'sp3' => $a['story_paragraph3'] ?? '',
                'c1t' => $a['card1_title'] ?? '',
                'c1x' => $a['card1_text'] ?? '',
                'c2t' => $a['card2_title'] ?? '',
                'c2x' => $a['card2_text'] ?? '',
                'c3t' => $a['card3_title'] ?? '',
                'c3x' => $a['card3_text'] ?? '',
                's1n' => $a['stats']['stat1_number'] ?? '',
                's1t' => $a['stats']['stat1_text'] ?? '',
                's2n' => $a['stats']['stat2_number'] ?? '',
                's2t' => $a['stats']['stat2_text'] ?? '',
                's3n' => $a['stats']['stat3_number'] ?? '',
                's3t' => $a['stats']['stat3_text'] ?? '',
                'sii' => isset($a['show_in_index']) && $a['show_in_index'] ? 1 : 0
            ]);
            echo "<p style='color:green'>Të dhënat shtesë u importuan.</p>";
        }
    }

    echo "<h1>Përditësimi Përfundoi!</h1>";

} catch (PDOException $e) {
    echo "<h1 style='color:red'>Gabim:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
