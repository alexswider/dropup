<?= $this->Html->script('jquery-2.1.4.min') ?>
<?= $this->Html->script('users') ?>

<div>
    <h5>Admins accounts:</h5>
    <ul>
        <?php foreach ($admins as $user): ?>
        <li><?= $user->username ?> [id: <?= $user->id ?>]</li>
        <?php endforeach; ?>
        <li> <a id="add-admin">Add admin</a></li>
    </ul>
    <div class="new-user" id="admin">
        <?= $this->Form->create(null, ['action' => 'addAdmin']) ?>
        <?= $this->Form->input('username') ?>
        <?= $this->Form->input('password') ?>
        <?= $this->Form->button('Add') ?>
        <?= $this->Form->end() ?>
    </div>
    <h5>Clients accounts:</h5>
    <ul>
        <?php foreach ($clients as $user): ?>
        <li><?= $user->username ?> [id: <?= $user->id ?>]</li>
        <?php endforeach; ?>
        <li> <a id="add-client">Add client</a></li>
    </ul>
    <div class="new-user" id="client">
        <?= $this->Form->create(null, ['action' => 'addClient']) ?>
        <?= $this->Form->input('username') ?>
        <?= $this->Form->input('password') ?>
        <?= $this->Form->select('idClient', $clientsName) ?>
        <?= $this->Form->button('Add') ?>
        <?= $this->Form->end() ?>
    </div>
</div>