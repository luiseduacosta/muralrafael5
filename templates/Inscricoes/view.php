<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
 */
$user_data = ['administrador_id' => 0,'aluno_id' => 0,'professor_id' => 0,'supervisor_id' => 0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="inscricoes view content">
            <aside>
                <div class="nav">
                    <?php if ($user_data['aluno_id'] && ($user_data['aluno_id'] == $inscricao->aluno_id)) : ?>
                        <?= $this->Html->link(__('Voltar'), 'javascript:history.back()', ['class' => 'button']) ?>
                        <?= $this->Form->postLink(__('Excluir Inscrição'), ['action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete inscricao_{0}?', $inscricao->id), 'class' => 'button']) ?>
                    <?php elseif ($user_data['administrador_id']) : ?>
                        <?= $this->Html->link(__('Listar Inscricões'), ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio_id], ['class' => 'button btn-secondary']) ?>
                        <?= $this->Form->postLink(__('Excluir Inscrição'), ['action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete inscricao_{0}?', $inscricao->id), 'class' => 'button']) ?>
                    <?php endif ?>
                </div>
            </aside>
            <h3>Inscricao_<?= h($inscricao->id) ?></h3>
            <table class="table table-hover table-striped table-responsive">
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($inscricao->id) ?></td>
                </tr>               
                <tr>
                    <th><?= __('Aluno') ?></th>
                    <td><?= $inscricao->aluno ? $this->Html->link($inscricao->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $inscricao->aluno->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Mural') ?></th>
                    <td><?= $inscricao->muralestagio ? $this->Html->link($inscricao->muralestagio->instituicao_entidade->instituicao ?? $inscricao->muralestagio->id, ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : $inscricao->muralestagio_id ?></td>
                </tr>
                <tr>
                    <th><?= __('Periodo') ?></th>
                    <td><?= h($inscricao->periodo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registro') ?></th>
                    <td><?= h($inscricao->registro) ?></td>
                </tr>
                <tr>
                    <th><?= __('Data') ?></th>
                    <td><?= $inscricao->data ? $inscricao->data->format('d/m/Y') : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Timestamp') ?></th>
                    <td><?= $inscricao->timestamp ? $inscricao->timestamp->format('d/m/Y H:i') : '' ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
