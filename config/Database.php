<?php
// config/Database.php

class Database {
    private $host = 'db'; // O nome do serviço do MySQL no docker-compose.yml
    private $db_name = 'rooms'; // Nome do seu banco de dados, conforme docker-compose.yml
    private $username = 'rooms'; // Nome do usuário do MySQL, conforme docker-compose.yml
    private $password = 'rooms'; // Senha do usuário do MySQL, conforme docker-compose.yml
    private $conn;

    /**
     * Obter a conexão com o banco de dados
     * @return PDO Retorna o objeto de conexão PDO
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro para exceções
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Define o modo de busca padrão para arrays associativos
            $this->conn->exec("set names utf8"); // Garante que a conexão use UTF-8 para caracteres especiais

        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
            // Em um ambiente de produção, você registraria o erro em um log, em vez de exibi-lo.
            exit(); // Encerra a aplicação se a conexão falhar
        }

        return $this->conn;
    }
}