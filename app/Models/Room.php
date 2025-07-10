<?php
// app/Models/Room.php

// Inclui a classe base do modelo
require_once __DIR__ . '/../Core/BaseModel.php';

class Room extends BaseModel { // Agora Room estende BaseModel
    private $table_name = "rooms"; // Nome da tabela de salas

    // Propriedades da sala
    public $id;
    public $name;
    public $capacity;
    public $description;

    // Construtor: não precisa mais obter a conexão, pois BaseMode já faz isso
    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai (BaseModel)
    }

    /**
     * Obtém todas as salas.
     * @return array Retorna um array de objetos/arrays associativos de salas.
     */
    public function getAllRooms() {
        $query = "SELECT id, name, capacity, description FROM " . $this->table_name . " ORDER BY name";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém uma sala pelo ID.
     * @param int $id O ID da sala.
     * @return array|null Retorna um array associativo da sala ou null se não encontrada.
     */
    public function getRoomById($id) {
        $query = "SELECT id, name, capacity, description FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
    
        $cleanId = htmlspecialchars(strip_tags($id));
    
        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $stmt->bindParam(':id', $cleanId);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $row ?: null;
    }
}