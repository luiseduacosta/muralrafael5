<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Complemento $nome
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="complementos form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Complementos'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $complemento->id],
                        ['confirm' => __('Are you sure you want to delete {0}?', $complemento->nome), 'class' => 'button']
                    ) ?>
                </div>
            </aside>
            <?= $this->Form->create($complemento) ?>
            <fieldset>
                <h3><?= __('Editando complemento_' . $complemento->id) ?></h3>
                <?php
                    echo $this->Form->control('periodo_especial');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
