<?php
// app/Controllers/AdminController.php

require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/../Models/Room.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/AuthMiddleware.php';
require_once __DIR__ . '/../Core/BaseController.php'; // Inclui BaseController

class AdminController extends BaseController { // AdminController estende BaseController

    private $reservationModel;
    private $roomModel;
    private $userModel;

    public function __construct() {
        parent::__construct(); // Chama o construtor do BaseController
        // Garante que apenas administradores acessem esta parte
        AuthMiddleware::requireRole('admin');
        $this->reservationModel = new Reservation();
        $this->roomModel = new Room();
        $this->userModel = new User();
    }

    /**
     * Exibe o dashboard do administrador.
     * Lista todas as reservas, usuários e salas.
     */
    public function index() {
        $allReservations = $this->reservationModel->getAllReservations();
        $allUsers = $this->userModel->getAllUsers();
        $allRooms = $this->roomModel->getAllRooms();

        // Renderiza a view do dashboard do admin
        // Passa todas as reservas, usuários e salas para a view
        $this->render('admin/dashboard', [
            'allReservations' => $allReservations,
            'allUsers' => $allUsers,
            'allRooms' => $allRooms
        ]);
    }

    /**
     * Exibe o formulário para editar uma reserva (para admin).
     * Um admin pode editar qualquer reserva.
     * @param int $id ID da reserva a ser editada.
     */
    public function editReservation($id) {
        $reservation = $this->reservationModel->getReservationById($id);

        if (!$reservation) {
            header('Location: /admin?error=reservation_not_found');
            exit();
        }

        $rooms = $this->roomModel->getAllRooms();
        $users = $this->userModel->getAllUsers(); // Admin pode mudar o usuário da reserva

        $this->render('admin/reservations/edit', [
            'reservation' => $reservation,
            'rooms' => $rooms,
            'users' => $users
        ]);
    }

    /**
     * Exibe a página de relatórios (exemplo).
     */
    public function showReports() {
        // Lógica para gerar dados de relatórios aqui (ex: contagem de reservas por sala, etc.)
        $this->render('admin/reports'); // Assumindo que você terá uma view admin/reports.php
    }

    // Você pode adicionar mais métodos para CRUD de usuários e salas aqui se necessário
    // Ex: public function createUserForm(), public function storeUser(), etc.

    /**
     * Atualiza uma reserva existente (para admin).
     * @param int $id ID da reserva a ser atualizada.
     */
    public function updateReservation($id) {
        // Verifica se a requisição é POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/reservations/edit/' . $id . '?error=invalid_method');
            exit();
        }

        // Filtra e sanitiza os dados de entrada
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $startTime = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $endTime = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Validação básica
        if (!$userId || !$roomId || !$startTime || !$endTime || !$status) {
            header('Location: /admin/reservations/edit/' . $id . '?error=missing_fields');
            exit();
        }

        // Tenta atualizar a reserva
        $success = $this->reservationModel->updateReservation(
            $id, $userId, $roomId, $startTime, $endTime, $status
        );

        if ($success) {
            header('Location: /admin?message=reservation_updated');
            exit();
        } else {
            header('Location: /admin/reservations/edit/' . $id . '?error=update_failed');
            exit();
        }
    }

    /**
     * Deleta uma reserva (para admin).
     * @param int $id ID da reserva a ser deletada.
     */
    public function deleteReservation($id) {
        // Verifica se a requisição é POST (para segurança)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin?error=invalid_method');
            exit();
        }

        // Tenta deletar a reserva
        $success = $this->reservationModel->deleteReservation($id);

        if ($success) {
            header('Location: /admin?message=reservation_deleted');
            exit();
        } else {
            header('Location: /admin?error=delete_failed');
            exit();
        }
    }

     /**
     * Exibe a página de relatórios (para admin).
     * Este é um placeholder. A lógica real de geração de relatórios
     * seria implementada aqui ou em um serviço separado.
     */




}