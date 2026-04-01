<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Aluno $aluno
 */
declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {
        $('#cpf').mask('000.000.000-00');
        $('#cep').mask('00000-000');
        $('#ingresso').mask('0000-S', { translation: { 'S': { pattern: '[12]', optional: false } } }); // last digit is only 1 or 2
        if ($('#codigo-telefone').val() == null) {
            codigo = '21';
        } else {
            codigo = $('#codigo-telefone').val();
        }

        if ($('#telefone').val().length >= 8 && $('#telefone').val().length <= 10) {
            $('#telefone').val('(' + codigo + ') ' + $('#telefone').val());
        } 
        var telMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000.0000' : '(00) 0000.00009';
        };
        var telOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(mask.apply({}, arguments), options);
            },
            clearIfNotMatch: true
        };
        $('#telefone').mask(telMaskBehavior, telOptions);

        if ($('#celular').val().length >= 8 && $('#celular').val().length <= 10) {
            $('#celular').val('(' + codigo + ') ' + $('#celular').val());
        } 
        var celMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000.0000' : '(00) 0000.00009';
        };
        var celOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(celMaskBehavior.apply({}, arguments), options);
            },
            clearIfNotMatch: true
        };
        $('#celular').mask(celMaskBehavior, celOptions);
    });
</script>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<!-- Needs this style to show the icons -->
<style>
    .editor-toolbar button {
        color: #333 !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    $(document).ready(function () {
        const easyMDE = new EasyMDE({element: document.getElementById('observacoes')});
    });
</script>

<div>
    <div class="column-responsive column-80">
        <div class="alunos form content">
    
            <?php if ($user_data['administrador_id']) : ?>
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Alunos'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?php endif; ?>
            <?= $this->Form->create($aluno) ?>
            <fieldset>
                <h3><?= __('Adicionando Aluno(a)') ?></h3>
                <?php
                if ($user_data['administrador_id']) :
                    $val = $this->request->getParam('pass') ? $this->request->getParam('pass')[0] : '';
                    echo $this->Form->control('user_id', ['type' => 'number', 'value' => $val, 'hidden' => true, 'label' => false ]);
                elseif ($user_data['aluno_id']) :
                        echo $this->Form->control('user_id', ['type' => 'number', 'value' => $user_session->get('id'), 'hidden' => true, 'label' => false ]);
                endif;
                    echo $this->Form->control('nome', ['label' => 'Nome Completo', 'required' => true]);
                    echo $this->Form->control('nomesocial', ['label' => 'Nome Social', 'required' => false]);
                if ($aluno->registro) {
                    echo $this->Form->control('registro', ['label' => 'Número de Registro - DRE', 'value' => $aluno->registro, 'required' => true, 'readonly' => true]);
                } else {
                    echo $this->Form->control('registro', ['label' => 'Número de Registro - DRE', 'required' => true]);
                }
                    echo $this->Form->control('codigo_telefone', ['label' => 'Código do Telefone', 'required' => false]);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'pattern' => '\([0-9]{2}\)[\s][0-9]{4,5}\.[0-9]{4}', 'placeholder' => '(00) 0000.0000', 'data-mask' => '(00) 0000.0000', 'required' => false]);
                    echo $this->Form->control('codigo_celular', ['label' => 'Código do Celular', 'required' => false]);
                    echo $this->Form->control('celular', ['label' => 'Celular', 'pattern' => '\([0-9]{2}\)[\s][0-9]{4,5}\.[0-9]{4}', 'placeholder' => '(00) 00000.0000', 'data-mask' => '(00) 00000.0000', 'required' => false]);
                if ($aluno->email) {
                    echo $this->Form->control('email', ['type' => 'email', 'value' => $aluno->email, 'required' => true, 'readonly' => true]);
                } else {
                    echo $this->Form->control('email', ['type' => 'email', 'required' => true]);
                }
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}', 'placeholder' => '000.000.000-00', 'data-mask' => '000.000.000-00', 'required' => true]);
                    echo $this->Form->control('identidade', ['label' => 'Registro da Identidade', 'required' => false]);
                    echo $this->Form->control('orgao', ['label' => 'Órgão Emissor da Identidade', 'required' => false]);
                    echo $this->Form->control('nascimento', ['label' => 'Data de Nascimento', 'placeholder' => 'dd-mm-aaaa', 'required' => true]);
                    echo $this->Form->control('ingresso', ['label' => 'Período de Ingresso', 'pattern' => '(19|20)[0-9]{2}-[1-2]', 'placeholder' => '0000-9', 'required' => true]);
                    echo $this->Form->control('turno_id', ['options' => $turnos, 'empty' => true, 'required' => true]);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'pattern' => '[0-9]{5}-[0-9]{3}', 'placeholder' => '00000-000', 'required' => true]);
                    echo $this->Form->control('endereco', ['label' => 'Endereço', 'placeholder' => 'Rua, Avenida, etc.', 'required' => false]);
                    echo $this->Form->control('municipio', ['label' => 'Município', 'placeholder' => 'Município', 'required' => false]);
                    echo $this->Form->control('bairro', ['label' => 'Bairro', 'placeholder' => 'Bairro', 'required' => false]);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Adicionar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
