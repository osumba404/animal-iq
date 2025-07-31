<?php
// cron/rotate_daily_animal.php

require_once '../includes/db.php';

function rotateAnimalOfTheDay(PDO $pdo) {
    // Check if today’s animal is already set
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM animal_of_the_day WHERE date = ?");
    $stmt->execute([$today]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) return; // Already rotated today

    // Get unused animals
    $stmt = $pdo->query("
        SELECT * FROM animals 
        WHERE id NOT IN (SELECT animal_id FROM animal_rotation_log)
          AND status = 'approved'
        ORDER BY id ASC
        LIMIT 1
    ");
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);

    // If all animals are exhausted, reset rotation log
    if (!$animal) {
        $pdo->exec("DELETE FROM animal_rotation_log");

        // Try again after reset
        $stmt = $pdo->query("
            SELECT * FROM animals 
            WHERE status = 'approved'
            ORDER BY id ASC
            LIMIT 1
        ");
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($animal) {
        // Update the animal of the day
        $stmt = $pdo->prepare("UPDATE animal_of_the_day SET animal_id = ?, date = ?");
        $stmt->execute([$animal['id'], $today]);

        // Log it
        $stmt = $pdo->prepare("INSERT INTO animal_rotation_log (animal_id, shown_on) VALUES (?, ?)");
        $stmt->execute([$animal['id'], $today]);
    }
}

rotateAnimalOfTheDay($pdo);


function getAnimalOfTheDay(PDO $pdo) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT a.*
        FROM animal_of_the_day aot
        JOIN animals a ON a.id = aot.animal_id
        WHERE aot.date = ?
    ");
    $stmt->execute([$today]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

