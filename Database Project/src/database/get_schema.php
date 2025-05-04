<?php
require_once __DIR__ . '/../db.php';

try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 
