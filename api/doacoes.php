<?php
// api/doacoes.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$request = explode('/', trim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        // Registrar nova doação
        $usuario_id = $data['usuario_id'];
        $valor = $data['valor'];
        $tipo = $data['tipo'];

        $stmt = $pdo->prepare("INSERT INTO doacoes (usuario_id, valor, tipo) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $valor, $tipo]);

        echo json_encode(['mensagem' => 'Doação registrada com sucesso!']);
        break;

    case 'GET':
        if (isset($request[1])) {
            // Buscar detalhes de uma doação específica
            $id = $request[1];
            $stmt = $pdo->prepare("SELECT * FROM doacoes WHERE id = ?");
            $stmt->execute([$id]);
            $doacao = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($doacao) {
                echo json_encode($doacao);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Doação não encontrada']);
            }
        } else {
            // Listar todas as doações
            $stmt = $pdo->query("SELECT * FROM doacoes");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>