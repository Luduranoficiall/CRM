<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
require_once '../config.php';
$id = $_GET['id'] ?? 0;
$res = $conn->query("SELECT * FROM leads WHERE id = " . intval($id));
require_once '../config.php';
$lead = $res->fetch_assoc();
foreach(['nome','email','telefone','instagram','ramo','faturamento_raw','invest_raw','objetivo','faz_trafego'] as $campo) {
  if (isset($lead[$campo])) $lead[$campo] = crypto_decrypt($lead[$campo]);
}
if (!$lead) die('Lead não encontrado');
$tags = json_decode($lead['tags_ai'], true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalhes do Lead</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-[#0ea5e9] via-[#1e3a8a] to-[#0b1726] min-h-screen">
  <div class="container mx-auto px-4 py-8">
    <a href="/admin/index.php" class="text-[#1e3a8a] underline mb-4 inline-block">← Voltar</a>
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <h2 class="text-2xl font-bold text-[#1e3a8a] mb-4">Detalhes do Lead</h2>
      <div class="mb-2"><b>Nome:</b> <?= htmlspecialchars($lead['nome']) ?></div>
      <div class="mb-2"><b>Email:</b> <?= htmlspecialchars($lead['email']) ?></div>
      <div class="mb-2"><b>Telefone:</b> <?= htmlspecialchars($lead['telefone']) ?></div>
      <div class="mb-2"><b>Instagram:</b> <?= htmlspecialchars($lead['instagram']) ?></div>
      <div class="mb-2"><b>Ramo:</b> <?= htmlspecialchars($lead['ramo']) ?></div>
      <div class="mb-2"><b>Faturamento:</b> <?= htmlspecialchars($lead['faturamento_raw']) ?> (<?= htmlspecialchars($lead['faturamento_categoria']) ?>)</div>
      <div class="mb-2"><b>Investimento:</b> <?= htmlspecialchars($lead['invest_raw']) ?> (<?= htmlspecialchars($lead['invest_categoria']) ?>)</div>
      <div class="mb-2"><b>Objetivo:</b> <?= htmlspecialchars($lead['objetivo']) ?></div>
      <div class="mb-2"><b>Já faz tráfego pago:</b> <?= htmlspecialchars($lead['faz_trafego']) ?></div>
      <div class="mb-2"><b>Tags IA:</b> <?php if ($tags) foreach ($tags as $tag) echo "<span class='px-2 py-1 bg-[#0ea5e9] text-white rounded mr-2 text-xs'>".htmlspecialchars($tag)."</span>"; ?></div>
      <div class="mb-2"><b>Score Potencial:</b> <?= htmlspecialchars($lead['score_potencial']) ?></div>
      <div class="mb-2"><b>Urgência:</b> <?= htmlspecialchars($lead['urgencia']) ?></div>
      <div class="mb-2"><b>Resumo IA:</b> <?= htmlspecialchars($lead['resumo'] ?? '') ?></div>
      <form method="post" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Nome</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($lead['nome']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($lead['email']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Telefone</label>
          <input type="text" name="telefone" value="<?= htmlspecialchars($lead['telefone']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Instagram</label>
          <input type="text" name="instagram" value="<?= htmlspecialchars($lead['instagram']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Ramo</label>
          <input type="text" name="ramo" value="<?= htmlspecialchars($lead['ramo']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Faturamento</label>
          <input type="text" name="faturamento_raw" value="<?= htmlspecialchars($lead['faturamento_raw']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Investimento</label>
          <input type="text" name="invest_raw" value="<?= htmlspecialchars($lead['invest_raw']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Objetivo</label>
          <input type="text" name="objetivo" value="<?= htmlspecialchars($lead['objetivo']) ?>" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
        </div>
        <div>
          <label class="block text-[#1e3a8a] font-semibold mb-1">Já faz tráfego pago?</label>
          <select name="faz_trafego" class="w-full px-4 py-2 rounded-lg border border-[#0ea5e9] mb-2">
            <option value="Sim" <?= $lead['faz_trafego']==='Sim'?'selected':'' ?>>Sim</option>
            <option value="Não" <?= $lead['faz_trafego']==='Não'?'selected':'' ?>>Não</option>
          </select>
        </div>
        <div class="md:col-span-2 flex gap-4 mt-4">
          <button type="submit" name="salvar" class="bg-[#1e3a8a] text-white rounded-lg px-6 py-2 shadow hover:bg-[#0ea5e9] transition-all duration-300">Salvar alterações</button>
          <button type="submit" name="reanalisar" class="bg-[#0ea5e9] text-white rounded-lg px-6 py-2 shadow hover:bg-[#1e3a8a] transition-all duration-300">Reanalisar IA</button>
        </div>
      </form>
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
        $stmt = $conn->prepare("UPDATE leads SET nome=?, email=?, telefone=?, instagram=?, ramo=?, faturamento_raw=?, invest_raw=?, objetivo=?, faz_trafego=? WHERE id=?");
        $stmt->bind_param('sssssssssi',
          $_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['instagram'], $_POST['ramo'], $_POST['faturamento_raw'], $_POST['invest_raw'], $_POST['objetivo'], $_POST['faz_trafego'], $id
        );
        $stmt->execute();
        $stmt->close();
        echo "<div class='mt-4 text-green-600'>Alterações salvas!</div>";
        echo "<script>setTimeout(()=>location.reload(),1200);</script>";
      }
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reanalisar'])) {
        // Chamar Gemini novamente e atualizar lead
        // ...
        echo "<div class='mt-4 text-green-600'>Lead reanalisado!</div>";
      }
      ?>
    </div>
  </div>
</body>
</html>