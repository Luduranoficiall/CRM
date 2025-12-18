<?php
require_once '../config.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");
$leads = [];
while ($row = $res->fetch_assoc()) {
  foreach(['nome','email','telefone','instagram','ramo','faturamento_raw','invest_raw','objetivo','faz_trafego'] as $campo) {
    if (isset($row[$campo])) $row[$campo] = crypto_decrypt($row[$campo]);
  }
  $leads[] = $row;
}
echo json_encode($leads);
?>
