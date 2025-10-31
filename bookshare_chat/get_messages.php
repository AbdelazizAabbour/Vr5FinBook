<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['book_id'])) {
    http_response_code(403);
    exit('Accès non autorisé');
}

$book_id = intval($_GET['book_id']);
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT m.*, u.username as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.book_id = ?
    AND (m.sender_id = ? OR m.receiver_id = ?)
    ORDER BY m.created_at ASC
");

$stmt->execute([$book_id, $user_id, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);