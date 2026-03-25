<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Professor $professor
 */

declare(strict_types=1);

$user_data = ['administrador_id' => 0, 'aluno_id' => 0, 'professor_id' => 0, 'supervisor_id' => 0, 'categoria' => '0'];
$user_session = $this->request->getAttribute('identity');
if ($user_session) {
    $user_data = $user_session->getOriginalData();
}
?>
<?php
// May be this is a temporary solution. Put into de Configuracoes table in json data format is a better solution
$departamentos = [
    'Fundamentos' => 'Fundamentos',
    'Métodos e técnicas' => 'Metodologia',
    'Política social' => 'Politicas',
]
?>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {

        $('#cpf').mask('000.000.000-00');

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
                field.mask(telMaskBehavior.apply({}, arguments), options);
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
        <div class="professores form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Professores'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?php if ($user_data['administrador_id']) : ?>
                        <?= $this->Form->postLink(
                            __('Excluir'),
                            ['action' => 'delete', $professor->id],
                            ['confirm' => __('Are you sure you want to delete {0}?', $professor->nome), 'class' => 'button'],
                        ) ?>
                    <?php endif; ?>
                </div>
            </aside>
            <?= $this->Form->create($professor) ?>
            <fieldset>
                <h3><?= __('Editando Professor(a)') ?></h3>
                <?php
                if ($user_data['administrador_id']) :
                    echo $this->Form->control('user_id', ['type' => 'number', 'label' => false, 'hidden' => true]);
                endif;
                    echo $this->Form->control('nome', ['label' => 'Nome completo', 'required' => true]);
                    echo $this->Form->control('cpf', ['label' => 'CPF', 'pattern' => '[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}', 'placeholder' => '000.000.000-00', 'required' => false]);
                    echo $this->Form->control('cress', ['label' => 'CRESS', 'required' => false]);
                    echo $this->Form->control('regiao', ['label' => 'Regiao', 'required' => false]);
                if ($professor->siape) {
                    echo $this->Form->control('siape', ['value' => $professor->siape, 'required' => false, 'readonly' => true]);
                } else {
                    echo $this->Form->control('siape', ['required' => false]);
                }
                if ($professor->email) {
                    echo $this->Form->control('email', ['type' => 'email', 'value' => $professor->email, 'required' => false, 'readonly' => false]);
                } else {
                    echo $this->Form->control('email', ['type' => 'email', 'required' => false]);
                }
                    echo $this->Form->control('datanascimento', ['type' => 'date', 'empty' => true, 'label' => 'Data de nascimento', 'required' => false]);
                    echo $this->Form->control('localnascimento', ['label' => 'Local de nascimento', 'required' => false]);
                    echo $this->Form->control('ddd_telefone', ['label' => 'DDD telefone', 'required' => false]);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'pattern' => '\([0-9]{2}\)\s[0-9]{4}\.[0-9]{4}', 'width' => '15', 'required' => false]);
                    echo $this->Form->control('ddd_celular', ['label' => 'DDD celular', 'required' => false]);
                    echo $this->Form->control('celular', ['label' => 'Celular', 'pattern' => '\([0-9]{2}\)\s[0-9]{4,5}\.[0-9]{4}', 'width' => '15', 'required' => false]);
                    echo $this->Form->control('homepage', ['label' => 'Homepage', 'required' => false]);
                    echo $this->Form->control('redesocial', ['label' => 'Rede social', 'required' => false]);
                    echo $this->Form->control('curriculolattes', ['label' => 'Curriculo Lattes', 'required' => false]);
                    echo $this->Form->control('atualizacaolattes', ['type' => 'date', 'empty' => true, 'label' => 'Atualizacao Lattes', 'required' => false]);
                    echo $this->Form->control('curriculosigma', ['label' => 'Curriculo Sigma', 'required' => false]);
                    echo $this->Form->control('pesquisadordgp', ['label' => 'Diretorio de Grupos de Pesquisa', 'required' => false]);
                    echo $this->Form->control('formacaoprofissional', ['label' => 'Formacao Profissional', 'required' => false]);
                    echo $this->Form->control('universidadedegraduacao', ['label' => 'Universidade de graduacao', 'required' => false]);
                    echo $this->Form->control('anoformacao', ['label' => 'Ano de formacao', 'pattern' => '(19|20)[0-9]{2}', 'placeholder' => '0000', 'required' => false]);
                    echo $this->Form->control('mestradoarea', ['label' => 'Área de mestrado', 'required' => false]);
                    echo $this->Form->control('mestradouniversidade', ['label' => 'Universidade de mestrado', 'required' => false]);
                    echo $this->Form->control('mestradoanoconclusao', ['label' => 'Ano de conclusao de mestrado', 'pattern' => '(19|20)[0-9]{2}', 'placeholder' => '0000', 'required' => false]);
                    echo $this->Form->control('doutoradoarea', ['label' => 'Área de doutorado', 'required' => false]);
                    echo $this->Form->control('doutoradouniversidade', ['label' => 'Universidade de doutorado', 'required' => false]);
                    echo $this->Form->control('doutoradoanoconclusao', ['label' => 'Ano de conclusao de doutorado', 'pattern' => '(19|20)[0-9]{2}', 'placeholder' => '0000', 'required' => false]);
                    echo $this->Form->control('dataingresso', ['type' => 'date', 'empty' => true, 'label' => 'Data de ingresso', 'required' => false]);
                    echo $this->Form->control('formaingresso', ['label' => 'Forma de ingresso', 'required' => false]);
                    echo $this->Form->control('tipocargo', ['label' => 'Tipo de cargo', 'required' => false]);
                    echo $this->Form->control('regimetrabalho', ['label' => 'Regime de trabalho', 'required' => false]);
                    echo $this->Form->control('departamento', ['label' => 'Departamento', 'options' => $departamentos, 'empty' => true, 'required' => false]);
                    echo $this->Form->control('dataegresso', ['type' => 'date', 'empty' => true, 'label' => 'Data de egresso', 'required' => false]);
                    echo $this->Form->control('motivoegresso', ['label' => 'Motivo de egresso', 'required' => false]);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
