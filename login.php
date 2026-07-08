<?php
// login.php - Traitement connexion
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- Connexion base de donnees ---
    $host = 'localhost';
    $db   = 'techshop_sn';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
            $_SESSION['user_id']  = $utilisateur['id'];
            $_SESSION['user_nom'] = $utilisateur['nom'];
            header('Location: add-product.php');
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $erreur = "Erreur de connexion : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - TechShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php if (!empty($erreur)): ?>
    <div style="background:#fee2e2;color:#dc2626;padding:12px 20px;text-align:center;font-size:.88rem">
      <?= htmlspecialchars($erreur) ?>
    </div>
  <?php endif; ?>

  <header>
    <div class="header-inner">
      <a href="index.html" class="logo"><span class="logo-name">TechShop</span><br><span class="logo-sub">Senegal</span></a>
      <nav><a href="index.html">Accueil</a><a href="about.html">A Propos</a><a href="categories.html">Categories</a></nav>
      <div class="header-actions"><a href="login.php" class="btn-login">Se connecter</a><a href="register.php" class="btn-register">S'inscrire</a></div>
    </div>
  </header>

  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-logo"><a href="index.html"><span class="name">TechShop</span><br><span class="sub">Senegal</span></a></div>
      <h2>Connexion</h2>
      <p class="auth-subtitle">Accedez a votre compte TechShop</p>
      <form action="login.php" method="POST">
        <div class="form-group">
          <label>Adresse email</label>
          <div class="input-wrap">
            <span class="icon">&#9993;</span>
            <input type="email" name="email" placeholder="exemple@email.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label>Mot de passe</label>
          <div class="input-wrap">
            <span class="icon">&#128274;</span>
            <input type="password" id="pw" name="password" placeholder="Votre mot de passe" required>
            <button type="button" class="toggle-pw" onclick="var i=document.getElementById('pw');i.type=i.type=='password'?'text':'password'">&#128065;</button>
          </div>
        </div>
        <button type="submit" class="btn-submit dark">Se connecter</button>
        <div class="divider"><span>ou</span></div>
        <p class="auth-switch">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
      </form>
    </div>
  </div>
</body>
</html>
