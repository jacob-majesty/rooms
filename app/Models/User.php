<?php
// app/Models/User.php (CORRIGIDO)

// Inclui a classe base do modelo
require_once __DIR__ . '/../Core/BaseModel.php';

class User extends BaseModel {
    private $table_name = "users"; // Nome da tabela de usuários no banco de dados

    // Propriedades do usuário (correspondem às colunas da tabela)
    public $id;
    public $email;
    public $password; // Armazenará o hash da senha
    public $role;     // Papel do usuário (admin, cliente)
    public $name;     // Nome do usuário

    // Construtor: não precisa mais obter a conexão, pois BaseMode já faz isso
    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai (BaseModel)
    }

    /**
     * Encontra um usuário pelo email.
     * Define as propriedades do objeto User se encontrado e o retorna.
     * @param string $email O email do usuário a ser buscado.
     * @return User|null Retorna o objeto User se encontrado, null caso contrário.
     */
    public function findByEmail($email) {
        $query = "SELECT id, email, password, role, name FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";

        // Prepara a consulta SQL para evitar injeção de SQL
        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel

        // Limpa e vincula o valor do email
        $cleanEmail = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $cleanEmail);
        // Executa a consulta
        $stmt->execute();

        // Obtém a linha do resultado
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se uma linha foi encontrada, atribui os valores às propriedades do objeto atual
        // e retorna o próprio objeto.
        if ($row) {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->password = $row['password']; // Este é o hash da senha
            $this->role = $row['role'];
            $this->name = $row['name'];
            return $this; // Retorna a própria instância do objeto User
        }

        return null; // Usuário não encontrado
    }

    /**
     * Obtém o ID do usuário pelo email.
     * @param string $email O email do usuário.
     * @return int|null Retorna o ID do usuário se encontrado, null caso contrário.
     */
    public function getIdByEmail($email) {
        // Este método também pode se beneficiar do findByEmail() agora
        $user = $this->findByEmail($email);
        return $user ? $user->id : null;
    }

    public function getAllUsers() {
        $query = "SELECT id, name, email, role, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query); // Usa $this->conn herdado de BaseModel
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ); // Retorna como array de objetos
    }
}