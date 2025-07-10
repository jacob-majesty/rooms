<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm mt-5">
            <div class="card-header text-center bg-primary text-white">
                <h4>Login no Sistema de Reservas</h4>
            </div>
            <div class="card-body">
                <?php
                // Exibe mensagens de erro, se houver
                if (isset($_GET['error'])) {
                    $error_message = '';
                    switch ($_GET['error']) {
                        case 'invalid_credentials':
                            $error_message = 'Email ou senha inválidos.';
                            break;
                        case 'access_denied':
                            $error_message = 'Acesso negado. Faça login para continuar.';
                            break;
                        case 'logged_out':
                            $error_message = 'Você foi desconectado.';
                            break;
                        default:
                            $error_message = 'Ocorreu um erro.';
                            break;
                    }
                    echo '<div class="alert alert-danger text-center" role="alert">' . htmlspecialchars($error_message) . '</div>';
                }
                ?>
                <form action="/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
                <div class="mt-3 text-center text-muted">
                    <small>Usuários de teste:</small><br>
                    <small>Admin: admin@email.com / adminpass</small><br>
                    <small>Cliente 1: cliente1@email.com / clientepass</small><br>
                    <small>Cliente 2: cliente2@email.com / clientepass</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>