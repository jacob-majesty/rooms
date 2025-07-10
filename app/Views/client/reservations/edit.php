<?php require_once __DIR__ . '/../../partials/header.php'; ?>

<h1 class="mb-4">Editar Reserva ID: <?= htmlspecialchars($reservation['id']) ?></h1>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php
            switch ($_GET['error']) {
                case 'missing_fields': echo 'Por favor, preencha todos os campos.'; break;
                case 'room_unavailable': echo 'A sala não está disponível no período selecionado. Por favor, escolha outro horário ou sala.'; break;
                case 'reservation_update_failed': echo 'Erro ao atualizar reserva. Tente novamente.'; break;
                case 'invalid_time_period': echo 'O tempo final da reserva deve ser após o tempo inicial.'; break;
            }
        ?>
    </div>
<?php endif; ?>

<?php if (empty($reservation)): ?>
    <p class="alert alert-warning">Reserva não encontrada.</p>
<?php else: ?>
    <form action="/reservations/<?= htmlspecialchars($reservation['id']) ?>/update" method="POST">
        <div class="mb-3">
            <label for="room_id" class="form-label">Sala:</label>
            <select class="form-select" id="room_id" name="room_id" required>
                <option value="">Selecione uma sala</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['id']) ?>" <?= ($room['id'] == $reservation['room_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?> (Capacidade: <?= htmlspecialchars($room['capacity']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Início da Reserva:</label>
            <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($reservation['start_time'])) ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">Fim da Reserva:</label>
            <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($reservation['end_time'])) ?>" required>
        </div>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <div class="mb-3">
                <label for="user_id" class="form-label">Usuário da Reserva (Apenas Admin):</label>
                <input type="text" class="form-control" id="user_id" name="user_id" value="<?= htmlspecialchars($reservation['user_id']) ?>" required>
                <small class="form-text text-muted">ID do Usuário. Em produção, seria um dropdown de usuários.</small>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">Atualizar Reserva</button>
            <a href="/<?= htmlspecialchars($_SESSION['user_role']) ?>" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>