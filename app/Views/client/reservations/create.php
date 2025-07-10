<?php require_once __DIR__ . '/../../partials/header.php'; ?>

<h1 class="mb-4">Fazer Nova Reserva</h1>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php
            switch ($_GET['error']) {
                case 'missing_fields': echo 'Por favor, preencha todos os campos.'; break;
                case 'room_unavailable': echo 'A sala não está disponível no período selecionado. Por favor, escolha outro horário ou sala.'; break;
                case 'reservation_failed': echo 'Erro ao criar reserva. Tente novamente.'; break;
                case 'invalid_time_period': echo 'O tempo final da reserva deve ser após o tempo inicial.'; break;
            }
        ?>
    </div>
<?php endif; ?>

<form action="/reservations" method="POST">
    <div class="mb-3">
        <label for="room_id" class="form-label">Sala:</label>
        <select class="form-select" id="room_id" name="room_id" required>
            <option value="">Selecione uma sala</option>
            <?php if (!empty($rooms)): ?>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['id']) ?>"><?= htmlspecialchars($room['name']) ?> (Capacidade: <?= htmlspecialchars($room['capacity']) ?>)</option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>Nenhuma sala disponível</option>
            <?php endif; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Início da Reserva:</label>
        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">Fim da Reserva:</label>
        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
    </div>
    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-success">Reservar Sala</button>
        <a href="/cliente" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>