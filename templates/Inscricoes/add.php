<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inscricao $inscricao
 */

declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div class="inscricoes form content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Listar Inscricoes'), ['action' => 'index'], ['class' => 'button']) ?>
        </div>
    </aside>
    <?= $this->Form->create($inscricao) ?>
    <fieldset>
        <h3><?= __('Adicionar Inscricao') ?></h3>
        <?php
            echo $this->Form->control('aluno_none', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $aluno ? $aluno->nome : '']);
            echo $this->Form->hidden('registro', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $aluno ? $aluno->registro : '']);
            echo $this->Form->hidden('aluno_id', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $aluno ? $aluno->id : '']);
            echo $this->Form->control('instituicao', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $instituicao ? $instituicao->instituicao : '']);
            echo $this->Form->hidden('mural_estagio_id', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $mural_estagio ? $mural_estagio->id : '']);
            echo $this->Form->control('data', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $data ? $data : '']);
            echo $this->Form->control('periodo', ['disabled' => !$user_data['administrador_id'], 'type' => 'text', 'value' => $periodo ? $periodo : '']);
            echo $this->Form->hidden('timestamp', ['disabled' => !$user_data['administrador_id']]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Confirmar Inscrição'), ['class' => 'button btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
