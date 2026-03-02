<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao[]|\Cake\Collection\CollectionInterface $avaliacoes
 */
// pr($estagiario);
// die();

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
    
?>
<div class="container">
    <h3><?= __('Avaliações') ?></h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <?php if ($user_data['administrador_id']): ?>
                        <th class="actions"><?= __('Ações') ?></th>
                        <th><?= $this->Paginator->sort('id') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('id', 'Avaliação on-line') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao->id', 'Imprime avaliação') ?></th>
                    <th><?= $this->Paginator->sort('aluno->nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('folhadeatividade->id', 'Folha de atividades') ?></th>
                    <th><?= $this->Paginator->sort('periodo', 'Período') ?></th>
                    <th><?= $this->Paginator->sort('professor->nome', 'Professor') ?></th>
                    <th><?= $this->Paginator->sort('nivel', 'Nível') ?></th>
                    <th><?= $this->Paginator->sort('ch', 'Carga horária') ?></th>
                    <th><?= $this->Paginator->sort('nota', 'Nota') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estagiarios as $estagiario): ?>
                    <?php // pr(estagiario); ?>
                    <?php // die(); ?>
                    <tr>
                        <?php if ($user_data['administrador_id']): ?>
                            <?php if (isset($estagiario->avaliacao->id)): ?>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $estagiario->avaliacao->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $estagiario->avaliacao->id]) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $estagiario->avaliacao->id], ['confirm' => __('Tem certeza que quer excluir o registro # {0}?', $estagiario->avaliacao->id)]) ?>
                                </td>
                            <?php endif; ?>
                            <td><?= isset($estagiario->id) ? $this->Html->link((string)$estagiario->id, ['controller' => 'estagiarios', 'action' => 'view', $estagiario->id]) : '' ?></td>
                        <?php else: ?>
                            <td><?= isset($estagiario->id) ? $estagiario->id : '' ?></td>
                        <?php endif; ?>

                        <?php if ($user_data['administrador_id'] || $user_data['supervisor_id']): ?>
                            <td><?= $estagiario->hasValue('avaliacao') ? $this->Html->link('Ver avaliação', ['controller' => 'Avaliacoes', 'action' => 'view', $estagiario->avaliacao->id], ['class' => 'btn btn-success']) : $this->Html->link('Fazer avaliação on-line', ['controller' => 'avaliacoes', 'action' => 'add', $estagiario->id], ['class' => 'btn btn-warning']) ?></td>
                        <?php else: ?>
                            <td><?= $estagiario->hasValue('avaliacao') ? $this->Html->link('Ver avaliação', ['controller' => 'Avaliacoes', 'action' => 'view', $estagiario->avaliacao->id], ['class' => 'btn btn-success']) : 'Sem avaliação on-line' ?></td>
                        <?php endif; ?>

                        <td><?= $this->Html->link('Imprime avaliação discente', ['controller' => 'estagiarios', 'action' => 'avaliacaodiscentepdf', $estagiario->id], ['class' => 'btn btn-success']) ?></td>    

                        <?php if ($user_data['administrador_id']): ?>
                            <td><?= $estagiario->hasValue('aluno') ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                        <?php else: ?>
                            <td><?= $estagiario->hasValue('aluno') ? $estagiario->aluno->nome : '' ?></td>
                        <?php endif; ?>

                        <td><?= $estagiario->hasValue('folhadeatividade') ? $this->Html->link('Ver folha de atividades on-line', ['controller' => 'folhadeatividades', 'action' => 'index', $estagiario->id], ['class' => 'btn btn-success']) : $this->Html->link('Imprimir folha', ['controller' => 'estagiarios', 'action' => 'folhadeatividadespdf', $estagiario->id]) ?></td>
                        <td><?= $estagiario->periodo ?></td>
                        <td><?= $estagiario->hasValue('professor') ? $estagiario->professor->nome : '' ?></td>
                        <td><?= $estagiario->nivel ?></td>
                        <td><?= $estagiario->ch ?></td>
                        <td><?= $estagiario->nota ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
