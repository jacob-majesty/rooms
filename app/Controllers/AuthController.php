<?php
// app/Controllers/AuthController.php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/AuthMiddleware.php';
require_once __DIR__ . '/../Core/BaseController.php'; // Inclui BaseController

class AuthController extends BaseController { // AuthController agora estende BaseController
    private $userModel;

    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai
        $this->userModel = new User();
    }

    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm() {
        // Se o usuário já estiver logado, redireciona para o dashboard apropriado
        if (isset($_SESSION['user_role'])) {
            header('Location: /' . $_SESSION['user_role']);
            exit();
        }
        // Renderiza a view de login
        $this->render('auth/login'); // CORRIGIDO: Usa o método render do BaseController
    }

    /**
     * Processa a submissão do formulário de login.
     */
    public function login() {
        // Se o usuário já estiver logado, redireciona para o dashboard apropriado
        if (isset($_SESSION['user_role'])) {
            header('Location: /' . $_SESSION['user_role']);
            exit();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$email || !$password) {
            header('Location: /login?error=missing_credentials');
            exit();
        }

        $user = $this->userModel->findByEmail($email);

        // Verifica se o usuário existe e a senha está correta
        // Para este projeto, estamos usando senhas fixas para os usuários pré-definidos
        // Em um ambiente real, você usaria password_verify($password, $user['password']);
        if ($user && $user->password === $password) { // Acesso à propriedade password do objeto User
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;

            // Redireciona para o dashboard apropriado com base no papel do usuário
            header('Location: /' . $user->role);
            exit();
        } else {
            header('Location: /login?error=invalid_credentials');
            exit();
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout() {
        // Destrói todas as variáveis de sessão
        session_unset();
        // Destrói a sessão
        session_destroy();
        // Redireciona para a página de login
        header('Location: /login?message=logged_out');
        exit();
    }
}