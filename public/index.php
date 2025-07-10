<?php
// public/index.php

// Inicia a sessão PHP no início de cada requisição para gerenciar o estado do usuário
// CORREÇÃO: Adicionada verificação para evitar aviso de sessão já iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Carrega as classes necessárias
// Certifique-se de que esses caminhos estão corretos em relação ao index.php
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/ClientController.php';
require_once __DIR__ . '/../app/Controllers/ReservationController.php';
require_once __DIR__ . '/../app/Core/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/User.php'; // Pode ser necessário para checar o usuário na rota raiz

// Função para obter a URL da requisição
function getRequestUri() {
    // Remove a query string para obter apenas o caminho
    $uri = $_SERVER['REQUEST_URI'];
    $pos = strpos($uri, '?');
    if ($pos !== false) {
        $uri = substr($uri, 0, $pos);
    }
    return rtrim($uri, '/'); // Remove barras no final para padronizar
}

$requestUri = getRequestUri();
$requestMethod = $_SERVER['REQUEST_METHOD'];

// --- Sistema de Rotas Simples ---

// Rota para a página de login
if ($requestUri === '/login') {
    $authController = new AuthController();
    if ($requestMethod === 'GET') {
        $authController->showLoginForm();
    } elseif ($requestMethod === 'POST') {
        $authController->login();
    }
}
// Rota para logout
elseif ($requestUri === '/logout') {
    $authController = new AuthController();
    $authController->logout();
}
// Rotas para o painel de administração
elseif (strpos($requestUri, '/admin') === 0) { // Começa com /admin
    $adminController = new AdminController(); // O middleware é chamado no construtor
    $parts = explode('/', $requestUri);

    if ($requestUri === '/admin' && $requestMethod === 'GET') {
        $adminController->index();
    }
    // Edição de Reserva (Admin) - GET
    elseif (preg_match('/^\/admin\/reservations\/edit\/(\d+)$/', $requestUri, $matches) && $requestMethod === 'GET') {
        $reservationId = $matches[1];
        $adminController->editReservation($reservationId);
    }
    // Atualização de Reserva (Admin) - POST
    elseif (preg_match('/^\/admin\/reservations\/update\/(\d+)$/', $requestUri, $matches) && $requestMethod === 'POST') {
        $reservationId = $matches[1];
        $adminController->updateReservation($reservationId);
    }
    // Deleção de Reserva (Admin) - POST
    elseif (preg_match('/^\/admin\/reservations\/delete\/(\d+)$/', $requestUri, $matches) && $requestMethod === 'POST') {
        $reservationId = $matches[1];
        $adminController->deleteReservation($reservationId);
    }
    // Outras rotas do admin (ex: relatórios)
    elseif ($requestUri === '/admin/reports' && $requestMethod === 'GET') {
        //$adminController->generateReports();
    }
    // Se a rota admin não for encontrada
    else {
        http_response_code(404);
        echo "<h1>404 Not Found - Rota Admin</h1><p>A página solicitada não foi encontrada.</p>";
    }
}
// Rotas para o painel do cliente
elseif (strpos($requestUri, '/cliente') === 0) { // Começa com /cliente
    $clientController = new ClientController(); // O middleware é chamado no construtor
    $parts = explode('/', $requestUri);

    if ($requestUri === '/cliente' && $requestMethod === 'GET') {
        $clientController->index();
    }
    // Formulário de Criação de Reserva (Cliente) - GET
    elseif ($requestUri === '/cliente/reservations/create' && $requestMethod === 'GET') {
        $clientController->createReservationForm();
    }
    // Edição de Reserva (Cliente) - GET
    elseif (preg_match('/^\/cliente\/reservations\/edit\/(\d+)$/', $requestUri, $matches) && $requestMethod === 'GET') {
        $reservationId = $matches[1];
        $clientController->editReservation($reservationId);
    }
    // Se a rota cliente não for encontrada
    else {
        http_response_code(404);
        echo "<h1>404 Not Found - Rota Cliente</h1><p>A página solicitada não foi encontrada.</p>";
    }
}
// Rotas genéricas de reserva (CRUD) - podem ser chamadas por admin ou cliente, mas com verificação interna de permissão
elseif (strpos($requestUri, '/reservations') === 0) {
    $reservationController = new ReservationController(); // O middleware é chamado no construtor

    // Criação de Reserva - POST (tanto admin quanto cliente)
    if ($requestUri === '/reservations' && $requestMethod === 'POST') {
        $reservationController->store();
    }
    // Atualização de Reserva - POST
    elseif (preg_match('/^\/reservations\/(\d+)\/update$/', $requestUri, $matches) && $requestMethod === 'POST') {
        $reservationId = $matches[1];
        $reservationController->update($reservationId);
    }
    // Deleção de Reserva - POST
    elseif (preg_match('/^\/reservations\/(\d+)\/delete$/', $requestUri, $matches) && $requestMethod === 'POST') {
        $reservationId = $matches[1];
        $reservationController->delete($reservationId);
    }
    // Se a rota de reserva não for encontrada
    else {
        http_response_code(404);
        echo "<h1>404 Not Found - Rota Reserva</h1><p>A página solicitada não foi encontrada.</p>";
    }
}
// Rota raiz (/) e rotas não encontradas
else {
    // Se a requisição for para a raiz ou para uma URL não mapeada,
    // verifica o status da sessão e redireciona.
    if (isset($_SESSION['user_email'])) {
        // Usuário logado, redireciona para o dashboard apropriado
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: /admin');
            exit();
        } elseif ($_SESSION['user_role'] === 'cliente') {
            header('Location: /cliente');
            exit();
        }
    } else {
        // Usuário não logado, redireciona para a página de login
        header('Location: /login');
        exit();
    }

    // Se chegou aqui, a rota não foi encontrada
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>A página solicitada não foi encontrada.</p>";
}