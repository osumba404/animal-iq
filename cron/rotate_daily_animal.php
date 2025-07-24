<?php
// cron/rotate_daily_animal.php

function getAnimalOfTheDay(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM animals ORDER BY RAND() LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


