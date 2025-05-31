<?php
// api/produtos.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$request = explode('/', trim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
        if (isset($request[1])) {
            // Buscar produto por ID
            $id = $request[1];
            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                echo json_encode($produto);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Produto não encontrado']);
            }
        } else {
            // Listar todos os produtos
            $stmt = $pdo->query("SELECT * FROM produtos");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        // Cadastrar novo produto
        $nome = $data['nome'];
        $descricao = $data['descricao'];
        $preco = $data['preco'];
        $estoque = $data['estoque'];
        $imagem_url = $data['imagem_url'];

        $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $imagem_url]);

        echo json_encode(['mensagem' => 'Produto cadastrado com sucesso!']);
        break;

    case 'PUT':
        // Atualizar produto
        $id = $request[1];
        $nome = $data['nome'] ?? null;
        $descricao = $data['descricao'] ?? null;
        $preco = $data['preco'] ?? null;
        $estoque = $data['estoque'] ?? null;
        $imagem_url = $data['imagem_url'] ?? null;

        $campos = [];
        $valores = [];

        if ($nome !== null) {
            $campos[] = "nome = ?";
            $valores[] = $nome;
        }
        if ($descricao !== null) {
            $campos[] = "descricao = ?";
            $valores[] = $descricao;
        }
        if ($preco !== null) {
            $campos[] = "preco = ?";
            $valores[] = $preco;
        }
        if ($estoque !== null) {
            $campos[] = "estoque = ?";
            $valores[] = $estoque;
        }
        if ($imagem_url !== null) {
            $campos[] = "imagem_url = ?";
            $valores[] = $imagem_url;
        }

        if (empty($campos)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nenhum campo para atualização fornecido']);
            break;
        }

        $valores[] = $id;
        $query = "UPDATE produtos SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($valores);

        echo json_encode(['mensagem' => 'Produto atualizado com sucesso!']);
        break;

    case 'DELETE':
        // Remover produto
        $id = $request[1];
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['mensagem' => 'Produto removido com sucesso!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>