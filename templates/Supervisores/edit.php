<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supervisor $supervisor
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
        <div class="supervisores form content">
            <aside>
                <div class="nav">   
                    <?= $this->Html->link(__('Listar Supervisores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $supervisor->id],
                            ['confirm' => __('Are you sure you want to delete {0}?', $supervisor->nome), 'class' => 'button'],
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($supervisor) ?>
            <fieldset>
                <h3><?= __('Editando supervisor_') . $supervisor->id ?></h3>
                <?php
                if ($user_data['administrador_id']) :
                    echo $this->Form->control('user_id', ['type' => 'number', 'hidden' => true]);
                endif;
                    echo $this->Form->control('nome', ['required' => true]);
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}', 'placeholder' => '000.000.000-00', 'required' => false]);
                    echo $this->Form->control('cress', ['label' => 'CRESS', 'required' => false]);
                    echo $this->Form->control('regiao', ['required' => false]);
                    echo $this->Form->control('email', ['required' => false]);
                    echo $this->Form->control('cargo', ['label' => 'Cargo na instituição', 'required' => false, 'default' => null]);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'pattern' => '[0-9]{5}-[0-9]{3}', 'placeholder' => '00000-000',  'required' => false]);
                    echo $this->Form->control('endereco', ['label' => 'Endereço', 'required' => false]);
                    echo $this->Form->control('bairro', ['label' => 'Bairro', 'required' => false]);
                    echo $this->Form->control('municipio', ['label' => 'Município', 'required' => false]);
                    echo $this->Form->control('codigo_tel', ['label' => 'DDD', 'required' => false]);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'pattern' => '\([0-9]{2}\)\s[0-9]{4}\.[0-9]{4}', 'placeholder' => '(00) 0000.0000', 'required' => false]);
                    echo $this->Form->control('codigo_cel', ['label' => 'DDD', 'required' => false]);
                    echo $this->Form->control('celular', ['label' => 'Celular', 'pattern' => '\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}', 'placeholder' => '(00) 00000.0000', 'required' => false]);
                    echo $this->Form->control('escola', ['label' => 'Instituição de Ensino', 'default' => null, 'required' => false]);
                    echo $this->Form->control('ano_formatura', ['label' => 'Ano de Formatura', 'pattern' => '(19|20)[0-9]{2}', 'placeholder' => '0000', 'required' => false, 'default' => null]);
                    echo $this->Form->control('outros_estudos', ['label' => 'Outros Estudos', 'required' => false, 'default' => null]);
                    echo $this->Form->control('area_curso', ['label' => 'Área de Curso', 'required' => false, 'default' => null]);
                    echo $this->Form->control('ano_curso', ['label' => 'Ano de Curso', 'pattern' => '(19|20)[0-9]{2}', 'placeholder' => '0000', 'required' => false, 'default' => null]);
                    echo $this->Form->control('curso_turma', ['label' => 'Turma do curso de supervisores', 'required' => false, 'default' => null, 'placeholder' => 'Turma']);
                    echo $this->Form->control('num_inscricao', ['label' => 'Número de Inscrição no curso de supervisores', 'required' => false, 'default' => null, 'placeholder' => '0000']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false, 'placeholder' => 'Observações']);
                    echo $this->Form->control('instituicoes._ids', ['label' => 'Instituição', 'options' => $instituicoes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
