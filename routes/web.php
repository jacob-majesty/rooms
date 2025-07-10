<?php
// app/routes/web.php

// Certifique-se de incluir a classe Router, se ela ainda não foi incluída em index.php
// require_once __DIR__ . '/../Core/Router.php'; // Se Router for instanciado aqui

// Instancia o roteador (se não for passado por injeção de dependência)
// Lembre-se que Router é instanciado em public/index.php
// $router = new Router(); // Esta linha DEVE ESTAR em public/index.php

// Rota padrão (página inicial)
// Redireciona para o dashboard do usuário logado ou para a tela de login
$router->get('/', function() {
    if (isset($_SESSION['user_role'])) {
        header('Location: /' . $_SESSION['user_role']);
        exit();
    }
    header('Location: /login');
    exit();
});

// Rotas de Autenticação
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Rotas para o Dashboard do Administrador
// (Protegidas por AuthMiddleware no construtor do AdminController)
$router->get('/admin', 'AdminController@index');
// Rotas de relatório para admin (exemplo)
$router->get('/admin/reports', 'AdminController@showReports');

// Rotas para o Dashboard do Cliente
// (Protegidas por AuthMiddleware no construtor do ClientController)
$router->get('/cliente', 'ClientController@index');

// Rotas de CRUD para RESERVAS (acessíveis por clientes e admins com controle de permissão no Controller)
// Requisição GET para exibir o formulário de criação de reserva (para cliente)
$router->get('/cliente/reservations/create', 'ClientController@createReservationForm');
// Requisição POST para criar uma nova reserva
$router->post('/reservations', 'ReservationController@store');

// Requisição GET para exibir o formulário de edição de reserva (para cliente ou admin)
// Usamos {id} para capturar o ID da reserva na URL
$router->get('/cliente/reservations/edit/{id}', 'ClientController@editReservation'); // Cliente pode editar suas
$router->get('/admin/reservations/edit/{id}', 'AdminController@editReservation');   // Admin pode editar todas

// Requisição POST para atualizar uma reserva existente (simulando PUT/PATCH com POST)
// O ID da reserva é passado na URL
$router->post('/reservations/{id}/update', 'ReservationController@update');

// Requisição POST para deletar uma reserva (simulando DELETE com POST)
// O ID da reserva é passado na URL
$router->post('/reservations/{id}/delete', 'ReservationController@delete');


// Exemplo de rota 404 para URLs não encontradas
// Esta rota deve ser a ÚLTIMA a ser definida para capturar tudo que não foi correspondido
$router->setNotFoundHandler(function() {
    http_response_code(404);
    require_once __DIR__ . '/../Views/404.php';
});

// Outras rotas podem ser adicionadas aqui
// Ex: Rotas para gerenciar salas, usuários (apenas para admin)
// $router->get('/admin/rooms', 'RoomController@index');
// $router->post('/admin/rooms/create', 'RoomController@store');