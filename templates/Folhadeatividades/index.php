<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade[]|\Cake\Collection\CollectionInterface $folhadeatividades
 */
// pr($estagiario);
// pr($folhadeatividades);
// die();

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
            <?= $this->Html->link(__('Cadastra nova atividade'), ['action' => 'add', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
            <?= $this->Html->link(__('Imprime folha de atividades'), ['action' => 'folhadeatividadespdf', '?' => ['estagiario_id' => $estagiario->id]], ['class' => 'button']) ?>
        </div>
    </aside>

    <h3><?= __('Folha de atividades da(o) estagiária(o) ' . $estagiario->aluno->nome) ?></h3>

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
                <?php $seconds = NULL ?>
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
                    list($hour, $minute, $second) = array_pad(explode(':', $folhadeatividade->horario), 3, null);
                    $seconds += (int) $hour * 3600;
                    $seconds += (int) $minute * 60;
                    $seconds += (int) $second;
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
                        echo $hours . ":" . $minutes . ":" . $seconds;
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