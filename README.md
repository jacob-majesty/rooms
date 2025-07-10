# Sistema de Gerenciamento de Reservas de Salas

Este é um pequeno projeto web para gerenciamento de reservas de salas, desenvolvido com foco em boas práticas de backend, Programação Orientada a Objetos (POO) e conteinerização com Docker.

---

## 🛠️ Stack Tecnológica

- **Linguagem de Programação:** PHP 8+ (POO)  
- **Banco de Dados:** MySQL 8+  
- **Servidor Web:** Nginx  
- **Gerenciador de Dependências:** Composer  
- **Interface (Front-end):** Bootstrap (layout simples)  
- **Conteinerização:** Docker e Docker Compose  

---

## 🚀 Funcionalidades Principais

### 🔐 Autenticação de Usuários

- **Login e Logout:** Usuários podem fazer login (admin ou cliente) e logout.  
- **Perfis de Acesso:**  
  - `Admin`  
  - `Cliente`  

---

### 📅 Gerenciamento de Reservas

#### 👨‍💼 Administrador (`/admin`)

- Acesso a um painel de controle centralizado  
- Visualiza todas as reservas existentes  
- Cria, edita e exclui qualquer reserva  
- Pode sobrepor reservas (regra de negócio específica para admin)  

#### 🙋 Cliente (`/cliente`)

- Acesso a um painel de controle restrito  
- Cria e edita suas próprias reservas  
- **Não** pode sobrepor reservas existentes  
- **Não** pode excluir reservas após a criação (restrito ao admin)  

#### ⚠️ Validação de Conflitos

- Lógica implementada para **evitar sobreposição de reservas** para usuários cliente  

---

### 🔄 Sistema de Roteamento

- Roteamento simples via `index.php` para direcionar as requisições aos controladores apropriados  

---

