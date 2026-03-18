<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Folhadeatividade> $folhadeatividades
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}

$supervisora = isset($estagiario->supervisor->nome);
if ($supervisora) {
    $supervisora = $estagiario->supervisor->nome;
} else {
    $supervisora = '_______________';
}

$cress = isset($estagiario->supervisor->cress);
if ($cress) {
    $cress = $estagiario->supervisor->cress;
} else {
    $cress = '_______________';
}

$professora = isset($estagiario->professor->nome);
if ($professora) {
    $professora = $estagiario->professor->nome;
} else {
    $professora = '_______________';
}
?>

<div class="folhadeatividades index content">

    <aside>
        <div class="nav">
            <?php if ($user_data['administrador_id']) : ?>
                <?= $this->Html->link(__('Estagiario(a)'), ['controller' => 'Estagiarios','action' => 'view', $estagiario->id], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Nova atividade'), ['action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Imprime atividades'), ['action' => 'folhadeatividadespdf', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Imprime folha de atividades'), ['action' => 'atividadesmanualpdf', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if ($user_data['professor_id'] && $user_data['professor_id'] == $estagiario->professor_id) : ?>
                <?= $this->Html->link(__('Atividades'), ['controller' => 'Folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação on-line'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação'), ['controller' => 'Avaliacoes', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('CH e nota'), ['controller' => 'Estagiarios', 'action' => 'view', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?php endif; ?>
            <?php if ($user_data['supervisor_id'] && $user_data['supervisor_id'] == $estagiario->supervisor_id) : ?>
                <?= $this->Html->link(__('Atividades'), ['controller' => 'Folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
                <?= $this->Html->link(__('Avaliação on-line'), ['controller' => 'Avaliacoes', 'action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
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
                <td><?= $estagiario->periodo ?></td>
                <td><?= $estagiario->nivel ?></td>
                <td><?= $estagiario->instituicao->instituicao ?></td>
                <td><?= $supervisora ?></td>
                <td><?= $professora ?></td>
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
                <?php $seconds = null ?>
                <?php foreach ($folhadeatividades as $folhadeatividade) : ?>
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
                    [$hour, $minute, $second] = array_pad(explode(':', (string)$folhadeatividade->horario), 3, null);
                    $seconds += (int)$hour * 3600;
                    $seconds += (int)$minute * 60;
                    $seconds += (int)$second;
                    // pr($seconds);
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5">Total de horas</td>
                    <td>
                        <?php
                        $hours = floor($seconds / 3600);
                        $seconds -= $hours * 3600;
                        $minutes = floor($seconds / 60);
                        $seconds -= $minutes * 60;
                        echo $hours . ':' . $minutes . ':' . $seconds;
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