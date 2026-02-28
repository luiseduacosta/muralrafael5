<?php
// pr('alunonovoturno ' . $alunonovoturno);
// pr('Super ' . $supervisor_atual);
// pr($ingresso);
// die();
?>

<!--?= $this->Html->script("jquery.maskedinput"); ?-->

<script>
    //$(document).ready(function () {
    //    $("#EstagiarioIngresso").mask("9999-9");
    //});

    $(document).ready(function () {

        var base_url = "<?= $this->Html->Url->build(array('controller' => 'Instituicaos', 'action' => 'seleciona_supervisor')); ?>";
        /* alert(base_url); */

        $("#EstagiarioIdInstituicao").change(function () {
            var id_instituicao = $(this).val();
            $("#EstagiarioIdSupervisor").load(base_url + "/" + id_instituicao);
            /* alert(id_instituicao); */
        })
    });

</script>

<div class='justify-content-center'>
<?php
if ($inserir == 0) {
    echo "<h1>Inserir Termo de Compromisso</h1>";
} elseif ($inserir == 1) {
    echo "<h1>Atualizar Termo de Compromisso</h1>";
}
?>
</div>

<div class='table-responsive'>
    
    <h2>Período <?= $periodo ?></h2>

    <table class='table table-hover table-striped table-responsive'>
        <tbody>
            <tr>
                <td>Registro:</td>
                <td>
                    <?= $registro ?>
                </td>
                <td>Estudante:</td>
                <td>
                    <?= $aluno ?>
                </td>
            </tr>
            <tr>
                <td>Nível de estágio</td>
                <td>
                    <?= $estagio_nivel = ($nivel == 9) ? "Não obrigatório " : $nivel ?>
                </td>
                <td>Período</td>
                <td>
                    <?= $periodo ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
echo $this->Form->create(null, [
    'url' => 'termocadastra?registro=' . $registro,
    'inputDefaults' => [
       // 'format' => ['before', 'label', 'between', 'input', 'after', 'error'],
       // 'div' => ['class' => 'form-group row'],
        'label' => true,//['class' => 'col-3'],
        'between' => "<div class = 'col-9'>",
       // 'class' => ['form-control'],
        'after' => "</div>"
    ]
]);

echo $this->Form->input('id', array('type' => 'hidden', 'value' => $estagiario_id));
echo $this->Form->input('inserir', array('type' => 'hidden', 'value' => $inserir));
echo $this->Form->input('aluno_id', array('type' => 'hidden', 'value' => $aluno_id));
echo $this->Form->input('registro', array('type' => 'hidden', 'value' => $registro));
echo $this->Form->input('turno', array('type' => 'hidden', 'value' => $turno));
echo $this->Form->input('nivel', array('type' => 'hidden', 'value' => $nivel));
echo $this->Form->input('tc', array('type' => 'hidden', 'value' => 1));
echo $this->Form->input('tc_solicitacao', array('type' => 'hidden', 'value' => date('Y-m-d')));
echo $this->Form->input('professor_id', array('type' => 'hidden', 'label' => 'Professor', 'value' => $professor_atual));
echo $this->Form->input('periodo', array('type' => 'hidden', 'value' => $periodo));
// echo $this->Form->input('id_area', array('type' => 'hidden', 'value' => $id_area));
echo $this->Form->input('complemento_id', array('type' => 'hidden', 'value' => $complemento_id));

if (strlen($ingresso) == 6) {
    echo $this->Form->input('ingresso', ['type' => 'hidden', 'label' => ['text' => 'Ano e semestre de ingresso na ESS'], 'value' => $ingresso, 'required', 'class' => 'form-control']);
} else {
    echo $this->Form->label('ingresso', 'Ano e semestre de ingresso na ESS');
    echo $this->Form->input('ingresso', ['type' => 'text', 'value' => $ingresso, 'required', 'class' => 'form-control']);
}

if (isset($alunoturno)) {
    echo $this->Form->input('alunoturno', ['type' => 'hidden', 'empty' => 'Seleciona', 'value' => $alunoturno, 'required', 'class' => 'form-control']);
} else {
    echo $this->Form->label('alunoturno', 'Turno');
    echo $this->Form->input('alunoturno', ['type' => 'select', 'options' => ['diurno' => 'Diurno', 'noturno' => 'Noturno'], 'empty' => 'Seleciona', 'required', 'class' => 'form-control']);
}

echo $this->Form->label('ajuste2020', 'Ajuste curricular 2020');
echo $this->Form->input('ajuste2020', ['type' => 'select', 'options' => ['0' => 'Não', '1' => 'Sim'], 'value' => $ajuste2020, 'empty' => 'Seleciona', 'required']);
echo '<p>Para ingressantes a partir de 2020 são 3 níves de estágio</p>';

echo $this->Form->label('tipo_de_estagio', 'Seleciona tipo de estágio');
echo $this->Form->input('tipo_de_estagio', ['type' => 'select', 'options' => [1 => 'Presencial', 2 => 'Remoto'], 'default' => '1', 'class' => 'form-control']);
?>

<fieldset class="border p-1">
    <legend class="w-auto">Benefícios</legend>
    <?php
    echo $this->Form->label('benetransporte', 'A instituição oferece vale transporte?');
    echo $this->Form->input('benetransporte', ['type' => 'select', 'options' => [1 => 'Sim', 0 => 'Não'], 'default' => '0', 'class' => 'form-control']);
    
    echo $this->Form->label('benealimentacao', 'A instituição oferece alimentação?');
    echo $this->Form->input('benealimentacao', ['type' => 'select', 'options' => [1 => 'Sim', 0 => 'Não'], 'default' => '0', 'class' => 'form-control']);

    echo $this->Form->label('benebolsa', 'Se a instituição oferece bolsa indique o valor em números inteiros, caso contrário digite o número 0');
    echo $this->Form->input('benebolsa', ['type' => 'text', 'class' => 'form-control', 'required']);
    ?>
</fieldset>

<fieldset class="border p-2">
    <?php
    echo $this->Form->label('id_instituicao', 'Instituição');
    echo $this->Form->input('id_instituicao', ['type' => 'select', 'options' => $instituicoes, 'empty' => ['0' => 'Selecione'], 'value' => $instituicao_atual, 'required', 'class' => 'form-control']);
    echo '<p>É obrigatório selecionar uma instituição.</p>';

    echo $this->Form->label('id_supervisor', 'Supervisor');
    echo $this->Form->input('id_supervisor', ['type' => 'select', 'label' => ['text' => 'Supervisor'], 'options' => $supervisores, 'value' => $supervisor_atual, 'empty' => ['0' => 'Selecione'], 'class' => 'form-control']);
    echo '<p>Se não souber quem é o(a) supervisor(a) pode deixar em branco</p>';
    ?>
</fieldset>

<div class='row justify-content-left'>
    <div class='col-auto'>
        <?php echo $this->Form->submit('Confirma', ['type' => 'Submit', 'class' => 'btn btn-primary']);
        ?>
        <?php echo $this->Form->end(); ?>
    </div>
</div>