<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao[]|\Cake\Collection\CollectionInterface $avaliacoes
 */
// pr($estagiario->item->aluno);
// die();
?>
<div class="avaliacoes index container">
    <h3><?= __('Estágios cursados pela(o) estudande ') ?></h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao->id', 'Declaração') ?></th>
                    <th><?= $this->Paginator->sort('aluno->nome', 'Aluno') ?></th> 
                    <th><?= $this->Paginator->sort('periodo', 'Período') ?></th>
                    <th><?= $this->Paginator->sort('nivel', 'Nível') ?></th>
                    <th><?= $this->Paginator->sort('instituicao->instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('supervisor->nome', 'Supervisor(a)') ?></th>
                    <th><?= $this->Paginator->sort('ch', 'Carga horária') ?></th>
                    <th><?= $this->Paginator->sort('nota', 'Nota') ?></th>
                    <th class="actions"><?= __('Ações') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estagiarios as $estagiario): ?>
                    <?php // pr($estagiario); ?>
                    <?php // die(); ?>
                    <tr>
                        <td><?= isset($estagiario->id) ? $this->Html->link((string)$estagiario->id, ['controller' => 'estagiarios', 'action' => 'view', $estagiario->id]) : '' ?></td>
                        <td><?= $this->Html->link('Imprime folha de avaliação', ['controller' => 'avaliacoes', 'action' => 'imprimeavaliacaopdf', $estagiario->id], ['class' => 'btn btn-success']) ?></td>
                        <td><?= $estagiario->hasValue('aluno') ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                        <td><?= $estagiario->periodo ?></td>
                        <td><?= $estagiario->nivel ?></td>
                        <td><?= $estagiario->hasValue('instituicao') ? $estagiario->instituicao->instituicao : '' ?></td>                        
                        <td><?= $estagiario->hasValue('supervisor') ? $estagiario->supervisor->nome : '' ?></td>
                        <td><?= $estagiario->ch ?></td>
                        <td><?= $estagiario->nota ?></td>
                        <?php if (isset($estagiario->id)): ?>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view', $estagiario->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $estagiario->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Tem certeza que quer excluir o registro # {0}?', $estagiario->id)]) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
