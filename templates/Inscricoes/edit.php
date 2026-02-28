<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
 */

//pr($inscricao)

?>
<div>
    <div class="column-responsive column-80">
        <div class="inscricoes form content">
            <aside>
                <div class="side-nav">
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $inscricao->id],
                        ['confirm' => __('Are you sure you want to delete inscricao_{0}?', $inscricao->id), 'class' => 'button']
                    ) ?>
                    <?= $this->Html->link(__('Listar Inscricoes'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($inscricao) ?>
            <fieldset>
                <h3><?= __('Editando Inscricao do aluno(a) ' . $inscricao->aluno->nome) ?></h3>
                <?php
                    echo $this->Form->control('registro');
                    echo $this->Form->control('aluno_id', ['type' => 'number', 'label' => 'aluno_id']);
                    echo $this->Form->control('mural_estagio_id', ['type' => 'number', 'label' => 'mural_estagio_id']);
                    echo $this->Form->control('data');
                    echo $this->Form->control('periodo');
                    echo $this->Form->control('timestamp');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
