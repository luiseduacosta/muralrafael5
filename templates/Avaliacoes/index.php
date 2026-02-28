<?php

declare(strict_types=1);

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Avaliacao[]|\Cake\Collection\CollectionInterface $avaliacoes
 */

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }

?>
<div class="avaliacoes index content">

    <?php if ( $user_data['administrador_id'] || $user_data['professor_id'] || $user_data['supervisor_id'] == 4 ): ?>

        <aside>
            <div class="nav">
                <?= $this->Html->link(__('Nova Avaliação'), ['action' => 'add'], ['class' => 'button']) ?>
            </div>
        </aside>

    <?php endif; ?>
    
    <?php if ($user_data['administrador_id']): ?>
    
        <h3><?= __('Lista de Avaliações') ?></h3>
    
        <div class="paginator">
            <?= $this->element('paginator'); ?>
        </div>
        <div class="table_wrap">
            <table>
                <thead>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.Alunos.nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.Instituicoes.instituicao', 'Instituicao') ?></th>
                    <th><?= $this->Paginator->sort('avaliacao1') ?></th>
                    <th><?= $this->Paginator->sort('timestamp') ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                    <tr>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $avaliacao->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $avaliacao->id]) ?>
                            <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $avaliacao->id], ['confirm' => __('Are you sure you want to delete {0}?', $avaliacao->id)]) ?>
                        </td>
                        <td><?= $this->Html->link((string)$avaliacao->id, ['action' => 'view', $avaliacao->id]) ?></td>
                        <td><?= ($avaliacao->estagiario and $avaliacao->estagiario->aluno) ? $this->Html->link(h($avaliacao->estagiario->aluno->nome), ['controller' => 'Alunos', 'action' => 'view', $avaliacao->estagiario->aluno->id]) : '' ?></td>
                                                <td><?= ($avaliacao->estagiario and $avaliacao->estagiario->instituicao) ? $this->Html->link(h($avaliacao->estagiario->instituicao->instituicao), ['controller' => 'Instituicoes', 'action' => 'view', $avaliacao->estagiario->instituicao->id]) : '' ?></td>
                        <td><?= h($avaliacao->avaliacao1) ?></td>
                        <td><?= h($avaliacao->timestamp) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="paginator">
            <?= $this->element('paginator'); ?>
            <?= $this->element('paginator_count'); ?>
        </div>

	<?php else: ?>
    
        <div class="table-responsive">
            <table class="table table-striped table-hover table-responsive">
                <thead>
                    <tr>
                        <?php if ($user_data['administrador_id']): ?>
                            <th class="actions"><?= __('Ações') ?></th>
                        <th><?= $this->Paginator->sort('id') ?></th>
                        <?php endif; ?>
                        <th><?= $this->Paginator->sort('estagiarios->avaliacao->id', 'Avaliação') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->aluno->nome', 'Aluno') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->periodo', 'Período') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->nivel', 'Nível') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->instituicao->instituicao', 'Instituição') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->supervisor->nome', 'Supervisor(a)') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->ch', 'Carga horária') ?></th>
                        <th><?= $this->Paginator->sort('estagiarios->nota', 'Nota') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estagiarios as $estagiario): ?>
                        <?php // pr($estagiario); ?>
                        <?php // die(); ?>
                        <tr>
                            <?php if ($user_data['administrador_id']): ?>
                                <?php if (isset($estagiario->avaliacao->id)): ?>
                                    <td class="actions">
                                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $estagiario->avaliacao->id]) ?>
                                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $estagiario->avaliacao->id]) ?>
                                        <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $estagiario->avaliacao->id], ['confirm' => __('Tem certeza que quer excluir este registro # {0}?', $estagiario->avaliacao->id)]) ?>
                                    </td>
                                <?php endif; ?>
                                <td><?= isset($estagiario->id) ? $this->Html->link((string)$estagiario->id, ['controller' => 'estagiarios', 'action' => 'view', $estagiario->id]) : '' ?></td>
                            <?php else: ?>
    
                                <?php if ($user_data['administrador_id'] || $user_data['supervisor_id']): ?>
                                    <td><?= $estagiario->hasValue('avaliacao') ? $this->Html->link('Ver avaliação', ['controller' => 'Avaliacoes', 'action' => 'view', $estagiario->avaliacao->id], ['class' => 'btn btn-success']) : $this->Html->link('Fazer avaliação', ['controller' => 'avaliacoes', 'action' => 'add', $estagiario->id], ['class' => 'btn btn-warning']) ?>
                                    </td>
                                <?php else: ?>
                                    <td><?= $estagiario->hasValue('avaliacao') ? $this->Html->link('Ver avaliação', ['controller' => 'Avaliacoes', 'action' => 'view', $estagiario->avaliacao->id], ['class' => 'btn btn-success']) : 'Sem avaliação on-line' ?>
                                    </td>
                                <?php endif; ?>
    
                                <?php if ($user_data['administrador_id']): ?>
                                    <td><?= $estagiario->hasValue('aluno') ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?>
                                    </td>
                                <?php else: ?>
                                    <td><?= $estagiario->hasValue('aluno') ? $estagiario->aluno->nome : '' ?></td>
                                <?php endif; ?>
    
                                <td><?= $estagiario->periodo ?></td>
                                <td><?= $estagiario->nivel ?></td>
                                <td><?= $estagiario->hasValue('instituicao') ? $estagiario->instituicao->instituicao : '' ?>
                                </td>
                                <td><?= $estagiario->hasValue('supervisor') ? $estagiario->supervisor->nome : '' ?></td>
                                <td><?= $estagiario->ch ?></td>
                                <td><?= $estagiario->nota ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    
    <?php endif; ?>
</div>