<?php
$host = 'localhost';
$db   = 'bookshare_php';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $owner_id = $_SESSION['user_id'] ?? null;

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $book_condition = $_POST['book_condition'] ?? 'used';
    $type = $_POST['type'] ?? 'lend';
    $price = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $description = trim($_POST['description'] ?? '');

    // === GESTION UPLOAD IMAGE ===
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploadsLivre/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedExt)) {
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $image = 'uploadsLivre/' . $fileName;
            }
        }
    }

    // === INSERTION DANS LA BASE ===
    $sql = "INSERT INTO books (title, author, book_condition, type, price, description, image, owner_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $author, $book_condition, $type, $price, $description, $image, $owner_id]);

    $_SESSION['success_message'] = "Livre ajouté avec succès !";
    header("Location: dashboard.php?success=1");
    exit;
}
?>
