<?php
// app/Controllers/ClientController.php (Exemplo com BaseController)

require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/../Models/Room.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/AuthMiddleware.php';
require_once __DIR__ . '/../Core/BaseController.php'; // Inclua o BaseController

class ClientController extends BaseController { // EXTENDA O BaseController

    public function __construct() {
        parent::__construct(); // Chame o construtor da classe pai
        AuthMiddleware::requireRole('cliente');
    }

    public function index() {
        $userEmail = $_SESSION['user_email'] ?? null;
        if (!$userEmail) {
            header('Location: /logout');
            exit();
        }

        $userModel = new User();
        $currentUser = $userModel->findByEmail($userEmail);

        if (!$currentUser) {
            header('Location: /logout');
            exit();
        }

        $reservationModel = new Reservation();
        $myReservations = $reservationModel->getReservationsByUserId($currentUser->id);

        $roomModel = new Room();
        $availableRooms = $roomModel->getAllRooms();
        $roomAvailability = $reservationModel->getRoomAvailability();

        // CHAME O MÉTODO RENDER DO BASECONTROLLER AQUI
        $this->render('client/dashboard', [
            'myReservations' => $myReservations,
            'availableRooms' => $availableRooms,
            'roomAvailability' => $roomAvailability
        ]);
    }

    // ... outros métodos ...
    public function createReservationForm() {
        $roomModel = new Room();
        $rooms = $roomModel->getAllRooms();
        $this->render('client/reservations/create', ['rooms' => $rooms]);
    }

    public function editReservation($id) {
        $reservationModel = new Reservation();
        $reservation = $reservationModel->getReservationById($id);
        $roomModel = new Room();
        $rooms = $roomModel->getAllRooms();
        // ... (other logic and validation) ...
        $this->render('client/reservations/edit', [
            'reservation' => $reservation,
            'rooms' => $rooms
        ]);
    }
}