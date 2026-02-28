<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Administrador $nome
 */

declare(strict_types=1);


$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>
<div>
    <div class="column-responsive column-80">
        <div class="administradores form content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Listar Administradores'), ['action' => 'index'], ['class' => 'button']) ?>
                </div>
            </aside>
            <?= $this->Form->create($administrador) ?>
            <fieldset>
                <h3><?= __('Editando administrador_' . $administrador->id) ?></h3>
                <?php
                    if ($user_data['administrador_id']):
                       echo $this->Form->control('user_id', ['type' => 'number']); 
                    endif;
                    echo $this->Form->control('nome');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Editar'), ['class' => 'button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
