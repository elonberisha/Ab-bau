<?php
require_once 'admin/includes/db_connect.php';

echo "<h1>Mbushja e Shërbimeve...</h1>";

try {
    // Pastro tabelën ekzistuese (opsionale, nëse do të fillosh nga e para)
    // $pdo->exec("TRUNCATE TABLE services"); 

    $services = [
        [
            'title' => 'Verlegung von Keramikfliesen',
            'desc' => 'Professionelle Verlegung von Keramikfliesen für Wand und Boden. Wir garantieren Präzision und Langlebigkeit für Ihr Zuhause.',
            'icon' => 'fa-th-large',
            'image' => 'uploads/services/keramik.jpg',
            'active' => 1
        ],
        [
            'title' => 'Marmorarbeiten',
            'desc' => 'Exklusive Marmorarbeiten für ein luxuriöses Ambiente. Von der Treppe bis zum Badezimmer – wir verarbeiten Marmor meisterhaft.',
            'icon' => 'fa-gem',
            'image' => 'uploads/services/marmor.jpg',
            'active' => 1
        ],
        [
            'title' => 'Granitverlegung',
            'desc' => 'Robuste und elegante Granitlösungen für Innen- und Außenbereiche. Ideal für Küchenarbeitsplatten und stark frequentierte Böden.',
            'icon' => 'fa-layer-group',
            'image' => 'uploads/services/granit.jpg',
            'active' => 1
        ],
        [
            'title' => 'Badsanierung',
            'desc' => 'Komplette Badsanierung aus einer Hand. Wir verwandeln Ihr altes Bad in eine moderne Wellness-Oase.',
            'icon' => 'fa-bath',
            'image' => 'uploads/services/bad.jpg',
            'active' => 1
        ],
        [
            'title' => 'Terrassen & Außenbereiche',
            'desc' => 'Gestaltung von Terrassen und Balkonen mit wetterfesten Fliesen und Natursteinen für langanhaltende Schönheit im Freien.',
            'icon' => 'fa-sun',
            'image' => 'uploads/services/terrasse.jpg',
            'active' => 1
        ],
        [
            'title' => 'Natursteinverlegung',
            'desc' => 'Fachgerechte Verlegung von diversen Natursteinen. Einzigartige Strukturen und Farben für ein individuelles Wohnerlebnis.',
            'icon' => 'fa-mountain',
            'image' => 'uploads/services/naturstein.jpg',
            'active' => 1
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image, active) VALUES (:title, :desc, :icon, :image, :active)");

    foreach ($services as $s) {
        // Kontrollo nëse ekziston
        $check = $pdo->prepare("SELECT id FROM services WHERE title = :title");
        $check->execute(['title' => $s['title']]);
        
        if (!$check->fetch()) {
            $stmt->execute($s);
            echo "<p style='color:green'>U shtua: {$s['title']}</p>";
        } else {
            echo "<p style='color:orange'>Ekziston tashmë: {$s['title']}</p>";
        }
    }

    echo "<h2>Procesi përfundoi!</h2>";
    echo "<a href='index.html'>Shiko Faqen</a>";

} catch (PDOException $e) {
    echo "Gabim: " . $e->getMessage();
}
?>
