<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="inscricoes view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Inscricoes'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Html->link(__('Editar Inscricao'), ['action' => 'edit', $inscricao->id], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(__('Deletar Inscricao'), ['action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete inscricao_{0}?', $inscricao->id), 'class' => 'button']) ?>
                    <?= $this->Html->link(__('Nova Inscricao'), ['action' => 'add'], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>inscricao_<?= h($inscricao->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($inscricao->id) ?></td>
                </tr>               
                <tr>
                    <th><?= __('Aluno') ?></th>
                    <td><?= $inscricao->aluno ? $this->Html->link($inscricao->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $inscricao->aluno->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Estagio') ?></th>
                    <td><?= $inscricao->muralestagio ? $this->Html->link($inscricao->muralestagio->instituicao ? $inscricao->muralestagio->instituicao->instituicao : $inscricao->muralestagio->id , ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : $inscricao->muralestagio_id ?></td>
                </tr>
                <tr>
                    <th><?= __('Periodo') ?></th>
                    <td><?= h($inscricao->periodo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro') ?></th>
                    <td><?= $this->Number->format($inscricao->registro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data') ?></th>
                    <td><?= $inscricao->data ? h($inscricao->data) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Timestamp') ?></th>
                    <td><?= $inscricao->timestamp ? h($inscricao->timestamp) : '' ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
