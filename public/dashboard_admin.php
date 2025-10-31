<?php
require_once __DIR__ . '/../app/auth/login.php';
$uid = $_SESSION['user_id'];
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>

<title>BookShare Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<link rel="icon" href="../pics/logo.png" type="image/x-icon">
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#213e5e",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101c22",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 24px;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark">
<div class="relative flex min-h-screen w-full">
<!-- SideNavBar -->
<aside class="flex w-64 flex-col bg-white p-4 dark:bg-background-dark dark:border-r dark:border-white/10">
<div class="flex h-full flex-col justify-between">
<div class="flex flex-col gap-4">
<div class="flex items-center gap-3 px-3">
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10" data-alt="BookShare Logo" style='background-image: url("../etud/aziz.jpg");'></div>
<div class="flex flex-col">
<h1 class="text-gray-900 dark:text-gray-100 text-base font-medium leading-normal">BookShare | CMC</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">Admin Panel</p>
</div>
</div>
<nav class="flex flex-col gap-2 mt-4">
<a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/20 text-primary dark:bg-primary/30 dark:text-white" href="#">
<span class="material-symbols-outlined fill">dashboard</span>
<p class="text-sm font-medium leading-normal">Tableau de bord</p>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg" href="#">
<span class="material-symbols-outlined">group</span>
<p class="text-sm font-medium leading-normal">Utilisateurs</p>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg" href="#">
<span class="material-symbols-outlined">menu_book</span>
<p class="text-sm font-medium leading-normal">Livres</p>
</a>

</nav>
</div>
<div class="flex flex-col gap-1">
<a class="flex items-center gap-3 px-3 py-2 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg" href="#">
<span class="material-symbols-outlined"></span>
<p class="text-sm font-medium leading-normal">Nom de l'administrateur</p>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg" href="#">

<a href="../public/logout.php">Logout</a>
</a>
</div>
</div>
</aside>
<!-- Main Content -->
<main class="flex-1 flex flex-col">
<!-- ToolBar -->
<header class="flex justify-end gap-2 px-6 py-3 bg-white dark:bg-background-dark border-b border-gray-200 dark:border-white/10">
<div class="flex gap-2">
<button class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
<span class="material-symbols-outlined">search</span>
</button>
<button class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
<span class="material-symbols-outlined">notifications</span>
</button>
</div>
</header>
<div class="flex-1 p-6 lg:p-8">
<!-- PageHeading -->
<div class="flex flex-wrap justify-between gap-3 mb-6">
<div class="flex min-w-72 flex-col gap-2">
<h1 class="text-gray-900 dark:text-gray-50 text-3xl font-bold tracking-tight">Tableau de bord  </h1>
<p class="text-gray-500 dark:text-gray-400 text-base font-normal leading-normal">Bienvenue, administrateur ! Voici un résumé de l'activité de la plateforme.</p>
</div>
</div>


<?php 
$stmt = $pdo->prepare('SELECT COUNT(*) FROM books');
$stmt->execute();
$totalBooks = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT SUM(price) FROM books');
$stmt->execute();
$totalBooksValue = $stmt->fetchColumn();
?>
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<div class="flex flex-col gap-2 rounded-xl p-6 border border-gray-200 dark:border-white/10 bg-white dark:bg-background-dark">
<p class="text-gray-800 dark:text-gray-300 text-base font-medium leading-normal">Total des livres</p>
<p class="text-gray-900 dark:text-gray-50 tracking-light text-3xl font-bold leading-tight"><?php echo $totalBooks; ?></p>
<p class="text-green-600 dark:text-green-500 text-sm font-medium leading-normal">+5.2%</p>
</div>
<div class="flex flex-col gap-2 rounded-xl p-6 border border-gray-200 dark:border-white/10 bg-white dark:bg-background-dark">
<p class="text-gray-800 dark:text-gray-300 text-base font-medium leading-normal">Utilisateurs enregistrés</p>
<p class="text-gray-900 dark:text-gray-50 tracking-light text-3xl font-bold leading-tight">
<?php
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users');
$stmt->execute();
$totalUsers = $stmt->fetchColumn();
echo $totalUsers;
?> dh
</p>
<p class="text-green-600 dark:text-green-500 text-sm font-medium leading-normal">+12.1%</p>
</div>
<br><br><br>
</div>
<br><br>
    <h2 style="color:black;  font-weight: bold; font-size: 35px;">Tous les livres</h2> <br><br>
    <?php
    $stmt = $pdo->prepare('
        SELECT b.*, u.name AS owner_name
        FROM books b
        JOIN users u ON b.owner_id = u.id
        ORDER BY b.created_at DESC
    ');
    $stmt->execute();
    $allBooks = $stmt->fetchAll();
    ?>
    <?php if (count($allBooks) === 0): ?>
      <p>Aucun livre dans la base de données.</p>
    <?php else: ?>
      <table style="width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.08);border-collapse:collapse;font-family:'Segoe UI',Arial,sans-serif;">
        <thead>
          <tr style="background:#213e5e;color:#fff;">
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Titre</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Auteur</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Type</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">État</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Prix</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Propriétaire</th>
            <th style="padding:1rem .75rem;text-align:center;font-weight:600;letter-spacing:.5px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($allBooks as $index => $book): ?>
            <tr style="border-bottom:1px solid #f0f0f0;transition:background .2s;">
              <td style="padding:1rem .75rem;color:#222;"><?php echo htmlspecialchars($book['title']); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['author'] ?? ''); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['type']); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['etat'] ?? ''); ?></td>
              <td style="padding:1rem .75rem;color:#213e5e;font-weight:600;"><?php echo $book['price'] ? number_format($book['price'],2,',',' ').' Dh' : 'Gratuit'; ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['owner_name']); ?></td>
              <td style="padding:1rem .75rem;text-align:center;">
                <form action="dashboard_admin.php" method="post" style="display:inline;">
                  <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                  <button type="submit" name="delete_book" style="background-color:#e74c3c;color:#fff;border:none;padding:.5rem 1rem;border-radius:6px;cursor:pointer;font-size:.85rem;font-weight:600;transition:background .2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table> <br><br><br>
    <?php endif; ?>
</div>
</main>
</div>

</main>

<?php
// Vérifier que la requête est bien POST et que les données nécessaires existent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'], $_POST['book_id'])) {
    $bookId = (int)$_POST['book_id'];

    // Démarrer une transaction pour garantir l'intégrité des suppressions
    $pdo->beginTransaction();

    try {
        // Supprimer les enregistrements liés dans la table wishlists
        $stmt = $pdo->prepare('DELETE FROM wishlists');
        $stmt->execute([$bookId]);

        // Supprimer les enregistrements liés dans la table exchanges
        $stmt = $pdo->prepare('DELETE FROM exchanges ?');
        $stmt->execute([$bookId]);

        // Supprimer le livre lui-même
        $stmt = $pdo->prepare('DELETE FROM books ');
        $stmt->execute([$bookId]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger avec succès
        header('Location: dashboard_admin.php');
        exit;
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        // Rediriger avec échec
        // Utiliser JavaScript pour rediriger après que la sortie ait déjà été envoyée
        echo '<script>window.location.href="dashboard_admin.php?deleted=0";</script>';
        exit;
    }
}
?>





</body>
</html>