<?php
// pr($aluno);
// pr($totalperiodos);
// echo $aluno->nome;
?>

<div class="content">
    <?= $this->Form->create($aluno) ?>
    <fieldset>
        <h3><?= __('Declaração de ' . $totalperiodos . 'º' . ' período do(a) aluno') ?></h3>
        <?php
        if ($aluno->periodonovo):
            echo $this->Form->control('novoperiodo', ['label' => ['text' => 'Período de ingresso'], 'value' => $aluno->periodonovo]);
        else:
            echo $this->Form->control('novoperiodo', ['label' => ['text' => 'Período de ingresso'], 'value' => $aluno->ingresso]);
        endif;
        echo $this->Form->control('nome', ['readonly']);
        echo $this->Form->control('nomesocial', ['label' => ['text' => 'Nome social']]);
        echo $this->Form->control('registro', ['readonly']);
        echo $this->Form->control('ingresso', ['readonly']);
        echo $this->Form->control('turno', ['options' => ['diurno' => 'Diurno', 'noturno' => 'Noturno']]);
        echo $this->Form->control('codigo_telefone', ['label' => ['text' => 'DDD']]);
        echo $this->Form->control('telefone');
        echo $this->Form->control('codigo_celular', ['label' => ['text' => 'DDD']]);
        echo $this->Form->control('celular');
        echo $this->Form->control('email');
        echo $this->Form->control('cpf', ['label' => ['text' => 'CPF']]);
        echo $this->Form->control('identidade', ['label' => ['text' => 'Carteira de identidade']]);
        echo $this->Form->control('orgao', ['label' => ['text' => 'Orgão emissor']]);
        echo $this->Form->control('nascimento', ['empty' => true]);
        echo $this->Form->control('endereco', ['label' => ['text' => 'Endereço']]);
        echo $this->Form->control('cep', ['label' => ['text' => 'CEP']]);
        echo $this->Form->control('municipio');
        echo $this->Form->control('bairro');
        echo $this->Form->control('observacoes', ['label' => ['text' => 'Observações']]);
        ?>
    </fieldset>
    <div class="d-flex justify-content-center">
        <div class="btn-group" role="group" aria-label="Confirma">
            <?= $this->Html->link('Imprime PDF', ['action' => 'declaracaoperiodopdf', '?' => ['id' => $aluno->id, 'totalperiodos' => $totalperiodos]], ['class' => 'button btn-info']); ?>
            <?= $this->Form->button(__('Confirmar alteraçoes'), ['type' => 'submit', 'class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>