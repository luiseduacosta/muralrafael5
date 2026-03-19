<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Inscricao> $inscricoes
 */
$user_data = ['administrador_id' => 0,'aluno_id' => 0,'professor_id' => 0,'supervisor_id' => 0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<div class="inscricoes index content">
    <aside>
        <?php if ($user_data['administrador_id']) : ?>
            <div class="nav">
                <?= $this->Html->link(__('Nova Inscricao'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
        <?php endif; ?>
    </aside>
    
    <div class="row justify-content-center">
        <div class="col-auto">
            <?php echo $this->Form->create($inscricoes, ['type' => 'get', 'url' => ['controller' => 'Inscricoes', 'action' => 'index'], 'class' => 'form-inline']); ?>
                <?= $this->Form->label('periodo', 'Período'); ?>
                <?= $this->Form->input('periodo', [
                    'label' => false,
                    'type' => 'select',
                    'options' => $periodos,
                    'value' => $periodo,
                    'class' => 'form-control',
                    'onchange' => 'this.form.submit()',
                ]); ?>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>

    <h3><?= __('Lista de inscrições') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('aluno_id') ?></th>
                    <th><?= $this->Paginator->sort('registro') ?></th>
                    <th><?= $this->Paginator->sort('muralestagio_id') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th><?= $this->Paginator->sort('periodo') ?></th>
                    <th><?= $this->Paginator->sort('timestamp') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscricoes as $inscricao) : ?>    
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $inscricao->id]) ?>
                        <?php if ($user_data['administrador_id']) : ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $inscricao->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $inscricao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $inscricao->id)]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Html->link((string)$inscricao->id, ['action' => 'view', $inscricao->id]) ?></td>
                    <td><?= $inscricao->aluno ? $this->Html->link($inscricao->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $inscricao->aluno->id]) : '' ?></td>
                    <td><?= (string)$inscricao->registro ?></td>
                    <td><?= $inscricao->muralestagio->instituicao_entidade ? $this->Html->link($inscricao->muralestagio->instituicao_entidade->instituicao, ['controller' => 'Muralestagios', 'action' => 'view', $inscricao->muralestagio->id]) : '' ?></td>
                    <td><?= $inscricao->data ? $inscricao->data->format('d/m/Y') : '' ?></td>
                    <td><?= h($inscricao->periodo) ?></td>
                    <td><?= $inscricao->timestamp ? $inscricao->timestamp->format('d/m/Y H:i:s') : '' ?></td>
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
