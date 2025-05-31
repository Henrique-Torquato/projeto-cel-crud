<?php
// api/pets.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Verifica se o ID foi fornecido na URL
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Busca o pet pelo ID
            $stmt = $pdo->prepare("SELECT * FROM pets WHERE id = ? AND status = 'disponivel'");
            $stmt->execute([$id]);
            $pet = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pet) {
                echo json_encode($pet); // Retorna o pet encontrado
            } else {
                http_response_code(404); // Pet não encontrado
                echo json_encode(['erro' => 'Pet não encontrado']);
            }
        } else {
            // Lista todos os pets disponíveis
            $stmt = $pdo->query("SELECT * FROM pets WHERE status = 'disponivel'");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        // Cadastrar novo pet
        $data = json_decode(file_get_contents('php://input'), true);
        $nome = $data['nome'];
        $idade = $data['idade'];
        $raca = $data['raca'];
        $deficiencia = $data['deficiencia'];
        $cuidados_especiais = $data['cuidados_especiais'];
        $foto_url = $data['foto_url'];

        $stmt = $pdo->prepare("INSERT INTO pets (nome, idade, raca, deficiencia, cuidados_especiais, foto_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $idade, $raca, $deficiencia, $cuidados_especiais, $foto_url]);

        echo json_encode(['mensagem' => 'Pet cadastrado com sucesso!']);
        break;

    case 'PUT':
        // Atualizar pet (usando ID na URL)
        $id = $_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        $nome = $data['nome'] ?? null;
        $idade = $data['idade'] ?? null;
        $raca = $data['raca'] ?? null;
        $deficiencia = $data['deficiencia'] ?? null;
        $cuidados_especiais = $data['cuidados_especiais'] ?? null;
        $foto_url = $data['foto_url'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do pet não fornecido']);
            break;
        }

        $campos = [];
        $valores = [];

        if ($nome !== null) {
            $campos[] = "nome = ?";
            $valores[] = $nome;
        }
        if ($idade !== null) {
            $campos[] = "idade = ?";
            $valores[] = $idade;
        }
        if ($raca !== null) {
            $campos[] = "raca = ?";
            $valores[] = $raca;
        }
        if ($deficiencia !== null) {
            $campos[] = "deficiencia = ?";
            $valores[] = $deficiencia;
        }
        if ($cuidados_especiais !== null) {
            $campos[] = "cuidados_especiais = ?";
            $valores[] = $cuidados_especiais;
        }
        if ($foto_url !== null) {
            $campos[] = "foto_url = ?";
            $valores[] = $foto_url;
        }

        if (empty($campos)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nenhum campo para atualização fornecido']);
            break;
        }

        $valores[] = $id;
        $query = "UPDATE pets SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($valores);

        echo json_encode(['mensagem' => 'Pet atualizado com sucesso!']);
        break;

    case 'DELETE':
        // Remover pet (usando ID na URL)
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM pets WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['mensagem' => 'Pet removido com sucesso!']);
        break;

    default:
        http_response_code(405); // Método não permitido
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>