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
                    <?= $this->Html->link(__('Editar Configuração'), ['action' => 'edit', $configuracao->id], ['class' => 'button']) ?>
                </div>
            </aside>
            <h3>Configurações</h3>
            <dl>
                <div class="row">
                    <dt class="col-3"><?= __('Instituição - Curso') ?></dt>
                    <dd class="col-9"><?= h($configuracao->instituicao_curso) ?></dd>
                    <dt class="col-3"><?= __('Período Atual Mural') ?></dt>
                    <dd class="col-9"><?= h($configuracao->mural_periodo_atual) ?></dd>
                    <dt class="col-3"><?= __('Período Termo Compromisso') ?></dt>
                    <dd class="col-9"><?= h($configuracao->termo_compromisso_periodo) ?></dd>
                    <dt class="col-3"><?= __('Inicio Termo Compromisso') ?></dt>
                    <dd class="col-9"><?= $configuracao->termo_compromisso_inicio?->format('d-m-Y') ?></dd>
                    <dt class="col-3"><?= __('Final Termo Compromisso') ?></dt>
                    <dd class="col-9"><?= $configuracao->termo_compromisso_final?->format('d-m-Y') ?></dd>
                    <dt class="col-3"><?= __('Período Calendário Acadêmico') ?></dt>
                    <dd class="col-9"><?= h($configuracao->periodo_calendario_academico) ?></dd>
                    <dt class="col-3"><?= __('Curso Turma Atual') ?></dt>
                    <dd class="col-9"><?= h($configuracao->curso_turma_atual) ?></dd>
                    <dt class="col-3"><?= __('Curso Abertura Inscriçõees') ?></dt>
                    <dd class="col-9"><?= $configuracao->curso_abertura_inscricoes?->format('d-m-Y') ?></dd>
                    <dt class="col-3"><?= __('Curso Encerramento Inscrições') ?></dt>
                    <dd class="col-9"><?= $configuracao->curso_encerramento_inscricoes?->format('d-m-Y') ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>
