<?php
require_once __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Accès non autorisé');
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['receiver_id']) || !isset($data['book_id']) || !isset($data['message'])) {
    http_response_code(400);
    exit('Données manquantes');
}

$stmt = $pdo->prepare("
    INSERT INTO messages (sender_id, receiver_id, book_id, message)
    VALUES (?, ?, ?, ?)
");

try {
    $stmt->execute([
        $_SESSION['user_id'],
        $data['receiver_id'],
        $data['book_id'],
        $data['message']
    ]);
    
    echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}