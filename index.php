<?php
// index.php

define('ROOT_DIR', __DIR__); // Define a raiz do projeto
require_once ROOT_DIR . '/includes/db.php';

// Obter a URL solicitada
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Dividir a URL em partes
$request = explode('/', trim($url, '/'));

// Determinar qual endpoint foi solicitado
if ($request[0] === 'usuarios') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/usuarios.php';
} elseif ($request[0] === 'pets') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/pets.php';
} elseif ($request[0] === 'adocoes') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/adocoes.php';
} elseif ($request[0] === 'doacoes') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/doacoes.php';
} elseif ($request[0] === 'produtos') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/produtos.php';
} elseif ($request[0] === 'pedidos') {
    if (isset($request[1])) {
        // Se houver um segundo segmento, trata-se de um ID
        $_GET['id'] = $request[1]; // Passa o ID para $_GET
    }
    require_once ROOT_DIR . '/api/pedidos.php';
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada']);
}
?>