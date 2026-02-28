<?php
declare(strict_types=1);

$user_data = ['administrador_id'=>0,'aluno_id'=>0,'professor_id'=>0,'supervisor_id'=>0];
$user_session = $this->request->getAttribute('identity');
if ($user_session) { $user_data = $user_session->getOriginalData(); }
?>

<h1>Folha de atividades</h1>

<?php
echo $this->Form->create(null, ['class' => 'form-inline', 'type' => 'GET']);
echo $this->Form->control('registro', ['type' => 'text', 'label' => 'DRE', 'value' => $this->getRequest()->getQuery('registro'), 'class' => 'form-control required']);
echo $this->Form->submit('Confirma', ['type' => 'Submit', 'label' => ['text' => 'Confirma'], 'class' => 'button']);
echo $this->Form->end();
?>

