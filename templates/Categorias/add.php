<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Categoria $categoria
 */
?>

<div class="container">
    
    <div class="categorias form content">
        <?= $this->Html->link(__('Listar Categorias'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2', 'style' => 'font-size: 10pt;']) ?>
        <div class="card">
            <div class="card-header">
                <h4><?= __('Nova Categoria') ?></h4>
            </div>
        <div class="card-body">
            <?= $this->Form->create($categoria) ?>
            <fieldset>
                <?php
                echo $this->Form->control('categoria', [
                    'label' => 'Categoria',
                    'class' => 'form-control',
                    'required' => true,
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Salvar'), ['class' => 'btn btn-success']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
