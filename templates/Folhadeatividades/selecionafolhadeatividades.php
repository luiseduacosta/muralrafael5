<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade[]|\Cake\Collection\CollectionInterface $folhadeatividades
 */
// pr($estagiarios);
// die();

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }

?>

<div class="folhadeatividades index content">
    
    <h3><?= __('Estágios cursados pela(o) estudande ') ?></h3>
    
    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <?php if ($user_data['administrador_id']): ?>
                        <th class="actions"><?= __('Ações') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao->id', 'Folha de atividades') ?></th>
                    <th><?= $this->Paginator->sort('aluno->nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('periodo', 'Período') ?></th>
                    <th><?= $this->Paginator->sort('nivel', 'Nível') ?></th>
                    <th><?= $this->Paginator->sort('instituicao->instituicao', 'Instituição') ?></th>
                    <th><?= $this->Paginator->sort('supervisor->nome', 'Supervisor(a)') ?></th>
                    <th><?= $this->Paginator->sort('ch', 'Carga horária') ?></th>
                    <th><?= $this->Paginator->sort('nota', 'Nota') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estagiarios as $estagiario): ?>
                    <?php // pr($estagiario); ?>
                    <?php // die(); ?>
                    <tr>

                        <?php if ($user_data['administrador_id']): ?> 
                            <?php if (isset($estagiario->id)): ?>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $estagiario->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $estagiario->id]) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Tem certeza que quer excluir o registro # {0}?', $estagiario->id)]) ?>
                                </td>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($user_data['administrador_id']): ?>
                            <td><?= isset($estagiario->id) ? $this->Html->link((string)$estagiario->id, ['controller' => 'estagiarios', 'action' => 'view', $estagiario->id]) : '' ?></td>
                        <?php else: ?>
                            <td><?= isset($estagiario->id) ? $estagiario->id : '' ?></td>
                        <?php endif; ?>

                        <td><?= $this->Html->link('Preencher folha de atividades', ['controller' => 'folhadeatividades', 'action' => 'index', $estagiario->id]) ?></td>

                        <?php if ($user_data['administrador_id']): ?>
                            <td><?= $estagiario->hasValue('aluno') ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                        <?php else: ?>
                            <td><?= $estagiario->hasValue('aluno') ? $estagiario->aluno->nome : '' ?></td>
                        <?php endif; ?>

                        <td><?= $estagiario->periodo ?></td>
                        <td><?= $estagiario->nivel ?></td>
                        <td><?= $estagiario->hasValue('instituicao') ? $estagiario->instituicao->instituicao : '' ?></td>
                        <td><?= $estagiario->hasValue('supervisor') ? $estagiario->supervisor->nome : '' ?></td>
                        <td><?= $estagiario->ch ?></td>
                        <td><?= $estagiario->nota ?></td>

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
