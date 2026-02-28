<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supervisor $supervisor
 */

declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="supervisores form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Supervisores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(
                        __('Deletar'),
                        ['action' => 'delete', $supervisor->id],
                        ['confirm' => __('Are you sure you want to delete {0}?', $supervisor->nome), 'class' => 'button']
                    ) ?>
                </div>
            </aside>
            <?= $this->Form->create($supervisor) ?>
            <fieldset>
                <h3><?= __('Editando supervisor_') . $supervisor->id ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                       echo $this->Form->control('user_id', ['type' => 'number']); 
                    endif;
                    echo $this->Form->control('nome');
                    echo $this->Form->control('cpf');
                    echo $this->Form->control('endereco');
                    echo $this->Form->control('bairro');
                    echo $this->Form->control('municipio');
                    echo $this->Form->control('cep');
                    echo $this->Form->control('codigo_tel');
                    echo $this->Form->control('telefone');
                    echo $this->Form->control('codigo_cel');
                    echo $this->Form->control('celular');
                    echo $this->Form->control('email');
                    echo $this->Form->control('escola');
                    echo $this->Form->control('ano_formatura');
                    echo $this->Form->control('cress');
                    echo $this->Form->control('regiao');
                    echo $this->Form->control('outros_estudos');
                    echo $this->Form->control('area_curso');
                    echo $this->Form->control('ano_curso');
                    echo $this->Form->control('cargo');
                    echo $this->Form->control('num_inscricao');
                    echo $this->Form->control('curso_turma');
                    echo $this->Form->control('observacoes');
                    echo $this->Form->control('instituicoes._ids', ['options' => $instituicoes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
