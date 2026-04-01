<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao $instituicao
 */
?>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<!-- Needs this style to show the icons -->
<style>
    .editor-toolbar button {
        color: #333 !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {
        $('#cnpj').mask('00.000.000/0000-00', {placeholder: '00.000.000/0000-00'});
        $('#cep').mask('00000-000', {placeholder: '00000-000'});
        const easyMDE = new EasyMDE({element: document.getElementById('observacoes')});
    });
</script>

<div>
    <div class="column-responsive column-80">
        <div class="instituicoes form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Instituições'), ['action' => 'index'], ['class' => 'button']) ?>
                    <?= $this->Form->postLink(
                        __('Excluir'),
                        ['action' => 'delete', $instituicao->id],
                        ['confirm' => __('Are you sure you want to delete {0}?', $instituicao->instituicao), 'class' => 'button'],
                    ) ?>
                </div>
            </aside>
            <?= $this->Form->create($instituicao) ?>
            <fieldset>
                <h3><?= __('Editando instituição_') . $instituicao->id ?></h3>
                <?php
                    echo $this->Form->control('instituicao');
                    echo $this->Form->control('area_id', ['label' => 'Área', 'options' => $areas, 'class' => 'form-control']);
                    echo $this->Form->control('natureza', ['required' => false]);
                    echo $this->Form->control('cnpj', ['label' => 'CNPJ', 'required' => false]);
                    echo $this->Form->control('email', ['label' => 'Email', 'required' => false]);
                    echo $this->Form->control('url', ['label' => 'Site da instituição', 'required' => false, 'placeholder' => 'http://www.site.com']);
                    echo $this->Form->control('endereco', ['label' => 'Endereço', 'required' => false]);
                    echo $this->Form->control('bairro', ['label' => 'Bairro', 'required' => false]);
                    echo $this->Form->control('municipio', ['label' => 'Município', 'required' => false]);
                    echo $this->Form->control('cep', ['label' => 'CEP', 'required' => false]);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'required' => false]);
                    echo $this->Form->control('beneficios', ['label' => 'Beneficios', 'required' => false]);
                    echo $this->Form->control('fim_de_semana', ['label' => 'Fim de semana', 'options' => ['1' => 'Sim', '0' => 'Nao', '2' => 'Parcial'], 'required' => false]);
                    echo $this->Form->control('local_inscricao', ['label' => 'Local de inscrição', 'options' => ['1' => 'Coordenação de Estágio/ESS/UFRJ', '0' => 'Instituicao']]);
                    echo $this->Form->control('convenio', ['label' => 'Nº do convênio na UFRJ', 'required' => false]);
                    echo $this->Form->control('expira', ['label' => 'Data de expiração', 'empty' => true, 'required' => false]);
                    echo $this->Form->control('seguro', ['options' => ['1' => 'Sim', '0' => 'Nao'], 'default' => '0', 'required' => false]);
                    echo $this->Form->control('avaliacao', ['options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], 'default' => '3']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações', 'required' => false]);
                    echo $this->Form->control('supervisores._ids', ['options' => $supervisores]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
