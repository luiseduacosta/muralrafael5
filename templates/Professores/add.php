<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor $professor
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<?php
// May be this is a temporary solution. Put into de Configuracoes table in json data format is a better solution
$departamentos = [
    'Fundamentos' => 'Fundamentos',
    'Métodos e técnicas' => 'Metodologia',
    'Política social' => 'Politicas'
]
?>
<div>
    <div class="column-responsive column-80">
        <div class="professores form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Professores'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($professor) ?>
            <fieldset>
                <h3><?= __('Adicionando Professor') ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                        $val = $this->request->getParam('pass') ? $this->request->getParam('pass')[0] : '';
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $val ]); 
                    else:
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $user_session->get('id'), 'hidden' => true ]); 
                    endif;
                    echo $this->Form->control('nome', ['label' => 'Nome Completo', 'required' => true]);
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}', 'placeholder' => '000.000.000-00', 'required' => false]);
                    if ($professor->siape) {
                        echo $this->Form->control('siape', ['value' => $professor->siape, 'required' => true, 'readonly' => true]);
                    } else {
                        echo $this->Form->control('siape', ['required' => true]);
                    }
                    echo $this->Form->control('datanascimento', ['empty' => true, 'required' => false]);
                    echo $this->Form->control('localnascimento', ['label' => 'Local Nascimento', 'required' => false]);
                    echo $this->Form->control('ddd_telefone', ['label' => 'DDD Telefone', 'required' => false]);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'pattern' => '\([0-9]{2}\)\s[0-9]{4}\.[0-9]{4}', 'placeholder' => '(00) 0000.0000', 'required' => false]);
                    echo $this->Form->control('ddd_celular', ['label' => 'DDD Celular', 'required' => false]);
                    echo $this->Form->control('celular', ['label' => 'Celular', 'pattern' => '\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}', 'placeholder' => '(00) 00000.0000', 'required' => false]);
                    if ($professor->email) {
                        echo $this->Form->control('email', ['type' => 'email', 'value' => $professor->email, 'required' => true, 'readonly' => true]);
                    } else {
                        echo $this->Form->control('email', ['type' => 'email', 'required' => true]);
                    }
                    echo $this->Form->control('homepage', ['label' => 'Homepage', 'required' => false]);
                    echo $this->Form->control('redesocial', ['label' => 'Rede Social', 'required' => false]);
                    echo $this->Form->control('curriculolattes', ['label' => 'Curriculo Lattes', 'required' => false]);
                    echo $this->Form->control('atualizacaolattes', ['empty' => true, 'required' => false]);
                    echo $this->Form->control('curriculosigma', ['label' => 'Curriculo Sigma', 'required' => false]);
                    echo $this->Form->control('pesquisadordgp', ['label' => 'Pesquisa Dord GP', 'required' => false]);
                    echo $this->Form->control('formacaoprofissional', ['label' => 'Formacao Profissional', 'required' => false]);
                    echo $this->Form->control('universidadedegraduacao', ['label' => 'Universidade de Graduacao', 'required' => false]);
                    echo $this->Form->control('anoformacao', ['label' => 'Ano de Formacao', 'required' => false]);
                    echo $this->Form->control('mestradoarea', ['label' => 'Mestrado Area', 'required' => false]);
                    echo $this->Form->control('mestradouniversidade', ['label' => 'Mestrado Universidade', 'required' => false]);
                    echo $this->Form->control('mestradoanoconclusao', ['label' => 'Mestrado Ano Conclussão', 'required' => false]);
                    echo $this->Form->control('doutoradoarea', ['label' => 'Doutorado Area', 'required' => false]);
                    echo $this->Form->control('doutoradouniversidade', ['label' => 'Doutorado Universidade', 'required' => false]);
                    echo $this->Form->control('doutoradoanoconclusao', ['label' => 'Doutorado Ano Conclussão', 'required' => false]);
                    echo $this->Form->control('dataingresso', ['empty' => true, 'required' => false]);
                    echo $this->Form->control('formaingresso', ['label' => 'Forma Ingresso', 'required' => false]);
                    echo $this->Form->control('tipocargo', ['label' => 'Tipo Cargo', 'required' => false]);
                    echo $this->Form->control('regimetrabalho', ['label' => 'Regime Trabalho', 'required' => false]);
                    echo $this->Form->control('departamento', ['label' => 'Departamento', 'options' => $departamentos, 'required' => true]);
                    echo $this->Form->control('dataegresso', ['empty' => true, 'required' => false]);
                    echo $this->Form->control('motivoegresso', ['label' => 'Motivo Egresso', 'required' => false]);
                    echo $this->Form->control('observacoes', ['label' => 'Observacoes', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
