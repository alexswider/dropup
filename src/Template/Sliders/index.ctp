<h3>Clients</h3>
<div>
    <?php foreach ($clients as $client): ?>
    <p>
        <a href="<?= $this->Url->build('/' . $client->urlName, true) ?>"><?= $client->name?></a> <?= $client->private ? ' <i class="fa fa-eye-slash"></i>' : '' ?>
    </p>
    <?php endforeach; ?>
</div>