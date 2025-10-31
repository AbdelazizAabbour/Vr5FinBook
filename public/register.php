<?php
require_once __DIR__ . '/../app/auth/login.php';
// --- PHP : register logic ----------------------------------------------------
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve & sanitize
    $name       = trim($_POST['name']       ?? '');
    $email      = trim($_POST['email']      ?? '');
    $password   = trim($_POST['password']   ?? '');
    $password2  = trim($_POST['password2']  ?? '');
    $university = trim($_POST['university'] ?? '');
    $major      = trim($_POST['major']      ?? '');
    $numTele    = trim($_POST['numTele']    ?? '');

    // Handle profile picture upload
    $profileImagePath = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileType = $_FILES['avatar']['type'];
        $fileSize = $_FILES['avatar']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            $uploadDir = __DIR__ . '/../uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_', true) . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $profileImagePath = 'uploads/profiles/' . $filename;
            } else {
                $error = 'Erreur lors du téléchargement de l\'image.';
            }
        } else {
            $error = 'Image invalide (formats autorisés : JPG, PNG, GIF, max 2MB).';
        }
    }

    // Validation
    if (!$name || !$email || !$password || !$password2) {
        $error = 'Tous les champs obligatoires doivent être remplis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } elseif ($password !== $password2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        try {
            // Connexion locale (XAMPP) – ajuste host / db / user / pass selon ton environnement
            $pdo = new PDO(
                'mysql:host=localhost;dbname=bookshare_php;charset=utf8mb4',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Cet e-mail est déjà utilisé.';
            } else {
                // Hacher le mot de passe
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password, university, major, avatar, numTele, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())'
                );
                $stmt->execute([$name, $email, $hash, $university, $major, $profileImagePath, $numTele]);
                $success = 'Compte créé avec succès ! Redirection vers la page de connexion…';
                header('Refresh:2; url=login.php');
            }
        } catch (PDOException $e) {
            // Message générique pour l'utilisateur + log interne
            error_log('Erreur PDO inscription : ' . $e->getMessage());
            $error = 'Erreur de connexion à la base de données. Veuillez réessayer plus tard.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BookShare - Inscription</title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="icon" href="../pics/logo.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ----------  THEME #213e5e  ---------- */
    :root{
      --primary:#213e5e;
      --primary-dark:#1a324d;
      --light:#f5f7fa;
      --danger:#d93025;
      --success:#28a745;
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
    body{
      display:flex;
      flex-direction:column;
      min-height:100vh;
      color:#222;
    }
    main{
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:120px 20px 60px;
    }
    /* Card */
    .form-card{
      background:#fff;
      padding:50px 40px;
      border-radius:14px;
      box-shadow:0 10px 30px rgba(0,0,0,.12);
      width:100%;
      max-width:480px;
      animation:fadeIn .6s ease;
    }
    @keyframes fadeIn{
      from{opacity:0;transform:translateY(20px)}
      to{opacity:1;transform:translateY(0)}
    }
    h2{margin-bottom:30px;text-align:center;color:var(--primary)}
    label{display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:#555}
    input[type=text],input[type=email],input[type=password],input[type=file]{
      width:100%;
      padding:12px 15px;
      border:1px solid #ccc;
      border-radius:8px;
      transition:border .3s,box-shadow .3s;
      margin-bottom:20px;
    }
    input[type=text]:focus,input[type=email]:focus,input[type=password]:focus,input[type=file]:focus{
      outline:none;
      border-color:var(--primary);
      box-shadow:0 0 0 3px rgba(33,62,94,.15);
    }
    button{
      width:100%;
      padding:14px;
      background:var(--primary);
      color:#fff;
      border:none;
      border-radius:8px;
      font-size:16px;
      font-weight:600;
      cursor:pointer;
      transition:background .3s;
    }
    button:hover{background:var(--primary-dark)}
    .error{color:var(--danger);font-size:14px;margin-bottom:15px;text-align:center}
    .success{color:var(--success);font-size:14px;margin-bottom:15px;text-align:center}
    .link{text-align:center;margin-top:20px;font-size:14px}
    .link a{color:var(--primary);text-decoration:none;font-weight:600}

    /* Header (same as before) */
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 72px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 40px;
      z-index: 1000;
      background-color: var(--primary);
      color: #fff;
    }
    #branding {
      font-size: 24px;
      font-weight: 700;
      letter-spacing: 1px;
    }
    #main-menu {
      display: flex;
      align-items: center;
      gap: 24px;
    }
    #main-menu a {
      color: #fff;
      text-decoration: none;
      font-size: 18px;
      transition: color 0.2s;
    }
    #main-menu a:hover {
      color: #a0c1e0;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div id="branding">BookShare | CMC</div>
    <nav id="main-menu">
      <div style="display:flex; gap:24px;">
        <a href="../public/index.php">Home</a>
        <a href="../public/news.php ">News</a>
      </div>
      <a href="../public/login.php" aria-label="Connexion" title="Connexion"><i class="fa-solid fa-right-to-bracket" style="color:#fff;font-size:18px;"></i></a>
      <a href="#" aria-label="Langue" title="Langue"><i class="fa-solid fa-globe" style="color:#fff;font-size:18px;"></i></a>
    </nav>
  </header>

<main>
  <section class="form-card">
    <h2>Inscription</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

    <form id="registerForm" method="post" enctype="multipart/form-data" novalidate>
      <!-- Row 1 -->
      <div class="field-row">
        <div class="field">
          <label>Nom complet *</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="field">
          <label>Email *</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="field">
          <label>Numéro de téléphone</label>
          <input type="text" id="numTele" name="numTele" placeholder="Facultatif">
        </div>
      </div>

      <!-- Row 2 -->
      <div class="field-row">
        <div class="field">
          <label>Mot de passe * (min. 6 caractères)</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="field">
          <label>Confirmer le mot de passe *</label>
          <input type="password" id="password2" name="password2" required>
        </div>
      </div>

      <!-- Row 3 -->
      <div class="field-row">
        <div class="field">
          <label>Université</label>
          <input type="text" id="university" name="university" placeholder="Facultatif">
        </div>
        <div class="field">
          <label>Filière / Majeur</label>
          <input type="text" id="major" name="major" placeholder="Facultatif">
        </div>
      </div>

      <!-- Profile Image Upload -->
      <div class="field">
        <label>Image de profil (facultatif)</label>
        <input type="file" id="avatar" name="avatar" accept="image/*">
      </div>

      <button type="submit">Créer mon compte</button>
    </form>

    <p class="link">Déjà inscrit ? <a href="../public/login.php">Se connecter</a></p>
  </section>
</main>

  <!-- Footer (same as before) -->
  <footer style="background:var(--primary); color:#ffffff; padding:60px 0 40px; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
    <div style="max-width:1200px; margin:0 auto; display:flex; flex-wrap:wrap; gap:40px; justify-content:space-between;">
      <div style="flex:1 1 250px;">
        <h3 style="margin-bottom:16px; font-size:22px; letter-spacing:1px;">BookShare | CMC</h3>
        <p style="font-size:14px; line-height:1.6; opacity:.9;">
          La plateforme étudiante pour partager, découvrir et redonner vie aux livres. 
          Ensemble, cultivons la lecture sans contraintes.
        </p>
      </div>
      <div style="flex:1 1 180px;">
        <h4 style="margin-bottom:16px; font-size:18px;">Navigation</h4>
        <ul style="list-style:none; padding:0;">
          <li style="margin-bottom:8px;"><a href="/" style="color:#ffffff; text-decoration:none; opacity:.85; transition:opacity .2s;">Accueil</a></li>
          <li style="margin-bottom:8px;"><a href="/catalogue.php" style="color:#ffffff; text-decoration:none; opacity:.85; transition:opacity .2s;">Catalogue</a></li>
          <li style="margin-bottom:8px;"><a href="/publish.php" style="color:#ffffff; text-decoration:none; opacity:.85; transition:opacity .2s;">Publier un livre</a></li>
          <li style="margin-bottom:8px;"><a href="/contact.php" style="color:#ffffff; text-decoration:none; opacity:.85; transition:opacity .2s;">Contact</a></li>
        </ul>
      </div>
      <div style="flex:1 1 220px;">
        <h4 style="margin-bottom:16px; font-size:18px;">Moyens de paiement</h4>
        <div style="display:flex; gap:12px; align-items:center;">
          <img src="https://www.workker.fr/img/cms/logo-stripe%20(1).png" alt="Stripe" style="height:65px;">
        </div>
        <p style="margin-top:12px; font-size:12px; opacity:.7;">Paiement sécurisé & crypté SSL 256-bit</p>
      </div>
      <div style="flex:1 1 220px;">
        <h4 style="margin-bottom:16px; font-size:18px;">Suivez-nous</h4>
        <div style="display:flex; gap:12px; margin-bottom:20px;">
          <a href="#" aria-label="Facebook" style="color:#ffffff; font-size:20px;"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram" style="color:#ffffff; font-size:20px;"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="Twitter" style="color:#ffffff; font-size:20px;"><i class="fa-brands fa-twitter"></i></a>
          <a href="#" aria-label="LinkedIn" style="color:#ffffff; font-size:20px;"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
        <ul style="list-style:none; padding:0; font-size:12px; opacity:.7;">
          <li style="margin-bottom:6px;"><a href="/terms.php" style="color:#ffffff; text-decoration:none;">Conditions d'utilisation</a></li>
          <li style="margin-bottom:6px;"><a href="/privacy.php" style="color:#ffffff; text-decoration:none;">Politique de confidentialité</a></li>
          <li><a href="/cookies.php" style="color:#ffffff; text-decoration:none;">Gestion des cookies</a></li>
        </ul>
      </div>
    </div>
    <div style="text-align:center; margin-top:40px; padding-top:20px; border-top:1px solid rgba(255,255,255,.2); font-size:12px; opacity:.7;">
      © <?= date('Y') ?> BookShare CMC. Tous droits réservés.
    </div>
  </footer>

  <script>
    // Light client-side validation
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', e => {
      const pwd  = form.password.value.trim();
      const pwd2 = form.password2.value.trim();
      if(pwd !== pwd2){
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
      }
    });
  </script>

</body>
</html>
