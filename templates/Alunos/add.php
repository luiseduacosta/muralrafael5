<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Aluno $aluno
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="alunos form content">
    
            <?php if ($user_data['administrador_id']): ?>
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Alunos'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?php endif; ?>
            <?= $this->Form->create($aluno) ?>
            <fieldset>
                <h3><?= __('Adicionando Aluno()a') ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                        $val = $this->request->getParam('pass') ? $this->request->getParam('pass')[0] : '';
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $val ]); 
                    else:
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $user_session->get('id'), 'hidden' => true, 'label' => false ]); 
                    endif;
                    echo $this->Form->control('nome');
                    echo $this->Form->control('nomesocial');
                    echo $this->Form->control('ingresso', ['label' => 'Período de Ingresso (ex: 2022-1)', 'placeholder' => '2022-1']);
                    echo $this->Form->control('turno', ['options' => ['diurno' => 'Diurno', 'noturno' => 'Noturno', 'indefinido' => 'Indefinido']]);
                    echo $this->Form->control('registro', ['label' => 'Número de Registro - DRE']);
                    echo $this->Form->control('codigo_telefone', ['label' => 'Código do Telefone']);
                    echo $this->Form->control('telefone', ['placeholder' => '(xx) xxxxx-xxxx']);
                    echo $this->Form->control('codigo_celular', ['label' => 'Código do Celular']);
                    echo $this->Form->control('celular', ['placeholder' => '(xx) xxxxx-xxxx']);
                    echo $this->Form->control('email', ['type' => 'email', 'value' => $email, 'readonly' => true]);
                    echo $this->Form->control('cpf', ['placeholder' => 'xxx.xxx.xxx-xx']);
                    echo $this->Form->control('identidade', ['label' => 'Registro da Identidade']);
                    echo $this->Form->control('orgao', ['label' => 'Órgão Emissor da Identidade']);
                    echo $this->Form->control('nascimento', ['placeholder' => 'dd/mm/aaaa']);
                    echo $this->Form->control('cep', ['placeholder' => 'xxxxx-xxx']);
                    echo $this->Form->control('endereco', ['placeholder' => 'Rua, Avenida, etc.']);
                    echo $this->Form->control('municipio', ['placeholder' => 'Município']);
                    echo $this->Form->control('bairro', ['placeholder' => 'Bairro']);
                    echo $this->Form->control('observacoes');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
