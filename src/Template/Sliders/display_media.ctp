<?php $this->Html->addCrumb($client->name, '/' . $client->urlName) ?>
<?php $this->Html->addCrumb($project->name, '/' . $client->urlName . '/' . $project->urlName) ?>
<?php $this->Html->addCrumb($item->name, '/' . $client->urlName . '/' . $project->urlName . '/' . $item->idItem) ?>

<div id="assets">
    <?php if (isset($date)) : ?>

    <?php elseif ($isAdmin): ?>

        <?= $this->Html->script('jquery-2.1.4.min') ?>
        <?= $this->Html->script('jquery-ui.min') ?>
        <?= $this->Html->script('drop') ?>

        <div id="new-asset">
                <div id="dropzone">
                    <p>Add new zip</p>
                </div>
                <div id="info">
                    <?= $this->Form->create() ?>
                    <?= $this->Form->input('zipfile', ['type' => 'hidden']) ?>
                    <?= $this->Form->input('name') ?>
                    <?= $this->Form->input('description') ?>
                    <?= $this->Form->input('height') ?>
                    <?= $this->Form->input('width') ?>
                    <?= $this->Form->button('Save') ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
    <?php endif; ?>
</div>
