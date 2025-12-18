<?php
header('Content-Type: application/json');
require_once '../config.php';
// Recebe JSON do quiz
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}
// Chama Gemini 3 (exemplo, substitua pela chamada real)
function callGemini($data) {
    $prompt = "Você é um assistente de qualificação de leads para uma agência de tráfego pago. Receba as respostas abaixo e devolva um JSON contendo: faturamento_categoria (0-10k, 10-50k, 50-200k, 200k+), invest_categoria (1k, 3k, 5k, 10k, 10k+), tags_ai = lista com insights do lead, score_potencial (0-100), urgencia (baixa, média, alta), resumo = descrição curta do potencial do lead. Responda apenas com JSON puro.";
    $apiKey = 'AIzaSyAF5YqUhF3_59YEGoV21uYu7LYivSCxXog'; // Chave Gemini fornecida
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;
    $payload = [
        "contents" => [[
            "parts" => [["text" => $prompt . "\n" . json_encode($data)]]
        ]]
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($response, true);
    // Extrai JSON puro da resposta Gemini
    $output = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $ai = json_decode($output, true);
    if (!$ai) {
        // fallback se não vier JSON válido
        $ai = [
            'faturamento_categoria' => '10-50k',
            'invest_categoria' => '3k',
            'tags_ai' => ['lead qualificado', 'potencial médio'],
            'score_potencial' => 65,
            'urgencia' => 'média',
            'resumo' => 'Lead com potencial médio para investimento.'
        ];
    }
    return $ai;
}
$ai = callGemini($data);
require_once '../config.php';
$stmt = $conn->prepare("INSERT INTO leads (nome, email, telefone, instagram, ramo, faturamento_raw, faturamento_categoria, invest_raw, invest_categoria, objetivo, faz_trafego, tags_ai, score_potencial, urgencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sssssssssssssis',
    crypto_encrypt($data['nome']),
    crypto_encrypt($data['email']),
    crypto_encrypt($data['telefone']),
    crypto_encrypt($data['instagram']),
    crypto_encrypt($data['ramo']),
    crypto_encrypt($data['faturamento']),
    $ai['faturamento_categoria'],
    crypto_encrypt($data['investimento']),
    $ai['invest_categoria'],
    crypto_encrypt($data['objetivo']),
    crypto_encrypt($data['faz_trafego']),
    json_encode($ai['tags_ai']),
    $ai['score_potencial'],
    $ai['urgencia']
);
$stmt->execute();
$stmt->close();
echo json_encode(['success' => true, 'ai' => $ai]);
?>