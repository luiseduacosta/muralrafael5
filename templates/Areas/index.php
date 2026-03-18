<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Area> $areas
 */
$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div class="areas index content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Html->link(__('Nova Área'), ['action' => 'add'], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>
    
    <h3><?= __('Lista de Áreas') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('area', 'Área') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areas as $area) : ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $area->id]) ?>
                        <?php if ($user_data['administrador_id']) : ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $area->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $area->id], ['confirm' => __('Are you sure you want to delete {0}?', $area->area)]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Number->format($area->id) ?></td>
                    <td><?= $this->Html->link($area->area, ['action' => 'view', $area->id]) ?></td>
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
