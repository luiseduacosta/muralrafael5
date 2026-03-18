<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Instituicao $instituicao
 */
?>

<?= $this->Html->script('jquery.mask.min'); ?>
<script>
    $(document).ready(function () {
        $('#cnpj').mask('00.000.000/0000-00', {placeholder: '00.000.000/0000-00'});
        $('#cep').mask('00000-000', {placeholder: '00000-000'});
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
                    echo $this->Form->control('natureza');
                    echo $this->Form->control('cnpj', ['label' => 'CNPJ']);
                    echo $this->Form->control('email', ['label' => 'Email']);
                    echo $this->Form->control('url', ['label' => 'Site da instituição', 'placeholder' => 'http://www.site.com']);
                    echo $this->Form->control('endereco', ['label' => 'Endereço']);
                    echo $this->Form->control('bairro', ['label' => 'Bairro']);
                    echo $this->Form->control('municipio', ['label' => 'Município']);
                    echo $this->Form->control('cep', ['label' => 'CEP']);
                    echo $this->Form->control('telefone', ['label' => 'Telefone', 'required' => false]);
                    echo $this->Form->control('beneficio', ['label' => 'Benefício', 'required' => false]);
                    echo $this->Form->control('fim_de_semana', ['label' => 'Fim de semana', 'options' => ['1' => 'Sim', '0' => 'Nao', '2' => 'Parcial'], 'required' => false]);
                    echo $this->Form->control('local_inscricao', ['label' => 'Local de inscrição', 'options' => ['1' => 'Coordenação de Estágio/ESS/UFRJ', '0' => 'Instituicao']]);
                    echo $this->Form->control('convenio', ['label' => 'Nº do convênio na UFRJ', 'required' => false]);
                    echo $this->Form->control('expira', ['label' => 'Data de expiração', 'empty' => true]);
                    echo $this->Form->control('seguro', ['options' => ['1' => 'Sim', '0' => 'Nao'], 'default' => '0']);
                    echo $this->Form->control('avaliacao', ['options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], 'default' => '3']);
                    echo $this->Form->control('observacoes', ['label' => 'Observações']);
                    echo $this->Form->control('supervisores._ids', ['options' => $supervisores]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
