<?php require_once __DIR__ . '/../partials/header.php'; ?>

<h1 class="mb-4">Painel do Cliente</h1>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success" role="alert">
        <?php
            switch ($_GET['message']) {
                case 'reservation_success': echo 'Reserva criada com sucesso!'; break;
                case 'reservation_updated_success': echo 'Reserva atualizada com sucesso!'; break;
                case 'reservation_deleted_success': echo 'Reserva excluída com sucesso!'; break;
            }
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php
            switch ($_GET['error']) {
                case 'missing_fields': echo 'Por favor, preencha todos os campos da reserva.'; break;
                case 'room_unavailable': echo 'A sala não está disponível no período selecionado. Por favor, escolha outro horário ou sala.'; break;
                case 'reservation_failed': echo 'Erro ao criar reserva.'; break;
                case 'reservation_update_failed': echo 'Erro ao atualizar reserva.'; break;
                case 'reservation_delete_failed': echo 'Erro ao excluir reserva.'; break;
                case 'reservation_not_found': echo 'Sua reserva não foi encontrada.'; break;
                case 'unauthorized_access': echo 'Você não tem permissão para acessar esta reserva.'; break;
            }
        ?>
    </div>
<?php endif; ?>

<h2 class="mt-4">Minhas Reservas</h2>
<a href="/cliente/reservations/create" class="btn btn-primary mb-3">Fazer Nova Reserva</a>

<?php if (!empty($myReservations)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sala</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myReservations as $reservation): ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['id']) ?></td>
                        <td><?= htmlspecialchars($reservation['room_name']) ?></td>
                        <td><?= htmlspecialchars($reservation['start_time']) ?></td>
                        <td><?= htmlspecialchars($reservation['end_time']) ?></td>
                        <td>
                            <a href="/cliente/reservations/edit/<?= htmlspecialchars($reservation['id']) ?>" class="btn btn-sm btn-info me-2">Editar</a>
                            <form action="/reservations/<?= htmlspecialchars($reservation['id']) ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja cancelar esta reserva?');">
                                <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="alert alert-info">Você ainda não possui nenhuma reserva.</p>
<?php endif; ?>

<h2 class="mt-5">Disponibilidade das Salas (Calendário)</h2>
<p class="text-muted">Esta seção mostrará um calendário com a disponibilidade das salas.</p>
<?php if (!empty($roomAvailability)): ?>
    <div class="card card-body">
        <h5>Reservas Atuais (para visualização no calendário)</h5>
        <ul class="list-group">
            <?php foreach ($roomAvailability as $availability): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($availability['room_name']) ?>:</strong>
                    De <?= htmlspecialchars($availability['start_time']) ?>
                    até <?= htmlspecialchars($availability['end_time']) ?>
                    (Reservado por: <?= htmlspecialchars($availability['reserved_by_user']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p class="alert alert-warning">Nenhuma reserva atual para exibir a disponibilidade.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>