<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Configuracao $configuracao
 */
?>
<div>
    <div class="column-responsive column-80">
        <div class="configuracoes view content">
            <aside>
                <div class="nav">
                    <?= $this->Html->link(__('Editar Configuracao'), ['action' => 'edit', $configuracao->id], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>Configurações</h3>
            <table>
                <tr>
                    <th><?= __('Instituição') ?></th>
                    <td><?= h($configuracao->instituicao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descrição') ?></th>
                    <td><?= h($configuracao->descricao) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mural Período Atual') ?></th>
                    <td><?= h($configuracao->mural_periodo_atual) ?></td>
                </tr>
                <tr>
                    <th><?= __('Curso Turma Atual') ?></th>
                    <td><?= h($configuracao->curso_turma_atual) ?></td>
                </tr>
                <tr>
                    <th><?= __('Curso Abertura Inscriçõees') ?></th>
                    <td><?= h($configuracao->curso_abertura_inscricoes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Curso Encerramento Inscrições') ?></th>
                    <td><?= h($configuracao->curso_encerramento_inscricoes) ?></td>
                </tr>
                <tr>
                    <th><?= __('Termo Compromisso Período') ?></th>
                    <td><?= h($configuracao->termo_compromisso_periodo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Termo Compromisso Início') ?></th>
                    <td><?= h($configuracao->termo_compromisso_inicio) ?></td>
                </tr>
                <tr>
                    <th><?= __('Termo Compromisso Final') ?></th>
                    <td><?= h($configuracao->termo_compromisso_final) ?></td>
                </tr>
                <tr>
                    <th><?= __('Período Calendário AcadÇemico') ?></th>
                    <td><?= h($configuracao->periodo_calendario_academico) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
