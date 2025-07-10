<?php require_once __DIR__ . '/../partials/header.php'; ?>

<h1 class="mb-4">Painel do Administrador</h1>

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
                case 'reservation_failed': echo 'Erro ao criar reserva. Verifique os dados.'; break;
                case 'reservation_update_failed': echo 'Erro ao atualizar reserva.'; break;
                case 'reservation_delete_failed': echo 'Erro ao excluir reserva.'; break;
                case 'reservation_not_found': echo 'Reserva não encontrada.'; break;
                case 'unauthorized_access': echo 'Acesso não autorizado à reserva.'; break;
                case 'room_unavailable': echo 'A sala não está disponível no período selecionado.'; break;
            }
        ?>
    </div>
<?php endif; ?>

<h2 class="mt-4">Todas as Reservas</h2>
<?php if (!empty($allReservations)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sala</th>
                    <th>Usuário</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Criado Em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allReservations as $reservation): ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['id']) ?></td>
                        <td><?= htmlspecialchars($reservation['room_name']) ?></td>
                        <td><?= htmlspecialchars($reservation['user_name']) ?> (<?= htmlspecialchars($reservation['user_email']) ?>)</td>
                        <td><?= htmlspecialchars($reservation['start_time']) ?></td>
                        <td><?= htmlspecialchars($reservation['end_time']) ?></td>
                        <td><?= htmlspecialchars($reservation['created_at']) ?></td>
                        <td>
                            <a href="/admin/reservations/edit/<?= htmlspecialchars($reservation['id']) ?>" class="btn btn-sm btn-info me-2">Editar</a>
                            <form action="/reservations/<?= htmlspecialchars($reservation['id']) ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta reserva?');">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="alert alert-info">Nenhuma reserva encontrada.</p>
<?php endif; ?>

<h2 class="mt-5">Gerenciar Salas e Usuários (Funcionalidades Futuras)</h2>
<p>Aqui você poderá adicionar links para gerenciar salas e usuários, como:</p>
<ul>
    <li><a href="/admin/rooms" class="btn btn-secondary btn-sm">Gerenciar Salas</a></li>
    <li><a href="/admin/users" class="btn btn-secondary btn-sm">Gerenciar Usuários</a></li>
</ul>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>