<?php
/**
 * @var \App\View\AppView $this
 * @var array $supervisorseminstituicao
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

// DataTables CSS (layout already loads jQuery in <head> — do not load jQuery again here)
$this->Html->css(
    'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',
    ['block' => true],
);
?>

<div class="estagiarios index content">
    <h3><?= __('Supervisores com instituição cadastrada errada') ?></h3>
    <div class="table-responsive">
        <table id="supervisoresTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th><?= __('Aluno(a)') ?></th>
                    <th><?= __('Instituicao') ?></th>
                    <th><?= __('Supervisor(a)') ?></th>
                    <th><?= __('Período') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supervisorseminstituicao as $item): ?>
                    <tr>
                        <td><?= empty($item['aluno_nome']) ? $this->Html->link((string)$item['registro'], ['controller' => 'Estagiarios', 'action' => 'view', $item['estagiario_id']]) : $this->Html->link(h($item['aluno_nome']), ['controller' => 'Estagiarios', 'action' => 'view', $item['estagiario_id']]) ?></td>
                        <td><?= empty($item['instituicao_nome']) ? '' : $this->Html->link($item['instituicao_nome'], ['controller' => 'Instituicoes', 'action' => 'view', $item['instituicao_id']]) ?></td>
                        <td><?= empty($item['supervisor_nome']) ? '' : $this->Html->link(h($item['supervisor_nome']), ['controller' => 'Supervisores', 'action' => 'view', $item['supervisor_id']]) ?></td>
                        <td><?= h($item['periodo']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->Html->script(
    [
        'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
        'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js'
    ],
    ['block' => true],
);
$this->Html->scriptBlock(
    <<<'JS'
$(function () {
    $('#supervisoresTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']]
    });
});
JS
    ,
    ['block' => true],
);
