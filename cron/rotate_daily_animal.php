<?php
// cron/rotate_daily_animal.php

require_once '../includes/db.php';

function getAnimalOfTheDay(PDO $pdo, string $timezone) {
    // Get the local date for that timezone
    $dateObj = new DateTime('now', new DateTimeZone($timezone));
    $today = $dateObj->format('Y-m-d');

    // Cache file per timezone
    $cacheFile = __DIR__ . "/../cache/animal_{$timezone}_{$today}.json";

    // If cache exists, load and return
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if ($data && isset($data['id'])) {
            return $data;
        }
    }

    // Pick a random approved animal
    $stmt = $pdo->query("
        SELECT * FROM animals
        WHERE status = 'approved'
        ORDER BY RAND()
        LIMIT 1
    ");
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($animal) {
        // Save to cache
        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0777, true);
        }
        file_put_contents($cacheFile, json_encode($animal));
    }

    return $animal;
}

// Example usage
$timezone = 'America/New_York'; // This should come from the user’s location
$animalOfTheDay = getAnimalOfTheDay($pdo, $timezone);


