<?php
// api/usuarios.php

require_once ROOT_DIR . '/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$request = explode('/', trim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        if ($_GET['url'] === 'login') {
            // Autenticação
            $email = $data['email'];
            $senha = $data['senha'];

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
            $stmt->execute([$email, $senha]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                echo json_encode(['mensagem' => 'Login bem-sucedido!', 'usuario' => $usuario]);
            } else {
                http_response_code(401);
                echo json_encode(['erro' => 'Credenciais inválidas']);
            }
        } else {
            // Cadastro
            $nome = $data['nome'];
            $email = $data['email'];
            $senha = $data['senha'];
            $telefone = $data['telefone'] ?? null;
            $endereco = $data['endereco'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, endereco) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $telefone, $endereco]);

            echo json_encode(['mensagem' => 'Usuário cadastrado com sucesso!']);
        }
        break;

    case 'GET':
        if (isset($request[1])) {
            // Buscar usuário por ID
            $id = $request[1];
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                echo json_encode($usuario);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Usuário não encontrado']);
            }
        } else {
            // Listar todos os usuários
            $stmt = $pdo->query("SELECT * FROM usuarios");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'PUT':
        $id = $request[1];
        $nome = $data['nome'] ?? null;
        $telefone = $data['telefone'] ?? null;
        $endereco = $data['endereco'] ?? null;

        $campos = [];
        $valores = [];

        if ($nome !== null) {
            $campos[] = "nome = ?";
            $valores[] = $nome;
        }
        if ($telefone !== null) {
            $campos[] = "telefone = ?";
            $valores[] = $telefone;
        }
        if ($endereco !== null) {
            $campos[] = "endereco = ?";
            $valores[] = $endereco;
        }

        if (empty($campos)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nenhum campo para atualização fornecido']);
            break;
        }

        $valores[] = $id;
        $query = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($valores);

        echo json_encode(['mensagem' => 'Usuário atualizado com sucesso!']);
        break;

    case 'DELETE':
        $id = $request[1];
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['mensagem' => 'Usuário removido com sucesso!']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não suportado']);
        break;
}
?>