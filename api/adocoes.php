<?php
// api/adocoes.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$request = explode('/', trim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        // Criar nova solicitação de adoção
        $pet_id = $data['pet_id'];
        $usuario_id = $data['usuario_id'];

        $stmt = $pdo->prepare("INSERT INTO adocoes (pet_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$pet_id, $usuario_id]);

        echo json_encode(['mensagem' => 'Solicitação de adoção registrada com sucesso!']);
        break;

    case 'GET':
        if (isset($request[1])) {
            // Buscar detalhes de uma adoção específica
            $id = $request[1];
            $stmt = $pdo->prepare("SELECT * FROM adocoes WHERE id = ?");
            $stmt->execute([$id]);
            $adocao = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($adocao) {
                echo json_encode($adocao);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Adoção não encontrada']);
            }
        } else {
            // Listar todas as adoções
            $stmt = $pdo->query("SELECT * FROM adocoes");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'PUT':
        // Atualizar status de uma adoção
        $id = $request[1];
        $status = $data['status'];

        $stmt = $pdo->prepare("UPDATE adocoes SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['mensagem' => 'Status da adoção atualizado com sucesso!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>