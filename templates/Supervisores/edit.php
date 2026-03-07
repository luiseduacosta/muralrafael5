<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supervisor $supervisor
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="supervisores form content">
            <aside>
                <div class="nav">   
                    <?= $this->Html->link(__('Listar Supervisores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']): ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $supervisor->id],
                            ['confirm' => __('Are you sure you want to delete {0}?', $supervisor->nome), 'class' => 'button']
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($supervisor) ?>
            <fieldset>
                <h3><?= __('Editando supervisor_') . $supervisor->id ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                       echo $this->Form->control('user_id', ['type' => 'number']); 
                    endif;
                    echo $this->Form->control('nome', ['required' => true]);
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}', 'placeholder' => '000.000.000-00', 'required' => false]);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'pattern' => '[0-9]{5}\-[0-9]{3}', 'placeholder' => '00000-000',  'required' => false]);
                    echo $this->Form->control('endereco', ['label' => 'Endereço', 'required' => false]);
                    echo $this->Form->control('bairro', ['label' => 'Bairro', 'required' => false]);
                    echo $this->Form->control('municipio', ['label' => 'Município', 'required' => false]);
                    echo $this->Form->control('codigo_tel', ['label' => 'DDD', 'required' => false]); // Changed from 'label' => 'DDD', 'required' => false
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'pattern' => '\([0-9]{2}\)[\s-][0-9]{4}\-[0-9]{4}', 'placeholder' => '(00) 0000-0000', 'required' => false]); // Changed from 'label' => 'Telefone', 'pattern' => '[0-9]{4}\-[0-9]{4}', 'placeholder' => '(00)0000-0000', 'required' => false
                    echo $this->Form->control('codigo_cel', ['label' => 'DDD', 'required' => false]); // Changed from 'label' => 'DDD', 'required' => false
                    echo $this->Form->control('celular', ['label' => 'Celular', 'pattern' => '\([0-9]{2}\)[\s-][0-9]{4,5}\-[0-9]{4}', 'placeholder' => '(00) 00000-0000', 'required' => false]); // Changed from 'label' => 'Celular', 'pattern' => '[0-9]{4}\-[0-9]{4}', 'placeholder' => '(00)0000-0000', 'required' => false
                    echo $this->Form->control('email', ['required' => false]);
                    echo $this->Form->control('escola', ['required' => false]);
                    echo $this->Form->control('ano_formatura', ['required' => false]);
                    echo $this->Form->control('cress', ['label' => 'CRESS', 'required' => true]);
                    echo $this->Form->control('regiao', ['required' => false]);
                    echo $this->Form->control('outros_estudos', ['required' => false]);
                    echo $this->Form->control('area_curso', ['required' => false]);
                    echo $this->Form->control('ano_curso', ['required' => false]);
                    echo $this->Form->control('cargo', ['required' => false]);
                    echo $this->Form->control('curso_turma', ['label' => 'Num da turma do curso de supervisores', 'required' => false]);
                    echo $this->Form->control('num_inscricao', ['label' => 'Num de inscricao', 'required' => false]);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                    echo $this->Form->control('instituicoes._ids', ['label' => 'Instituição', 'options' => $instituicoes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
