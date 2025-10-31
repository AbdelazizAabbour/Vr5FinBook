<?php
require_once __DIR__ . '/../app/db.php';
session_start();
if (!isset($_SESSION['user_id'])){ header('Location: /login.php'); exit; }
$uid = $_SESSION['user_id'];

// Stats
$stmt = $pdo->prepare('SELECT COUNT(*) FROM books WHERE owner_id = ?');
$stmt->execute([$uid]);
$totalBooks = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT SUM(price) FROM books WHERE owner_id = ? AND price IS NOT NULL');
$stmt->execute([$uid]);
$totalValue = $stmt->fetchColumn() ?: 0;

// Liste des livres
$stmt = $pdo->prepare('SELECT * FROM books WHERE owner_id = ? ORDER BY created_at DESC');
$stmt->execute([$uid]);
$mybooks = $stmt->fetchAll();

// Récupérer les infos actuelles de l'utilisateur
$stmt = $pdo->prepare('SELECT name, email, university, major, avatar FROM users WHERE id = ?');
$stmt->execute([$uid]);
$user = $stmt->fetch();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="BookShare - Partagez vos livres et trouvez des livres à échanger">
  <meta name="keywords" content="livres, échange, partage, université">
  <link rel="icon" href="../pics/logo.png" type="image/x-icon">
  <title>Tableau de bord - Bookshare</title>
  <style>
    :root{--primary:#213e5e;--light:#ffffff;}
    *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;}
    body{background:#f5f5f5;color:#333;}
    header{background:var(--primary);color:var(--light);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;}
    header h1{font-size:1.4rem;}
    header a{color:var(--light);text-decoration:none;font-weight:bold;}
    .container{max-width:1200px;margin:auto;padding:2rem;}
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;margin-bottom:2rem;}
    .card{background:var(--light);border-radius:8px;padding:1.5rem;box-shadow:0 2px 6px rgba(0,0,0,.1);}
    .card h3{margin-bottom:.5rem;color:var(--primary);}
    .card .num{font-size:2rem;font-weight:bold;color:#222;}
    .btn{display:inline-block;background:var(--primary);color:var(--light);padding:.7rem 1.2rem;border-radius:4px;text-decoration:none;margin-bottom:2rem;}
    .btn:hover{opacity:.9;}
    .book-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;}
    .book-card{background:var(--light);border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.1);display:flex;flex-direction:column;}
    .book-card img{width:100%;height:260px;object-fit:cover;background:#ddd;}
    .book-card .info{padding:1rem;flex:1;display:flex;flex-direction:column;justify-content:space-between;}
    .book-card .title{font-weight:bold;margin-bottom:.3rem;}
    .book-card .type{font-size:.9rem;color:#555;margin-bottom:.5rem;}
    .book-card .price{font-size:1rem;color:var(--primary);}
    form{background:var(--light);border-radius:8px;padding:1.5rem;box-shadow:0 2px 6px rgba(0,0,0,.1);margin-bottom:2rem;}
    form h2{margin-bottom:1rem;color:var(--primary);}
    .form-group{margin-bottom:1rem;}
    label{display:block;margin-bottom:.3rem;font-weight:bold;}
    input,select,textarea{width:100%;padding:.6rem;border:1px solid #ccc;border-radius:4px;}
    button{background:var(--primary);color:var(--light);border:none;padding:.7rem 1.2rem;border-radius:4px;cursor:pointer;}
    button:hover{opacity:.9;}
    .msg-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:1rem;border-radius:4px;margin-bottom:1rem;}
    .msg-error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:1rem;border-radius:4px;margin-bottom:1rem;}
  </style>
</head>
<body>
  <header>
    <a href="../public/logout.php">
      <img src="<?php 
        if (!empty($user['avatar'])) {
            echo '../' . htmlspecialchars($user['avatar']);
        } else {
            echo '../pics/11.png';
        }
      ?>" 
           alt="Avatar" 
           style="width:60px;height:60px;object-fit:cover;border-radius:50%;margin-right:.5rem;vertical-align:middle;">
    </a>
    <h1>Tableau de bord - Bonjour <?php echo htmlspecialchars($user['name']); ?></h1>
    <a href="../public/logout.php">Déconnexion</a>
  </header>

  <div class="container">
    <!-- Statistiques -->
    <div class="stats">
      <div class="card">
        <h3>Total livres</h3>
        <div class="num"><?php echo $totalBooks; ?></div>
      </div>
      <div class="card">
        <h3>Valeur estimée</h3>
        <div class="num"><?php echo number_format($totalValue,2,',',' '); ?> Dh</div>
      </div>
    </div>

    <!-- Messages éventuels -->
    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
      <div class="msg-success">Profil modifié avec succès !</div>
    <?php endif; ?>
    <?php if (isset($_GET['bookadded']) && $_GET['bookadded'] == 1): ?>
      <div class="msg-success">Livre ajouté avec succès !</div>
    <?php endif; ?>

    <!-- Formulaire de mise à jour du profil -->
    <form action="../public/update_profile.php" method="post" enctype="multipart/form-data">
      <h2>Mettre à jour mon profil</h2>
      <div class="form-group">
        <label>Nom</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
      </div>
      <div class="form-group">
        <label>Mot de passe (laisser vide pour conserver l'actuel)</label>
        <input type="password" name="password" placeholder="Nouveau mot de passe">
      </div>
      <div class="form-group">
        <label>Université</label>
        <input type="text" name="university" value="<?php echo htmlspecialchars($user['university'] ?? ''); ?>">
      </div>
      <div class="form-group">
        <label>Filière</label>
        <input type="text" name="major" value="<?php echo htmlspecialchars($user['major'] ?? ''); ?>">
      </div>
     
      <button type="submit" name="update_profile">Enregistrer les modifications</button>
    </form>

    <!-- Formulaire d'ajout de livre -->
    <form action="../public/add_book.php" method="post" enctype="multipart/form-data">
  <h2>Ajouter un nouveau livre</h2>

  <div class="form-group">
    <label>Titre</label>
    <input type="text" name="title" required>
  </div>

  <div class="form-group">
    <label>Auteur</label>
    <input type="text" name="author" required>
  </div>

  <div class="form-group">
    <label>État du livre</label>
    <select name="book_condition" required>
      <option value="new">Neuf</option>
      <option value="good">Bon</option>
      <option value="used">Utilisé</option>
      <option value="worn">Abîmé</option>
    </select>
  </div>

  <div class="form-group">
    <label>Type</label>
    <select name="type" required>
      <option value="lend">Prêt</option>
      <option value="exchange">Échange</option>
      <option value="sell">Vente</option>
    </select>
  </div>

  <div class="form-group">
    <label>Prix (Dh)</label>
    <input type="number" step="0.01" name="price">
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="3" placeholder="Description du livre..."></textarea>
  </div>

  <div class="form-group">
    <label>Photo de couverture</label>
    <input type="file" name="image" accept="image/*">
  </div>

  <button type="submit">Ajouter le livre</button>
    </form>


    <!-- Liste de tous les livres (tableau avec suppression) -->
    <!-- <h2>Tous les livres</h2> <br><br> -->
    <?php
    // Récupérer tous les livres avec les infos de leur propriétaire
    $stmt = $pdo->prepare('
        SELECT b.*, u.name AS owner_name
        FROM books b
        JOIN users u ON b.owner_id = u.id
        ORDER BY b.created_at DESC
    ');
    $stmt->execute();
    $allBooks = $stmt->fetchAll();
    ?>
    <!-- <?php if (count($allBooks) === 0): ?>
      <p>Aucun livre dans la base de données.</p>
    <?php else: ?> -->
      <table style="width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.08);border-collapse:collapse;font-family:'Segoe UI',Arial,sans-serif;">
        <thead>
          <!-- <tr style="background:#213e5e;color:#fff;">
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Titre</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Auteur</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Type</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">État</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Prix</th>
            <th style="padding:1rem .75rem;text-align:left;font-weight:600;letter-spacing:.5px;">Propriétaire</th>
            <th style="padding:1rem .75rem;text-align:center;font-weight:600;letter-spacing:.5px;">Action</th>
          </tr> -->
        </thead>
        <tbody>
          <?php foreach($allBooks as $index => $book): ?>
            <!-- <tr style="border-bottom:1px solid #f0f0f0;transition:background .2s;">
              <td style="padding:1rem .75rem;color:#222;"><?php echo htmlspecialchars($book['title']); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['author'] ?? ''); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['type']); ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['etat'] ?? ''); ?></td>
              <td style="padding:1rem .75rem;color:#213e5e;font-weight:600;"><?php echo $book['price'] ? number_format($book['price'],2,',',' ').' Dh' : 'Gratuit'; ?></td>
              <td style="padding:1rem .75rem;color:#555;"><?php echo htmlspecialchars($book['owner_name']); ?></td>
              <td style="padding:1rem .75rem;text-align:center;">
                <form action="../public/delete_book.php" method="post" style="display:inline;">
                  <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                  <button type="submit" style="background-color: red  ;;color:#fff;border:none;padding:.5rem 1rem;border-radius:6px;cursor:pointer;font-size:.85rem;font-weight:600;transition:background .2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">Supprimer</button>
                </form>
              </td>
            </tr> -->
          <?php endforeach; ?>
        </tbody>
      </table> <br><br><br>
    <?php endif; ?>

    <!-- Liste des livres -->
    <h2>Mes livres</h2> <br><br>
   <?php if (count($mybooks) === 0): ?>
  <p>Vous n'avez pas encore ajouté de livre.</p>
<?php else: ?>
  <div class="book-grid">
    <?php foreach($mybooks as $b): ?>
      <div class="book-card">
        <img src="<?php 
            // Vérifie si une image est disponible dans la colonne 'image'
            if (!empty($b['image'])) {
                echo '../public/' . htmlspecialchars($b['image']);
            } else {
                echo '../pics/11.webp'; // image par défaut
            }
        ?>" 
        alt="Couverture du livre"
        style="width:100%;height:220px;object-fit:cover;border-radius:10px;">
        
        <div class="info" style="padding:10px;">
          <div>
            <div class="title" style="font-weight:bold;">
              <?php echo htmlspecialchars($b['title']); ?>
            </div>
            <div class="type" style="font-size:13px;color:#666;">
              <?php echo htmlspecialchars($b['type']); ?>
            </div>
            <div class="etat" style="font-size:13px;color:#009879;">
              <?php echo htmlspecialchars($b['etat'] ?? ''); ?>
            </div>
            <div class="details" style="font-size:13px;color:#555;">
              <?php echo htmlspecialchars($b['author'] ?? ''); ?>
            </div>
          </div>
          <div class="price" style="font-size:14px;color:#007bff;margin-top:6px;">
            <?php echo $b['price'] ? number_format($b['price'], 2, ',', ' ') . ' Dh' : 'Gratuit'; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

  </div>

    <!-- Boîte de messagerie entre utilisateurs -->
    <div class="card" style="margin-bottom:2rem;">
      <h3 style="margin-bottom:1rem;">Messagerie</h3>

      <!-- Liste des conversations -->
      <div id="conv-list" style="max-height:200px;overflow-y:auto;border:1px solid #ddd;border-radius:6px;padding:.5rem;background:#fafafa;">
        <?php
        $stmt = $pdo->prepare('
            SELECT DISTINCT u.id, u.name
            FROM messages m
            JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id)
            WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?
            ORDER BY m.created_at DESC
        ');
        $stmt->execute([$uid, $uid, $uid]);
        $convs = $stmt->fetchAll();
        if (!$convs): ?>
          <p style="color:#777;font-size:.9rem;">Aucune conversation.</p>
        <?php else: ?>
          <?php foreach ($convs as $c): ?>
            <div class="conv-item" data-uid="<?php echo $c['id']; ?>" style="padding:.5rem;cursor:pointer;border-radius:4px;margin-bottom:.25rem;">
              <?php echo htmlspecialchars($c['name']); ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Zone d’envoi -->
      <!-- Zone d’envoi -->
      <form id="msg-form" style="margin-top:1rem;display:flex;gap:.75rem;align-items:center;">
        <input type="hidden" id="receiver_id" value="">
        <input type="text" id="msg-input" placeholder="Tapez votre message…" style="flex:1;padding:.75rem 1rem;border:1px solid #ccc;border-radius:24px;font-size:.95rem;outline:none;transition:border .2s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='#ccc';">
        <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.65rem 1.2rem;border-radius:50%;cursor:pointer;font-size:1rem;transition:background .2s,transform .1s;" onmouseover="this.style.background='#1a2f4a';" onmouseout="this.style.background='var(--primary)';" onmousedown="this.style.transform='scale(.95)';" onmouseup="this.style.transform='scale(1)';">➤</button>
      </form>

      <!-- Historique des messages -->
      <div id="msg-history" style="margin-top:1rem;max-height:250px;overflow-y:auto;border:1px solid #eee;border-radius:12px;padding:1rem;background:#fafafa;display:flex;flex-direction:column;gap:.5rem;">
        <?php
        // Display last 20 messages between current user and default receiver (first in list)
        if (!empty($convs)) {
            $defaultReceiver = $convs[0]['id'];
            $stmt = $pdo->prepare('
                SELECT body, sender_id, created_at
                FROM messages
                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                ORDER BY created_at ASC
                LIMIT 20
            ');
            $stmt->execute([$uid, $defaultReceiver, $defaultReceiver, $uid]);
            $messages = $stmt->fetchAll();
            if (!$messages) {
                echo '<p style="color:#777;font-size:.9rem;text-align:center;">Aucun message.</p>';
            } else {
                foreach ($messages as $m) {
                    $isMe = $m['sender_id'] == $uid;
                    echo '
                    <div style="align-self:'.($isMe?'flex-end':'flex-start').';max-width:70%;">
                        <span style="display:inline-block;background:'.($isMe?'var(--primary)':'#fff').';color:'.($isMe?'#fff':'#222').';padding:.6rem 1rem;border-radius:18px;font-size:.9rem;box-shadow:0 1px 3px rgba(0,0,0,.08);">'
                        .htmlspecialchars($m['body']).
                        '</span>
                        <div style="font-size:.7rem;color:#888;margin-top:.25rem;text-align:'.($isMe?'right':'left').';">'.date('H:i', strtotime($m['created_at'])).'</div>
                    </div>';
                }
            }
        } else {
            echo '<p style="color:#777;font-size:.9rem;text-align:center;">Sélectionnez une conversation.</p>';
        }
        ?>
      </div>

      <!-- Historique des messages -->
      <div id="msg-history" style="margin-top:1rem;max-height:250px;overflow-y:auto;border:1px solid #eee;border-radius:6px;padding:.75rem;background:#fff;"></div>
    </div>

    <script>
    // Charger les messages d’un correspondant
    function loadMessages(rid){
      fetch('../public/get_messages.php?rid='+rid)
        .then(r=>r.json())
        .then(data=>{
          const box=document.getElementById('msg-history');
          box.innerHTML='';
          data.forEach(m=>{
            const div=document.createElement('div');
            div.style.marginBottom='.5rem';
            div.style.textAlign= m.sender_id==<?php echo $uid;?> ? 'right' : 'left';
            div.innerHTML=`<span style="display:inline-block;background:${m.sender_id==<?php echo $uid;?>?'#213e5e':'#eee'};color:${m.sender_id==<?php echo $uid;?>?'#fff':'#222'};padding:.4rem .8rem;border-radius:12px;font-size:.9rem;">${m.body}</span>`;
            box.appendChild(div);
          });
          box.scrollTop=box.scrollHeight;
        });
    }

    // Choisir un correspondant
    document.querySelectorAll('.conv-item').forEach(item=>{
      item.addEventListener('click',()=>{
        document.querySelectorAll('.conv-item').forEach(i=>i.style.background='');
        item.style.background='#e3f2fd';
        const rid=item.dataset.uid;
        document.getElementById('receiver_id').value=rid;
        loadMessages(rid);
      });
    });

    // Envoyer un message
    document.getElementById('msg-form').addEventListener('submit',e=>{
      e.preventDefault();
      const rid=document.getElementById('receiver_id').value;
      const body=document.getElementById('msg-input').value.trim();
      if(!rid || !body)return;
      fetch('../public/send_message.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({receiver_id:rid,body:body})
      }).then(()=>{
        document.getElementById('msg-input').value='';
        loadMessages(rid);
      });
    });
    </script>
</body>
</html>
