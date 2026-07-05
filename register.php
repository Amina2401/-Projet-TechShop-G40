<?php
// register.php - Traitement inscription
session_start();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $tel      = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caracteres.";
    } else {
        $host = 'localhost';
        $db   = 'techshop_sn';
        $user = 'root';
        $pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verifier si email deja utilise
            $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $erreur = "Cet email est deja utilise.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare(
                    "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, created_at)
                     VALUES (?, ?, ?, ?, ?, NOW())"
                );
                $insert->execute([$nom, $prenom, $email, $tel, $hash]);
                $succes = "Compte cree avec succes ! Vous pouvez maintenant vous connecter.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription - TechShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php if ($erreur): ?>
    <div style="background:#fee2e2;color:#dc2626;padding:12px 20px;text-align:center;font-size:.88rem"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>
  <?php if ($succes): ?>
    <div style="background:#d1fae5;color:#065f46;padding:12px 20px;text-align:center;font-size:.88rem"><?= htmlspecialchars($succes) ?> <a href="login.php" style="font-weight:700;text-decoration:underline">Se connecter</a></div>
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
      <h2>Creer un compte</h2>
      <p class="auth-subtitle">Rejoignez la communaute TechShop</p>
      <form action="register.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Nom</label>
            <div class="input-wrap"><span class="icon">&#128100;</span><input type="text" name="nom" placeholder="Diallo" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required></div>
          </div>
          <div class="form-group">
            <label>Prenom</label>
            <div class="input-wrap"><span class="icon">&#128100;</span><input type="text" name="prenom" placeholder="Amadou" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required></div>
          </div>
        </div>
        <div class="form-group">
          <label>Email</label>
          <div class="input-wrap"><span class="icon">&#9993;</span><input type="email" name="email" placeholder="exemple@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></div>
        </div>
        <div class="form-group">
          <label>Telephone</label>
          <div class="input-wrap"><span class="icon">&#128222;</span><input type="tel" name="telephone" placeholder="+221 77 000 00 00" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"></div>
        </div>
        <div class="form-group">
          <label>Mot de passe</label>
          <div class="input-wrap"><span class="icon">&#128274;</span><input type="password" id="pw" name="password" placeholder="Min. 6 caracteres" required></div>
        </div>
        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <div class="input-wrap"><span class="icon">&#128274;</span><input type="password" name="confirm" placeholder="Repetez le mot de passe" required></div>
        </div>
        <label class="checkbox-label">
          <input type="checkbox" required>
          J'accepte les <a href="#">conditions d'utilisation</a>
        </label>
        <button type="submit" class="btn-submit gold">Creer mon compte</button>
        <p class="auth-switch">Deja un compte ? <a href="login.php">Se connecter</a></p>
      </form>
    </div>
  </div>
</body>
</html>
