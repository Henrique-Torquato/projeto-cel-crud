<?php
// api/pedidos.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$request = explode('/', trim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        // Criar novo pedido
        $usuario_id = $data['usuario_id'];
        $itens = $data['itens'];

        $pdo->beginTransaction();

        try {
            // Inserir o pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, valor_total, status) VALUES (?, 0, 'pendente')");
            $stmt->execute([$usuario_id]);
            $pedido_id = $pdo->lastInsertId();

            $total = 0;

            foreach ($itens as $item) {
                $produto_id = $item['produto_id'];
                $quantidade = $item['quantidade'];

                // Obter preço do produto
                $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
                $stmt->execute([$produto_id]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$produto) {
                    throw new Exception("Produto não encontrado");
                }

                $preco_unitario = $produto['preco'];
                $subtotal = $preco_unitario * $quantidade;

                // Inserir item do pedido
                $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
                $stmt->execute([$pedido_id, $produto_id, $quantidade, $preco_unitario]);

                $total += $subtotal;
            }

            // Atualizar valor total do pedido
            $stmt = $pdo->prepare("UPDATE pedidos SET valor_total = ? WHERE id = ?");
            $stmt->execute([$total, $pedido_id]);

            $pdo->commit();
            echo json_encode(['mensagem' => 'Pedido criado com sucesso!', 'pedido_id' => $pedido_id]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['erro' => $e->getMessage()]);
        }
        break;

    case 'GET':
        if (isset($request[1])) {
            // Buscar detalhes de um pedido específico
            $id = $request[1];
            $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
            $stmt->execute([$id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido) {
                // Buscar itens do pedido
                $stmt = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
                $stmt->execute([$id]);
                $itens_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $pedido['itens'] = $itens_pedido;
                echo json_encode($pedido);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Pedido não encontrado']);
            }
        } else {
            // Listar todos os pedidos
            $stmt = $pdo->query("SELECT * FROM pedidos");
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pedidos as &$pedido) {
                $stmt = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
                $stmt->execute([$pedido['id']]);
                $pedido['itens'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode($pedidos);
        }
        break;

    case 'PUT':
        // Atualizar status de um pedido
        $id = $request[1];
        $status = $data['status'];

        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['mensagem' => 'Status do pedido atualizado com sucesso!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>