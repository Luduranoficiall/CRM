<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
require_once '../config.php';
$filtro_nome = $_GET['nome'] ?? '';
$filtro_email = $_GET['email'] ?? '';
$filtro_status = $_GET['status'] ?? '';
$sql = "SELECT * FROM leads WHERE 1=1";
if ($filtro_nome) $sql .= " AND nome LIKE '%".$conn->real_escape_string($filtro_nome)."%'";
if ($filtro_email) $sql .= " AND email LIKE '%".$conn->real_escape_string($filtro_email)."%'";
if ($filtro_status) $sql .= " AND faturamento_categoria = '".$conn->real_escape_string($filtro_status)."'";
$sql .= " ORDER BY created_at DESC";
$res = $conn->query($sql);
$leads = [];
while ($row = $res->fetch_assoc()) {
  require_once '../config.php';
  foreach(['nome','email','telefone','instagram','ramo','faturamento_raw','invest_raw','objetivo','faz_trafego'] as $campo) {
    if (isset($row[$campo])) $row[$campo] = crypto_decrypt($row[$campo]);
  }
  $leads[] = $row;
}
function coluna($cat) {
  switch($cat) {
    case '0-10k': return 'Cold';
    case '10-50k': return 'Morno';
    case '50-200k': return 'Quente';
    case '200k+': return 'Ultra Quente';
    default: return 'Cold';
  }
}
$cols = ['Cold','Morno','Quente','Ultra Quente'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard CRM</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script src="/assets/js/kanban.js" defer></script>
</head>
<body class="bg-gradient-to-br from-[#0ea5e9] via-[#1e3a8a] to-[#0b1726] min-h-screen">
  <nav class="flex justify-between items-center p-4 bg-white shadow mb-6">
    <div class="text-[#1e3a8a] font-bold text-xl">CRM Admin</div>
    <div class="flex gap-4 items-center">
      <form method="post" action="" style="display:inline">
        <button type="submit" name="export_csv" class="bg-[#0ea5e9] text-white rounded-lg px-4 py-2 shadow hover:bg-[#1e3a8a] transition-all duration-300">Exportar CSV</button>
      </form>
      <a href="/admin/logout.php" class="text-[#0ea5e9] font-semibold">Logout</a>
    </div>
  </nav>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=leads.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($leads[0]));
    foreach ($leads as $row) fputcsv($output, $row);
    fclose($output);
    exit;
  }
  ?>
  <div class="container mx-auto px-2">
    <div class="bg-white rounded-xl shadow p-6 mb-6">
      <h3 class="text-[#1d4ed8] font-bold mb-4 text-center">Leads por Status</h3>
      <canvas id="leadsChart" height="80"></canvas>
    </div>
    <form method="get" class="mb-6 flex flex-wrap gap-2 items-end bg-white rounded-xl shadow p-4">
      <input type="text" name="nome" value="<?= htmlspecialchars($filtro_nome) ?>" placeholder="Filtrar por nome" class="px-4 py-2 rounded-lg border border-[#0ea5e9] focus:ring-2 focus:ring-[#1d4ed8]">
      <input type="text" name="email" value="<?= htmlspecialchars($filtro_email) ?>" placeholder="Filtrar por email" class="px-4 py-2 rounded-lg border border-[#0ea5e9] focus:ring-2 focus:ring-[#1d4ed8]">
      <select name="status" class="px-4 py-2 rounded-lg border border-[#0ea5e9] focus:ring-2 focus:ring-[#1d4ed8]">
        <option value="">Todos os status</option>
        <option value="0-10k" <?= $filtro_status=="0-10k"?'selected':'' ?>>Cold (0-10k)</option>
        <option value="10-50k" <?= $filtro_status=="10-50k"?'selected':'' ?>>Morno (10-50k)</option>
        <option value="50-200k" <?= $filtro_status=="50-200k"?'selected':'' ?>>Quente (50-200k)</option>
        <option value="200k+" <?= $filtro_status=="200k+"?'selected':'' ?>>Ultra Quente (200k+)</option>
      </select>
      <button type="submit" class="bg-[#1e3a8a] text-white rounded-lg px-6 py-2 shadow hover:bg-[#0ea5e9] transition-all duration-300">Filtrar</button>
    </form>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <?php foreach ($cols as $col): ?>
      <div class="bg-white rounded-xl shadow-lg p-4 min-h-[300px]">
        <h3 class="text-[#1d4ed8] font-bold mb-4 text-center"><?= $col ?></h3>
        <div id="<?= strtolower(str_replace(' ', '-', $col)) ?>-col" class="kanban-col">
          <?php foreach ($leads as $lead) if (coluna($lead['faturamento_categoria']) === $col) include '../components/card.php'; echo renderCard($lead); ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const data = {
  labels: ['Cold', 'Morno', 'Quente', 'Ultra Quente'],
  datasets: [{
    label: 'Leads',
    data: [
      <?= count(array_filter($leads, fn($l)=>coluna($l['faturamento_categoria'])==='Cold')) ?>,
      <?= count(array_filter($leads, fn($l)=>coluna($l['faturamento_categoria'])==='Morno')) ?>,
      <?= count(array_filter($leads, fn($l)=>coluna($l['faturamento_categoria'])==='Quente')) ?>,
      <?= count(array_filter($leads, fn($l)=>coluna($l['faturamento_categoria'])==='Ultra Quente')) ?>
    ],
    backgroundColor: [
      '#0ea5e9', '#1e3a8a', '#1d4ed8', '#0b1726'
    ],
    borderRadius: 8,
    borderWidth: 2
  }]
};
new Chart(document.getElementById('leadsChart').getContext('2d'), {
  type: 'bar',
  data,
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</html>