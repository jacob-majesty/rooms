<?php
// app/Controllers/ReservationController.php

require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/AuthMiddleware.php';
require_once __DIR__ . '/../Core/BaseController.php';

class ReservationController extends BaseController {

    private $reservationModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        // Todas as ações neste controller requerem login
        AuthMiddleware::requireLogin();
        $this->reservationModel = new Reservation();
        $this->userModel = new User();
    }

    /**
     * Processa a criação de uma nova reserva (POST /reservations).
     * Accessible por clientes (suas próprias) e admins (qualquer um).
     */
    public function store() {
        // Obter dados do formulário
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $startTime = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $endTime = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // user_id pode vir do formulário (para admin) ou da sessão (para cliente)
        $postedUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);


        // Pega o email do usuário logado da sessão para determinar seu ID e papel
        $userEmail = $_SESSION['user_email'] ?? null;
        if (!$userEmail) {
            header('Location: /logout'); // Redireciona se a sessão estiver inválida
            exit();
        }

        $currentUser = $this->userModel->findByEmail($userEmail); // Objeto User ou null
        if (!$currentUser) {
            header('Location: /logout'); // Redireciona se usuário da sessão não for encontrado
            exit();
        }

        $userId = $currentUser->id; // ID do usuário logado

        // Se o usuário logado é admin E um user_id foi postado, usa o postado
        // Isso permite que admins criem reservas para outros usuários.
        if ($currentUser->role === 'admin' && $postedUserId) {
            $userId = $postedUserId;
        }

        // Validação básica dos campos
        if (!$roomId || !$startTime || !$endTime) {
            header('Location: /' . $currentUser->role . '?error=missing_fields');
            exit();
        }

        // Determina se a sobreposição é permitida (apenas para admins)
        $allowOverlap = ($currentUser->role === 'admin');

        // Tenta criar a reserva
        $success = $this->reservationModel->createReservation($userId, $roomId, $startTime, $endTime, $allowOverlap);

        if ($success) {
            header('Location: /' . $currentUser->role . '?message=reservation_success');
            exit();
        } else {
            // Se falhou, pode ser por conflito de horário
            header('Location: /' . $currentUser->role . '?error=room_unavailable');
            exit();
        }
    }

    /**
     * Processa a atualização de uma reserva existente (POST /reservations/{id}/update).
     * Accessible por clientes (suas próprias) e admins (qualquer um).
     * @param int $id ID da reserva a ser atualizada.
     */
    public function update($id) {
        // Obter dados do formulário
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $startTime = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $endTime = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // user_id pode vir do formulário (para admin) ou da sessão (para cliente)
        $postedUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        // Pega o email do usuário logado da sessão
        $userEmail = $_SESSION['user_email'] ?? null;
        if (!$userEmail) {
            header('Location: /logout');
            exit();
        }

        $currentUser = $this->userModel->findByEmail($userEmail);
        if (!$currentUser) {
            header('Location: /logout');
            exit();
        }

        // ID do usuário logado (usado para verificar permissão do cliente)
        $loggedUserId = $currentUser->id;

        // Se o usuário logado é admin E um user_id foi postado, usa o postado
        $targetUserId = ($currentUser->role === 'admin' && $postedUserId) ? $postedUserId : $loggedUserId;


        // Validar campos
        if (!$id || !$roomId || !$startTime || !$endTime) {
            header('Location: /' . $currentUser->role . '?error=missing_fields');
            exit();
        }

        // Obter a reserva existente para verificar a propriedade
        $existingReservation = $this->reservationModel->getReservationById($id);

        if (!$existingReservation) {
            header('Location: /' . $currentUser->role . '?error=reservation_not_found');
            exit();
        }

        // Regra de Negócio: Clientes só podem editar suas próprias reservas
        // Admins podem editar qualquer reserva
        if ($currentUser->role === 'cliente' && $existingReservation['user_id'] !== $loggedUserId) {
            header('Location: /' . $currentUser->role . '?error=unauthorized_access');
            exit();
        }

        // Determina se a sobreposição é permitida (apenas para admins)
        $allowOverlap = ($currentUser->role === 'admin');

        // Tenta atualizar a reserva
        $success = $this->reservationModel->updateReservation($id, $targetUserId, $roomId, $startTime, $endTime, $allowOverlap);

        if ($success) {
            header('Location: /' . $currentUser->role . '?message=reservation_updated_success');
            exit();
        } else {
            header('Location: /' . $currentUser->role . '?error=room_unavailable');
            exit();
        }
    }

    /**
     * Processa a exclusão de uma reserva (POST /reservations/{id}/delete).
     * Accessible por clientes (suas próprias) e admins (qualquer um).
     * @param int $id ID da reserva a ser excluída.
     */
    public function delete($id) {
        if (!$id) {
            // Se não houver ID, redireciona de volta com erro
            header('Location: /' . ($_SESSION['user_role'] ?? 'login') . '?error=reservation_delete_failed');
            exit();
        }

        $userEmail = $_SESSION['user_email'] ?? null;
        if (!$userEmail) {
            header('Location: /logout');
            exit();
        }

        $currentUser = $this->userModel->findByEmail($userEmail);
        if (!$currentUser) {
            header('Location: /logout');
            exit();
        }

        $existingReservation = $this->reservationModel->getReservationById($id);

        if (!$existingReservation) {
            header('Location: /' . $currentUser->role . '?error=reservation_not_found');
            exit();
        }

        // Regra de Negócio: Clientes só podem excluir suas próprias reservas
        // Admins podem excluir qualquer reserva
        if ($currentUser->role === 'cliente' && $existingReservation['user_id'] !== $currentUser->id) {
            header('Location: /' . $currentUser->role . '?error=unauthorized_access');
            exit();
        }

        // Tenta excluir a reserva
        $success = $this->reservationModel->deleteReservation($id);

        if ($success) {
            header('Location: /' . $currentUser->role . '?message=reservation_deleted_success');
            exit();
        } else {
            header('Location: /' . $currentUser->role . '?error=reservation_delete_failed');
            exit();
        }
    }
}