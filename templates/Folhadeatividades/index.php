<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Folhadeatividade> $folhadeatividades
 * @var \App\Model\Entity\Estagiario $estagiario
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

$supervisora = isset($estagiario->supervisor->nome) ? $estagiario->supervisor->nome : '_______________';
$cress = isset($estagiario->supervisor->cress) ? $estagiario->supervisor->cress : '_______________';
$professora = isset($estagiario->professor->nome) ? $estagiario->professor->nome : '_______________';
?>

<div class="folhadeatividades index content">

    <aside>
        <div class="nav">
            <?php if (!empty($user_data['administrador_id'])): ?>
                <?= $this->Html->link(__('Estagiario(a)'), ['controller' => 'Estagiarios', 'action' => 'view', $estagiario->id], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Nova atividade'), ['action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Imprime atividades'), ['action' => 'folhadeatividadespdf', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Imprime folha de atividades'), ['action' => 'atividadesmanualpdf', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if (!empty($user_data['professor_id']) && $user_data['professor_id'] == $estagiario->professor_id): ?>
                <?= $this->Html->link(__('Atividades'), ['controller' => 'Folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação on-line'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('CH e nota'), ['controller' => 'Estagiarios', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if (!empty($user_data['supervisor_id']) && $user_data['supervisor_id'] == $estagiario->supervisor_id): ?>
                <?= $this->Html->link(__('Atividades'), ['controller' => 'Folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação on-line'), ['controller' => 'Avaliacoes', 'action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if (!empty($user_data['aluno_id']) && $user_data['aluno_id'] == $estagiario->aluno_id): ?>
                <?= $this->Html->link(__('Nova atividade'), ['controller' => 'Folhadeatividades', 'action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
        </div>
    </aside>

    <h3><?= __('Folha de atividades da(o) estagiária(o) ' . ($estagiario->aluno->nome ?? ' S/d')) ?></h3>

    <div class="table_wrap">
        <table>
            <tr>
                <th>Período</th>
                <th>Nível</th>
                <th>Instituição</th>
                <th>Supervisor</th>
                <th>Professor(a)</th>
            </tr>
            <tr>
                <td><?= h($estagiario->periodo) ?></td>
                <td><?= h($estagiario->nivel) ?></td>
                <td><?= h($estagiario->instituicao->instituicao ?? '') ?></td>
                <td><?= h($supervisora) ?></td>
                <td><?= h($professora) ?></td>
            </tr>
        </table>
    </div>

    <div class="paginator">
        <?= $this->element('paginator'); ?>
    </div>
    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="actions"><?= __('Ações') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('dia') ?></th>
                    <th><?= $this->Paginator->sort('inicio') ?></th>
                    <th><?= $this->Paginator->sort('final') ?></th>
                    <th><?= $this->Paginator->sort('horario', 'Horas') ?></th>
                    <th><?= $this->Paginator->sort('atividade') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $seconds = 0; ?>
                <?php foreach ($folhadeatividades as $folhadeatividade): ?>
                    <tr>
                        <td class="actions">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $folhadeatividade->id]) ?>
                            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $folhadeatividade->id]) ?>
                            <?= $this->Form->postLink(__('Excluir'), ['action' => 'delete', $folhadeatividade->id], ['confirm' => __('Tem certeza que quer excluir o registro # {0}?', $folhadeatividade->id)]) ?>
                        </td>
                        <td><?= $folhadeatividade->id ?></td>
                        <td><?= h($folhadeatividade->dia) ?></td>
                        <td><?= h($folhadeatividade->inicio) ?></td>
                        <td><?= h($folhadeatividade->final) ?></td>
                        <td><?= h($folhadeatividade->horario) ?></td>
                        <td><?= h($folhadeatividade->atividade) ?></td>
                    </tr>
                    <?php
                    if (!empty($folhadeatividade->horario)) {
                        [$hour, $minute, $second] = array_pad(explode(':', (string)$folhadeatividade->horario), 3, '0');
                        $seconds += (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                    }
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5">Total de horas</td>
                    <td>
                        <?php
                        $hours = floor($seconds / 3600);
                        $remSeconds = $seconds % 3600;
                        $minutes = floor($remSeconds / 60);
                        $remSeconds = $remSeconds % 60;
                        echo sprintf('%02d:%02d:%02d', $hours, $minutes, $remSeconds);
                        ?>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <?= $this->element('paginator'); ?>
        <?= $this->element('paginator_count'); ?>
    </div>

</div>