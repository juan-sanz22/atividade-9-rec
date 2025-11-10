<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <h2>Bem-vindo ao Dashboard, <?= htmlspecialchars($_SESSION["username"]) ?>!</h2>
    <p>Você está logado com sucesso.</p>
    <ul>
      <li><a href="#">📚 Página interna 1</a></li>
      <li><a href="#">⚙️ Página interna 2</a></li>
    </ul>
    <p><a href="logout.php">Sair</a></p>
  </div>
</body>
</html>