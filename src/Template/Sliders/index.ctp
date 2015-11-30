<?php if ($isAdmin): ?>
<?= $this->Html->script('jquery-2.1.4.min') ?>
<?= $this->Html->script('jquery.friendurl.min') ?>
<?= $this->Html->script('clients') ?>
<?php endif; ?>

<h3>Clients</h3>
<div>
    <?php if ($isAdmin): ?>
    <p>
        <i class="fa fa-plus-circle"></i> <a id="add-client">Add new client</a>
    </p>
    <div class="new-client slide-form">
        <?= $this->Form->create() ?>
        <?= $this->Form->input('name') ?>
        <?= $this->Form->input('urlName') ?>
        <?= $this->Form->select('private', [1 => 'Yes', 0 => 'No']) ?>
        <?= $this->Form->button('Add') ?>
        <?= $this->Form->end() ?>
    </div>
    <?php endif; ?>
    <?php foreach ($clients as $client): ?>
    <p>
        <a href="<?= $this->Url->build('/' . $client->urlName, true) ?>"><?= $client->name?></a> <?= $client->private ? ' <i class="fa fa-eye-slash"></i>' : '' ?>
    </p>
    <?php endforeach; ?>
</div>