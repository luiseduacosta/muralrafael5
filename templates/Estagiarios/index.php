<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Estagiario> $estagiarios
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<div class="estagiarios index content">

    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['controller' => 'Estagiarios', 'action' => 'index'], 'class' => 'form-inline']); ?>
    
    <div class="row justify-content-center">
        <div class="col-auto">
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Form->label('estagiarioperiodo', 'Período'); ?>
                <?= $this->Form->input('periodo', [
                        'default' => $periodo ?? $configuracao->termo_compromisso_periodo,
                        'id' => 'estagiarioperiodo',
                        'type' => 'select',
                        'options' => $periodos,
                        'class' => 'form-control',
                        'onChange' => 'this.form.submit();',
                    ]);
                ?>
            <?php else : ?>
                <h3 class="label">Período: <?= $configuracao['mural_periodo_atual']; ?></h3>
            <?php endif; ?>
        </div>
    </div>

    <?= $this->Form->end() ?>
    
    <aside class='row d-flex justify-content-between'>
        <div class="nav">
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Html->link(__('Novo Estagiario'), ['action' => 'add'], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
        
        <div class="paginator">
            <?= $this->element('paginator'); ?>
        </div>

        <div class='nav'>
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Html->link(__('Relatório de erros'), ['action' => 'relatorio'], ['class' => 'button']) ?>
            <?php endif; ?>    
        </div>
    </aside>

    <h3><?= __('Lista de Estagiarios') ?></h3>

    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <?php if ($user_data['administrador_id']) : ?>
                        <th class="actions"><?= __('Actions') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('Estagiarios.id', 'Id') ?></th>
                    <th><?= $this->Paginator->sort('Alunos.nome', 'Aluno') ?></th>
                    <th><?= $this->Paginator->sort('Instituicoes.instituicao', 'Instituicao') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.periodo', 'Período') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.nivel', 'Nível') ?></th>
                    <th><?= $this->Paginator->sort('Supervisores.nome', 'Supervisor') ?></th>
                    <th><?= $this->Paginator->sort('Professores.nome', 'Professor') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.nota', 'Nota') ?></th>
                    <th><?= $this->Paginator->sort('Estagiarios.ch', 'CH') ?></th>
                </tr>

                <?= $this->Form->create(null, ['type' => 'get', 'url' => ['controller' => 'Estagiarios', 'action' => 'index'], 'class' => 'form-inline']) ?>
                <tr class='filters'>
                    <?php if ($user_data['administrador_id']) : ?>
                        <th></th>
                    <?php endif; ?>
                    <th></th>
                    <th><?= $this->Form->select('aluno_id', $alunos, ['empty' => 'Todos', 'value' => $this->request->getQuery('aluno_id'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th><?= $this->Form->select('instituicao_id', $instituicoes, ['empty' => 'Todos', 'value' => $this->request->getQuery('instituicao_id'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th></th>
                    <th><?= $this->Form->select('nivel', $niveis, ['empty' => 'Todos', 'value' => $this->request->getQuery('nivel'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th><?= $this->Form->select('supervisor_id', $supervisores, ['empty' => 'Todos', 'value' => $this->request->getQuery('supervisor_id'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th><?= $this->Form->select('professor_id', $professores, ['empty' => 'Todos', 'value' => $this->request->getQuery('professor_id'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th><?= $this->Form->select('nota', $notas, ['empty' => 'Todos', 'value' => $this->request->getQuery('nota'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                    <th><?= $this->Form->select('ch', $chs, ['empty' => 'Todos', 'value' => $this->request->getQuery('ch'), 'onChange' => 'this.form.submit();'], ['class' => 'form-control']) ?></th>
                </tr>
                <?= $this->Form->end() ?>

            </thead>
            <tbody>
                <?php foreach ($estagiarios as $estagiario) : ?>
                <tr>
                    <?php if ($user_data['administrador_id']) : ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $estagiario->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $estagiario->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $estagiario->id], ['confirm' => __('Are you sure you want to delete estagiario #{0}?', $estagiario->id)]) ?>
                        </td>
                    <?php endif; ?>
                    <td><?= $this->Html->link((string)$estagiario->id, ['action' => 'view', $estagiario->id]) ?></td>
                    <td><?= $estagiario->aluno ? $this->Html->link($estagiario->aluno->nome, ['controller' => 'Alunos', 'action' => 'view', $estagiario->aluno->id]) : '' ?></td>
                    <td><?= $estagiario->instituicao ? $this->Html->link($estagiario->instituicao->instituicao, ['controller' => 'Instituicoes', 'action' => 'view', $estagiario->instituicao->id]) : '' ?></td>
                    <td><?= h($estagiario->periodo) ?></td>
                    <td><?= h($estagiario->nivel) ?></td>
                    <td><?= ($estagiario->supervisor and $estagiario->supervisor->nome) ? $this->Html->link($estagiario->supervisor->nome, ['controller' => 'Supervisores', 'action' => 'view', $estagiario->supervisor->id]) : '' ?></td>
                    <td><?= $estagiario->professor ? $this->Html->link($estagiario->professor->nome, ['controller' => 'Professores', 'action' => 'view', $estagiario->professor->id]) : '' ?></td>
                    <td><?= $estagiario->nota ? $this->Number->format($estagiario->nota) : '' ?></td>
                    <td><?= $estagiario->ch ? $this->Number->format($estagiario->ch) : '' ?></td>
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
