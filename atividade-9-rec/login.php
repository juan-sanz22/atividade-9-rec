<?php
session_start();

$mysqli = new mysqli("localhost", "root", "root", "login_db");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["username"] ?? "");
    $pass = $_POST["password"] ?? "";

    if ($user === "" || $pass === "") {
        $msg = "Preencha usuário e senha.";
    } else {
        $stmt = $mysqli->prepare("SELECT id, username, senha FROM usuarios WHERE username = ?");
        if (!$stmt) {
            die("Prepare falhou: " . $mysqli->error);
        }
        $stmt->bind_param("s", $user);
        $stmt->execute();

        $dados = null;
        if (method_exists($stmt, 'get_result')) {
            $res = $stmt->get_result();
            $dados = $res->fetch_assoc();
        } else {
            $stmt->bind_result($id, $username, $senha);
            if ($stmt->fetch()) {
                $dados = ['id'=>$id, 'username'=>$username, 'senha'=>$senha];
            }
        }
        $stmt->close();

        if ($dados) {
            $stored = $dados['senha'];
            $ok = false;

            if (password_verify($pass, $stored)) {
                $ok = true;
            }
            elseif ($pass === $stored) {
                $newHash = password_hash($pass, PASSWORD_DEFAULT);
                $upd = $mysqli->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                $upd->bind_param("si", $newHash, $dados['id']);
                $upd->execute();
                $upd->close();
                $ok = true;
            }

            if ($ok) {
                $_SESSION["user_id"] = $dados["id"];
                $_SESSION["username"] = $dados["username"];
                header("Location: dashboard.php");
                exit;
            }
        }

        $msg = "Usuário ou senha incorretos!";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login Simples</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <h3>Login</h3>
    <?php if ($msg): ?><p class="msg"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Usuário" required>
      <input type="password" name="password" placeholder="Senha" required>
      <button type="submit">Entrar</button>
    </form>
    <p><small>Dica: admin / 123</small></p>
  </div>
</body>
</html>