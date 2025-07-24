<!-- admin/encyclopaedia-editor.php -->
<?php
$animals = $pdo->query("SELECT * FROM animals ORDER BY created_at DESC")->fetchAll();
?>