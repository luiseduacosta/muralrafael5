<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = $configuracao['descricao'] . ' - ' . $configuracao['instituicao'];

?>

<!DOCTYPE html>
<!-- templates/layout/default.php -->
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?= $cakeDescription ?>:
            <?= $this->fetch('title') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        
        <?= $this->Html->css(['normalize.min', 'fonts', 'milligram.min', 'cake', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', 'nav', 'mural']) ?>
        <?= $this->Html->script(['https://code.jquery.com/jquery-3.7.0.min.js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bundle.min.js']) ?>
        
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

        <?= $this->fetch('script') ?>
    </head>
    <body>
        <?= $this->element('submenu_navegacao'); ?>
        <div id="content">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
        <?= $this->element('footer'); ?>
    </body>
</html>
