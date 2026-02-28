<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Area $area
 */
?>
<div class="areas view content">
    <aside>
        <div class="nav">
            <?= $this->Html->link(__('Listar Areas'), ['action' => 'index'], ['class' => 'button']) ?>
            <?= $this->Html->link(__('Editar Area'), ['action' => 'edit', $area->id], ['class' => 'button']) ?>
            <?= $this->Form->postLink(__('Deletar Area'), ['action' => 'delete', $area->id], ['confirm' => __('Are you sure you want to delete {0}?', $area->area), 'class' => 'button']) ?>
            <?= $this->Html->link(__('Nova Area'), ['action' => 'add'], ['class' => 'button']) ?>
        </div>
    </aside>
    <h3>area_<?= h($area->id) ?></h3>
    <table>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= h($area->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Area') ?></th>
            <td><?= h($area->area) ?></td>
        </tr>
    </table>
    
    <?php if (!empty($area->instituicoes)) : ?>
    <div class="related">
        <h4><?= __('Related Instituições') ?></h4>
        <div class="table_wrap">
            <table>
                <tr>
                    <th class="actions"><?= __('Actions') ?></th>
                    <th><?= __('Id') ?></th>
                    <th><?= __('Instituicao') ?></th>
                    <th><?= __('Natureza') ?></th>
                    <th><?= __('CNPJ') ?></th>
                    <th><?= __('Email') ?></th>
                    <th><?= __('URL') ?></th>
                    <th><?= __('Avaliação') ?></th>
                </tr>
                <?php foreach ($area->instituicoes as $instituicao) : ?>
                <tr>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $instituicao->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $instituicao->id]) ?>
                        <?= $this->Form->postLink(__('Deletar'), ['action' => 'delete', $instituicao->id], ['confirm' => __('Are you sure you want to delete # {0}?', $instituicao->id)]) ?>
                    </td>
                    <td><?= h($instituicao->id) ?></td>
                    <td><?= $this->Html->link($instituicao->instituicao, ['controller' => 'instituicoes', 'action' => 'view', $instituicao->id]) ?></td>
                    <td><?= h($instituicao->natureza) ?></td>
                    <td><?= h($instituicao->cnpj) ?></td>
                    <td><?= $instituicao->email ? $this->Text->autoLinkEmails($instituicao->email) : '' ?></td>
                    <td><?= $instituicao->url ? $this->Html->link($instituicao->url) : '' ?></td>
                    <td><?= h($instituicao->avaliacao) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
</div>
