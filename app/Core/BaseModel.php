<?php
// app/Core/BaseModel.php

// Inclui a classe de conexão com o banco de dados (que ainda está em config/)
require_once __DIR__ . '/../../config/Database.php';

abstract class BaseModel {
    protected $conn; // Conexão com o banco de dados, acessível pelas classes filhas

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
}