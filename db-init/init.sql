-- db-init/init.sql

-- Cria o banco de dados se ele não existir
CREATE DATABASE IF NOT EXISTS rooms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usa o banco de dados recém-criado (ou já existente)
USE rooms;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Em um sistema real, use senhas hash (PASSWORD_HASH)
    role ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Salas
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Reservas
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    -- Adiciona uma restrição para evitar reservas sobrepostas na mesma sala
    CONSTRAINT unique_reservation_per_room UNIQUE (room_id, start_time, end_time)
);

-- Dados Iniciais (para testar)
-- As senhas aqui são em texto claro para facilitar o teste, conforme o projeto.
-- Em produção, JAMAIS faça isso. Use PASSWORD_HASH para senhas seguras.
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Admin User', 'admin@email.com', 'adminpass', 'admin'),
('Cliente Um', 'cliente1@email.com', 'clientepass', 'cliente'),
('Cliente Dois', 'cliente2@email.com', 'clientepass', 'cliente');

INSERT IGNORE INTO rooms (name, capacity, description) VALUES
('Sala Alpha', 10, 'Sala de reunião pequena com projetor.'),
('Sala Beta', 25, 'Auditório para apresentações e workshops.'),
('Sala Gamma', 5, 'Sala para reuniões rápidas e brainstorming.');