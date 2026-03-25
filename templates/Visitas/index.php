<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Visita> $visitas
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div class="visitas index content">
    <aside>
        <?php if ($user_data['administrador_id']) : ?>
            <div class="nav">
                <?php if (isset($instituicao_id)) : ?>
                    <?= $this->Html->link(__('Nova Visita'), ['action' => 'add', '?' => ['instituicao_id' => $instituicao_id]], ['class' => 'button']) ?>
                <?php else : ?>
                    <?= $this->Html->link(__('Nova Visita'), ['action' => 'add'], ['class' => 'button']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </aside>
    
    <h3><?= __('Lista de visitas') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('instituicao_id', 'Instiuição') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th><?= $this->Paginator->sort('motivo') ?></th>
                    <th><?= $this->Paginator->sort('responsavel', 'Responsável') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao', 'Avaliação') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitas as $visita) : ?>
                <tr> 
                    <?php //pr($visita); ?>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $visita->id]) ?>
                        <?php if ($user_data['administrador_id']) : ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $visita->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $visita->id], ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id)]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Html->link((string)$visita->id, ['action' => 'view', $visita->id]) ?></td>
                    <td><?= $visita->instituicao ? $this->Html->link($visita->instituicao->instituicao, ['controller' => 'Visitas', 'action' => 'view', $visita->id]) : '' ?></td>
                    <td><?= h($visita->data) ?></td>
                    <td><?= h($visita->motivo) ?></td>
                    <td><?= h($visita->responsavel) ?></td>
                    <td><?= h($visita->avaliacao) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>
</div>
