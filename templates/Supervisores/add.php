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
                    <?= $this->Html->link(__('Listar Supervisores(as)'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($supervisor) ?>
            <fieldset>
                <h3><?= __('Adicionando Supervisor(a)') ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                        $val = $this->request->getParam('pass') ? $this->request->getParam('pass')[0] : '';
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $val ]); 
                    else:
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $user_session->get('id'), 'hidden' => true ]); 
                    endif;
                    echo $this->Form->control('nome', ['required' => true]);
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}', 'placeholder' => '000.000.000-00', 'required' => false]);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'pattern' => '[0-9]{5}\-[0-9]{3}', 'placeholder' => '00000-000',  'required' => false]);
                    echo $this->Form->control('endereco', ['required' => false]);
                    echo $this->Form->control('bairro', ['required' => false]);
                    echo $this->Form->control('municipio', ['required' => false]);
                    echo $this->Form->control('codigo_tel', ['label' => 'DDD', 'required' => false]);
                    echo $this->Form->control('telefone', ['placeholder' => '(00)0000-0000', 'label' => 'Telefone', 'required' => false]);
                    echo $this->Form->control('codigo_cel', ['label' => 'DDD', 'required' => false]);
                    echo $this->Form->control('celular', ['placeholder' => '(00)0000-0000', 'label' => 'Celular', 'required' => false]);
                    if ($supervisor->email) {
                        echo $this->Form->control('email', ['placeholder' => 'email@example.com', 'value' => $supervisor->email, 'readonly' => true, 'required' => true]);
                    } else {
                        echo $this->Form->control('email', ['placeholder' => 'email@example.com', 'required' => true]);
                    }
                    echo $this->Form->control('escola', ['label' => 'Instituição de Ensino', 'default' => null, 'required' => false]);
                    echo $this->Form->control('ano_formatura', ['required' => false, 'default' => null]);
                    if ($supervisor->cress) {
                        echo $this->Form->control('cress', ['label' => 'CRESS', 'value' => $supervisor->cress, 'readonly' => true, 'required' => true]);
                    } else {
                        echo $this->Form->control('cress', ['label' => 'CRESS', 'required' => true]);
                    }
                    echo $this->Form->control('regiao', ['label' => 'Região', 'default' => '7', 'required' => false]);
                    echo $this->Form->control('outros_estudos', ['required' => false, 'default' => null]);
                    echo $this->Form->control('area_curso', ['required' => false, 'default' => null]);
                    echo $this->Form->control('ano_curso', ['required' => false, 'default' => null]);
                    echo $this->Form->control('cargo', ['label' => 'Cargo na instituição', 'required' => false, 'default' => null]);
                    echo $this->Form->control('num_inscricao', ['label' => 'Número de Inscrição no curso de supervisores', 'required' => false, 'default' => null]);
                    echo $this->Form->control('curso_turma', ['label' => 'Turma de Curso de Supervisores', 'required' => false, 'default' => null]);
                    echo $this->Form->control('observacoes', ['required' => false, 'default' => null]);
                    echo $this->Form->control('instituicoes._ids', ['options' => $instituicoes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
