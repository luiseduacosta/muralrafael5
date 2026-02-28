<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Folhadeatividade $folhadeatividade
 */
// pr($folhadeatividades);
// pr($estagiario);
// die();
?>

<div class="areas add content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Listar atividades'), ['controller' => 'folhadeatividades', 'action' => 'index', '?' => ['estagiario_id' => isset($estagiario) ? $estagiario->id : '1']], ['class' => 'button']) ?>
        </div>
    </aside>

    <div>

        <?php if (isset($folhadeatividades)): ?>
            <h3>Últimas 5 atividades</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-responsive">
                    <tr>
                        <th>Id</th>
                        <th>Data</th>
                        <th>Início</th>
                        <th>Final</th>
                        <th>Horário</th>
                        <th>Atividade</th>
                    </tr>
                    <?php /** Últimos 5 registros */ ?>
                    <?php $i = 0 ?>
                    <?php $total = count($folhadeatividades->toArray()); ?>
                    <?php $ultimos = $total - 6; ?>
                    <?php foreach ($folhadeatividades as $folhadeatividade): ?>
                        <tr>
                            <?php if ($i > $ultimos): ?>
                                <?php // pr($folhadeatividade) ?>
                                <td><?= $folhadeatividade->id ?></td>
                                <td><?= $folhadeatividade->dia ?></td>
                                <td><?= $folhadeatividade->inicio ?></td>
                                <td><?= $folhadeatividade->final ?></td>
                                <td><?= $folhadeatividade->horario ?></td>
                                <td><?= $folhadeatividade->atividade ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php else: ?>
            <p><?php echo 'Sem atividades' . '<br>' ?></p>
        <?php endif; ?>

        <?= $this->Form->create(null, ['type' => 'post']) ?>
        <fieldset>
            <h3><?= __('Adicionar uma atividade') ?></h3>
            <?php
            echo isset($estagiario) ? $this->Form->control('estagiario_id', ['options' => [$estagiario->id => $estagiario->aluno->nome]], 'readonly') : $this->Form->control('estagiario_id', ['type' => 'number']);
            echo $this->Form->control('dia');
            echo $this->Form->control('inicio', ['label' => ['text' => 'Horário de início']]);
            echo $this->Form->control('final', ['label' => ['text' => 'Horário de finalização']]);
            echo $this->Form->control('atividade', ['label' => ['text' => 'Atividade realizada']]);
            echo $this->Form->control('horario', ['type' => 'hidden', 'value' => null]);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>

    </div>
</div>