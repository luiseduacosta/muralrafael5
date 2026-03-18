<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
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
        <div class="inscricoes form content">
            <aside>
                <div class="side-nav">
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $inscricao->id],
                            ['confirm' => __('Are you sure you want to delete inscricao_{0}?', $inscricao->id), 'class' => 'button'],
                        ) ?>
                    <?php endif; ?>
                    <?= $this->Html->link(__('Listar Inscricoes'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($inscricao) ?>
            <fieldset>
                <h3><?= __('Editando Inscricao do aluno(a) ' . $inscricao->aluno->nome) ?></h3>
                <?php
                    echo $this->Form->control('registro');
                    echo $this->Form->control('aluno_id', ['type' => 'number', 'label' => 'Aluno(a)']);
                    echo $this->Form->control('muralestagio_id', ['type' => 'number', 'label' => 'Muralestagio']);
                    echo $this->Form->control('data');
                    echo $this->Form->control('periodo');
                    echo $this->Form->control('timestamp');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
