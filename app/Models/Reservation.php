<?php
// app/Models/Reservation.php

// Inclui a classe base do modelo
require_once __DIR__ . '/../Core/BaseModel.php';

class Reservation extends BaseModel { // Agora Reservation estende BaseModel
    private $table_name = "reservations"; // Nome da tabela de reservas

    // Construtor: não precisa mais obter a conexão, pois BaseMode já faz isso
    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai (BaseModel)
    }

    /**
     * Verifica se uma sala está disponível para um determinado período.
     * @param int $roomId ID da sala.
     * @param string $startTime Início da reserva (formato DATETIME).
     * @param string $endTime Fim da reserva (formato DATETIME).
     * @param int|null $currentReservationId ID da reserva atual (para edição, para ignorar a si mesma).
     * @return bool Retorna true se a sala estiver disponível, false caso contrário.
     */
    public function isRoomAvailable($roomId, $startTime, $endTime, $currentReservationId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . "
                  WHERE room_id = :room_id
                  AND id != :current_reservation_id -- Ignora a própria reserva ao editar
                  AND (
                      (start_time < :end_time AND end_time > :start_time)
                  )";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $roomIdParam = htmlspecialchars(strip_tags($roomId));
        $stmt->bindParam(':room_id', $roomIdParam);

        $start_time_param = htmlspecialchars(strip_tags($startTime));
        $stmt->bindParam(':start_time', $start_time_param);

        $endTimeParam = htmlspecialchars(strip_tags($endTime));
        $stmt->bindParam(':end_time', $endTimeParam);

        $stmt->bindValue(':current_reservation_id', $currentReservationId ?? 0, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row['count'] == 0);
    }

    /**
     * Cria uma nova reserva.
     * Regra de Negócio: Admin pode sobrepor reservas. Clientes não.
     * @param int $userId ID do usuário que está fazendo a reserva.
     * @param int $roomId ID da sala a ser reservada.
     * @param string $startTime Início da reserva (formato DATETIME).
     * @param string $endTime Fim da reserva (formato DATETIME).
     * @param bool $allowOverlap Se true, permite sobrepor reservas (usado para admin).
     * @return bool Retorna true em caso de sucesso, false caso contrário (ex: conflito).
     */
    public function createReservation($userId, $roomId, $startTime, $endTime, $allowOverlap = false) {
        if (strtotime($endTime) <= strtotime($startTime)) {
            return false;
        }

        if (!$allowOverlap && !$this->isRoomAvailable($roomId, $startTime, $endTime)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (user_id, room_id, start_time, end_time) VALUES (:user_id, :room_id, :start_time, :end_time)";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel

        $endTimeParam = htmlspecialchars(strip_tags($endTime));
        $stmt->bindParam(':end_time', $endTimeParam);

        $roomIdParam = htmlspecialchars(strip_tags($roomId));
        $stmt->bindParam(':room_id', $roomIdParam);

        $start_time_param = htmlspecialchars(strip_tags($startTime));
        $stmt->bindParam(':start_time', $start_time_param);

        $endTimeParam = htmlspecialchars(strip_tags($endTime));
        $stmt->bindParam(':end_time', $endTimeParam);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Atualiza uma reserva existente.
     * Regra de Negócio: Admin pode sobrepor reservas. Clientes não.
     * @param int $reservationId ID da reserva a ser atualizada.
     * @param int $userId Novo ID do usuário proprietário da reserva.
     * @param int $roomId Novo ID da sala.
     * @param string $startTime Novo início da reserva.
     * @param string $endTime Novo fim da reserva.
     * @param bool $allowOverlap Se true, permite sobrepor reservas (usado para admin).
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function updateReservation($reservationId, $userId, $roomId, $startTime, $endTime, $allowOverlap = false) {
        if (strtotime($endTime) <= strtotime($startTime)) {
            return false;
        }

        if (!$allowOverlap && !$this->isRoomAvailable($roomId, $startTime, $endTime, $reservationId)) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . "
                  SET user_id = :user_id, room_id = :room_id, start_time = :start_time, end_time = :end_time
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel

        $reservationIdParam = htmlspecialchars(strip_tags($reservationId));
        $stmt->bindParam(':id', $reservationIdParam);

        $user_id_param = htmlspecialchars(strip_tags($userId));
        $stmt->bindParam(':user_id', $user_id_param);

        $roomIdParam = htmlspecialchars(strip_tags($roomId));
        $stmt->bindParam(':room_id', $roomIdParam);

        $start_time_param = htmlspecialchars(strip_tags($startTime));
        $stmt->bindParam(':start_time', $start_time_param);
        
        $endTimeParam = htmlspecialchars(strip_tags($endTime));
        $stmt->bindParam(':end_time', $endTimeParam);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Deleta uma reserva.
     * @param int $reservationId ID da reserva a ser deletada.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function deleteReservation($reservationId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $idParam = htmlspecialchars(strip_tags($reservationId));
        $stmt->bindParam(':id', $idParam);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    /**
     * Obtém todas as reservas (útil para administradores).
     * Inclui informações do usuário e da sala para melhor visualização.
     * @return array Retorna um array de arrays associativos de reservas.
     */
    public function getAllReservations() {
        $query = "SELECT r.id, r.user_id, u.name as user_name, u.email as user_email,
                         r.room_id, rm.name as room_name,
                         r.start_time, r.end_time, r.created_at
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN rooms rm ON r.room_id = rm.id
                  ORDER BY r.start_time DESC";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém as reservas de um usuário específico.
     * @param int $userId O ID do usuário.
     * @return array Retorna um array de arrays associativos de reservas do usuário.
     */
    public function getReservationsByUserId($userId) {
        $query = "SELECT r.id, r.user_id, u.name as user_name,
                         r.room_id, rm.name as room_name,
                         r.start_time, r.end_time, r.created_at
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN rooms rm ON r.room_id = rm.id
                  WHERE r.user_id = :user_id
                  ORDER BY r.start_time DESC";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $userIdParam = htmlspecialchars(strip_tags($userId));
        $stmt->bindParam(':user_id', $userIdParam);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém uma reserva específica pelo ID.
     * @param int $reservationId O ID da reserva.
     * @return array|null Retorna um array associativo da reserva ou null se não encontrada.
     */
    public function getReservationById($reservationId) {
        $query = "SELECT r.id, r.user_id, u.name as user_name, u.email as user_email,
                         r.room_id, rm.name as room_name,
                         r.start_time, r.end_time, r.created_at
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN rooms rm ON r.room_id = rm.id
                  WHERE r.id = :id LIMIT 0,1";
    
        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $reservationIdParam = htmlspecialchars(strip_tags($reservationId));
        $stmt->bindParam(':id', $reservationIdParam);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $row ?: null;
    }

    /**
     * Obtém dados de disponibilidade de salas para um calendário.
     * @param string|null $startDate Data de início para o período (opcional).
     * @param string|null $endDate Data de fim para o período (opcional).
     * @return array Retorna um array de reservas com informações da sala.
     */
    public function getRoomAvailability($startDate = null, $endDate = null) {
        $query = "SELECT r.id as reservation_id, rm.id as room_id, rm.name as room_name,
                         r.start_time, r.end_time, u.name as reserved_by_user
                  FROM " . $this->table_name . " r
                  JOIN rooms rm ON r.room_id = rm.id
                  JOIN users u ON r.user_id = u.id";

        $conditions = [];
        $params = [];

        if ($startDate) {
            $conditions[] = "r.start_time >= :start_date";
            $params[':start_date'] = $startDate;
        }
        if ($endDate) {
            $conditions[] = "r.end_time <= :end_date";
            $params[':end_date'] = $endDate;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY rm.name, r.start_time";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}