<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visita $visita
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<div>
    <div class="column-responsive column-80">
        <div class="visitas form content">
        <aside>
            <div class="nav">
                <?= $this->Html->link(__('Listar Visitas'), ['action' => 'index'], ['class' => 'button']) ?>
                <?php if ($user_data['administrador_id']) : ?>
                    <?= $this->Form->postLink(
                        __('Excluir'),
                        ['action' => 'delete', $visita->id],
                        ['confirm' => __('Are you sure you want to delete visita_{0}?', $visita->id), 'class' => 'button'],
                    ) ?>
                <?php endif; ?>
            </div>
        </aside>
            <?= $this->Form->create($visita) ?>
            <fieldset>
                <h3><?= __('Editando visita_') . $visita->id ?></h3>
                <?php
                    echo $this->Form->control('instituicao_id', ['options' => $instituicoes]);
                    echo $this->Form->control('data');
                    echo $this->Form->control('motivo');
                    echo $this->Form->control('responsavel', ['label' => 'Responsável']);
                    echo $this->Form->control('descricao', ['label' => 'Descrição']);
                    echo $this->Form->control('avaliacao', ['label' => 'Avaliação']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
