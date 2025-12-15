<?php
require_once 'admin/includes/db_connect.php';

echo "<h1>Fillimi i Migrimit...</h1>";

try {
    // 1. Tabela HERO_SECTION
    $pdo->exec("DROP TABLE IF EXISTS hero_section");
    $pdo->exec("CREATE TABLE hero_section (
        id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) DEFAULT '',
        subtitle TEXT DEFAULT NULL,
        image VARCHAR(255) DEFAULT '',
        button1_text VARCHAR(100) DEFAULT '',
        button1_link VARCHAR(255) DEFAULT '',
        button2_text VARCHAR(100) DEFAULT '',
        button2_link VARCHAR(255) DEFAULT '',
        stat1_number VARCHAR(50) DEFAULT '',
        stat1_text VARCHAR(100) DEFAULT '',
        stat2_number VARCHAR(50) DEFAULT '',
        stat2_text VARCHAR(100) DEFAULT '',
        stat3_number VARCHAR(50) DEFAULT '',
        stat3_text VARCHAR(100) DEFAULT ''
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>Tabela hero_section u krijua.</p>";

    $pdo->exec("INSERT INTO hero_section (title, subtitle, button1_text, button1_link, button2_text, button2_link, stat1_number, stat1_text) 
                VALUES ('Cilësi dhe Saktësi', 'Partneri juaj për ndërtim', 'Katalogje', 'catalogs.html', 'Kontakt', 'contact.html', '500+', 'Projekte')");


    // 2. Tabela ABOUT_SECTION
    $pdo->exec("DROP TABLE IF EXISTS about_section");
    $pdo->exec("CREATE TABLE about_section (
        id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) DEFAULT '',
        description1 TEXT DEFAULT NULL,
        description2 TEXT DEFAULT NULL,
        image1 VARCHAR(255) DEFAULT '',
        image2 VARCHAR(255) DEFAULT '',
        image3 VARCHAR(255) DEFAULT '',
        shop_title VARCHAR(100) DEFAULT '',
        shop_text TEXT DEFAULT NULL,
        processing_title VARCHAR(100) DEFAULT '',
        processing_text TEXT DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>Tabela about_section u krijua.</p>";

    $pdo->exec("INSERT INTO about_section (title, description1) VALUES ('Rreth Nesh', 'Ne jemi specialistë...')");


    // 3. Tabela CONTACT_SECTION
    $pdo->exec("DROP TABLE IF EXISTS contact_section");
    $pdo->exec("CREATE TABLE contact_section (
        id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) DEFAULT 'Na Kontaktoni',
        subtitle TEXT DEFAULT NULL,
        address_line1 VARCHAR(255) DEFAULT '',
        address_line2 VARCHAR(255) DEFAULT '',
        phone1 VARCHAR(50) DEFAULT '',
        phone2 VARCHAR(50) DEFAULT '',
        email VARCHAR(100) DEFAULT '',
        facebook_link VARCHAR(255) DEFAULT '',
        instagram_link VARCHAR(255) DEFAULT '',
        linkedin_link VARCHAR(255) DEFAULT '',
        whatsapp_number VARCHAR(50) DEFAULT '',
        map_embed_code TEXT DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>Tabela contact_section u krijua.</p>";

    $pdo->exec("INSERT INTO contact_section (email, phone1, address_line1) VALUES ('info@ab-bau.de', '0176 555 370 71', 'Talstraße 3d, 85238 Petershausen')");


    // 4. Tabela LEGAL_SECTION (Impressum, Privacy, AGB)
    $pdo->exec("DROP TABLE IF EXISTS legal_section");
    $pdo->exec("CREATE TABLE legal_section (
        id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        impressum_content LONGTEXT DEFAULT NULL,
        privacy_content LONGTEXT DEFAULT NULL,
        agb_content LONGTEXT DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>Tabela legal_section u krijua.</p>";

    $pdo->exec("INSERT INTO legal_section (impressum_content) VALUES ('Impressum Text...')");


    // 5. Fshijmë tabelën e vjetër 'settings'
    $pdo->exec("DROP TABLE IF EXISTS settings");
    echo "<p style='color:orange'>Tabela settings u fshi.</p>";


    echo "<hr><h1>Migrimi Përfundoi me Sukses!</h1>";
    echo "<a href='admin/dashboard.php'>Shko tek Dashboard</a>";

} catch (PDOException $e) {
    echo "<h1 style='color:red'>Gabim gjatë ekzekutimit:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>