<?php
// config/AuthMiddleware.php

class AuthMiddleware {

    public static function startSession() {
        // Verifica se uma sessão já não está ativa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin() {
        self::startSession(); // Chama o método para garantir que a sessão esteja iniciada
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login?error=unauthorized');
            exit();
        }
    }

    public static function requireRole($role) {
        self::startSession(); // Chama o método para garantir que a sessão esteja iniciada
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            // Redireciona para login se não estiver logado ou não tiver o papel correto
            // Ou para uma página de erro de permissão
            header('Location: /login?error=permission_denied');
            exit();
        }
    }
}