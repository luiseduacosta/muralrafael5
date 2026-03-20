<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Estagiario $estagiario
 */
?>

<script type="text/javascript">

    $(document).ready(function () {

        var url = "<?= $this->Html->Url->build(['controller' => 'estagiarios', 'action' => 'termodecompromisso', '?' => ['aluno_id' => $aluno_id]]); ?>";
        $("#instituicao-id").change(function () {
            var instituicao = $(this).val();
            // alert(url + '&instituicao_id=' +instituicao);
            window.location = url + '&instituicao_id=' + instituicao;
        })
    })
</script>

<?php
$submit = [
    'button' => "<div class='d-flex justify-content-center'><button type ='submit' class= 'btn btn-danger' {{attrs}}>{{text}}</button></div>",
]
?>

<div class="container">
            <?= $this->Form->create(null, ['type' => 'post']) ?>
                <legend><?= __('Solicitação de termo de compromisso') ?></legend>
                <?php
                if (isset($atualiza) && $atualiza == 1) {
                    echo $this->Form->control('id', ['value' => $estagio->id, 'type' => 'hidden', 'readonly']);
                }
                ?>
                <fieldset>
                    <?php echo $this->Form->control('registro', ['value' => $estagio->aluno->registro, 'readonly']); ?>
                    <?php echo $this->Form->control('aluno_id', ['label' => ['text' => 'Aluno'], 'options' => [$estagio->aluno->id => $estagio->aluno->nome], 'empty' => false, 'readonly']); ?>
                    <?php echo $this->Form->control('ingresso', ['label' => ['text' => 'Ingresso'], 'value' => $estagio->aluno->ingresso, 'readonly']); ?>
                    <?php echo $this->Form->control('nivel', ['options' => ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '9' => 'Extra curricular'], 'value' => $estagio->aluno->nivel, 'readonly']); ?>
                    <?php echo $this->Form->control('periodo', ['label' => ['text' => 'Período'], 'value' => $estagio->aluno->periodo, 'readonly']); ?>
                    <?php echo $this->Form->control('nivel', ['options' => ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '9' => 'Extra curricular'], 'value' => $estagio->aluno->nivel, 'readonly']); ?>
                    <?php echo $this->Form->control('periodo', ['label' => ['text' => 'Período'], 'value' => $estagio->aluno->periodo, 'readonly']); ?>
                </fieldset>

            <?php
            if (isset($instituicao_id)) {
                echo $this->Form->control('instituicao_id', ['label' => ['text' => 'Instituição1'], 'options' => $instituicoes, 'value' => $instituicao_id, 'required']);
            } elseif (isset($estagio) && $estagio->hasValue('instituicao')) {
                echo $this->Form->control('instituicao_id', ['label' => ['text' => 'Instituição2'], 'options' => $instituicoes, 'empty' => [$estagio->instituicao->id => $estagio->instituicao->instituicao], 'required']);
            } else {
                echo $this->Form->control('instituicao_id', ['label' => ['text' => 'Instituição3'], 'options' => $instituicoes, 'empty' => ['Seleciona instituição de estágio'], 'required']);
            }

            if (isset($estagio) && $estagio->hasValue('supervisor')) :
                if (isset($supervisoresdainstituicao) && ($supervisoresdainstituicao)) {
                    echo $this->Form->control('supervisor_id', ['label' => ['text' => 'Supervisor(a)1'], 'options' => $supervisoresdainstituicao, 'value' => $estagio->supervisor->id]);
                } else {
                    echo $this->Form->control('supervisor_id', ['label' => ['text' => 'Supervisor(a)2'], 'options' => ['Sem informação'], 'value' => $estagio->supervisor->id]);
                }
            else :
                if (isset($supervisoresdainstituicao)) :
                    echo $this->Form->control('supervisor_id', ['label' => ['text' => 'Supervisor(a)3'], 'options' => $supervisoresdainstituicao, 'empty' => 'Selecione supervisor(a)']);
                else :
                        echo $this->Form->control('supervisor_id', ['label' => ['text' => 'Supervisor(a)4'], 'options' => ['Sem informação'], 'empty' => ['0' => 'Sem informação']]);
                endif;
            endif;
            ?>

            <div class="d-flex justify-content-center">
                <div class="btn-group" role="group" aria-label="Confirma">
                    <?php if (isset($estagio) && $estagio->id) : ?>
                        <?= $this->Html->link('Imprime PDF', ['action' => 'termodecompromissopdf', $estagio->id], ['class' => 'btn btn-lg btn-primary', 'rule' => 'button', 'style' => 'width: 200px']); ?>
                    <?php endif; ?>
                    <?php $this->Form->setTemplates($submit); ?>
                    <?= $this->Form->button(__('Confirmar alteraçoes antes de imprimir'), ['type' => 'submit', 'class' => 'btn btn-lg btn-danger btn-xs col-lg-3', 'style' => 'max-width:200px; word-wrap:break-word;']) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
</div>