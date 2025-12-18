<?php
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (!isset($_SESSION['tentativas'])) $_SESSION['tentativas'] = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['usuario'] ?? '';
  $pass = $_POST['senha'] ?? '';
  $hash = password_hash('123456', PASSWORD_DEFAULT);
  // Usuário hardcoded (pode adaptar para MySQL)
  if ($_SESSION['tentativas'] > 5) {
    $erro = 'Muitas tentativas. Tente novamente em 5 minutos.';
  } elseif (($user === 'admin' && password_verify($pass, $hash)) || autenticaMySQL($user, $pass)) {
    $_SESSION['admin'] = true;
    $_SESSION['tentativas'] = 0;
    session_regenerate_id(true);
    // Log de acesso
    require_once '../config.php';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $conn->prepare("INSERT INTO admin_logs (usuario, acao, ip) VALUES (?, 'login', ?)");
    $stmt->bind_param('ss', $user, $ip);
    $stmt->execute();
    $stmt->close();
    header('Location: /admin/index.php');
    exit;
  } else {
    $_SESSION['tentativas']++;
    $erro = 'Usuário ou senha inválidos.';
  }
}
function autenticaMySQL($user, $pass) {
  // Opcional: autenticação via MySQL
  return false;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-[#0ea5e9] via-[#1e3a8a] to-[#0b1726] min-h-screen flex items-center justify-center">
  <form method="post" class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-sm">
    <h2 class="text-2xl font-bold text-[#1e3a8a] mb-6 text-center">Login Admin</h2>
    <?php if (!empty($erro)) echo "<div class='mb-4 text-red-600'>$erro</div>"; ?>
    <input name="usuario" type="text" placeholder="Usuário" class="w-full mb-4 px-4 py-2 rounded-lg border border-[#0ea5e9] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8]" required>
    <input name="senha" type="password" placeholder="Senha" class="w-full mb-6 px-4 py-2 rounded-lg border border-[#0ea5e9] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8]" required>
    <button type="submit" class="w-full bg-[#1e3a8a] text-white rounded-lg py-2 font-semibold shadow hover:bg-[#0ea5e9] transition-all duration-300">Entrar</button>
  </form>
</body>
</html>