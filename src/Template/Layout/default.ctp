<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'DropApp';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->css('font-awesome.min.css') ?>
         
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <header>
        <div>
            <div class="left"><?= $this->Html->image('brigade.png') ?></div>
            <div class="left"><?= $this->Html->getCrumbs(' > ', 'Clients') ?></div>
            <div class="right">
                <?php if ($userData): ?>
                <?= $isAdmin ? $this->Html->link('Panel', ['controller' => 'Users']) : '' ?>
                <i>|</i>
                <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout']) ?>
                <?php else: ?>
                <?= $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login']) ?>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    <footer>
    </footer>
</body>
</html>
