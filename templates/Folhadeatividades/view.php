<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade $folhadeatividade
 */
?>
<div class="areas view content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Estagiário(a)'), ['controller' => 'Estagiarios', 'action' => 'view', $folhadeatividade->estagiario_id], ['class' => 'button']) ?>
            <?= $this->Html->link(__('Editar atividade'), ['action' => 'edit', $folhadeatividade->id], ['class' => 'button']) ?>
            <?= $this->Form->postLink(__('Excluir atividade'), ['action' => 'delete', $folhadeatividade->id], ['confirm' => __('Tem certeza que quer excluir o registro # {0}?', $folhadeatividade->id), 'class' => 'button']) ?>
            <?= $this->Html->link(__('Listar atividades'), ['action' => 'index', '?' => ['estagiario_id' => $folhadeatividade->estagiario_id]], ['class' => 'button']) ?>
            <?= $this->Html->link(__('Nova atividade'), ['action' => 'add', '?' => ['estagiario_id' => $folhadeatividade->estagiario_id]], ['class' => 'button']) ?>
        </div>
    </aside>

    <div class="table_wrap">
        <h3><?= h('folhadeatividade_' . $folhadeatividade->id) ?></h3>
        <table class="table table-striped table-hover table-responsive">
            <tr>
                <th><?= __('Atividade') ?></th>
                <td><?= h($folhadeatividade->atividade) ?></td>
            </tr>
            <tr>
                <th><?= __('Id') ?></th>
                <td><?= $folhadeatividade->id ?></td>
            </tr>
            <tr>
                <th><?= __('Estagiário') ?></th>
                <td><?= $folhadeatividade->estagiario_id ?></td>
            </tr>
            <tr>
                <th><?= __('Día') ?></th>
                <td><?= h($folhadeatividade->dia) ?></td>
            </tr>
            <tr>
                <th><?= __('Início') ?></th>
                <td><?= h($folhadeatividade->inicio) ?></td>
            </tr>
            <tr>
                <th><?= __('Final') ?></th>
                <td><?= h($folhadeatividade->final) ?></td>
            </tr>
            <tr>
                <th><?= __('Horário') ?></th>
                <td><?= h($folhadeatividade->horario) ?></td>
            </tr>
        </table>
    </div>
</div>