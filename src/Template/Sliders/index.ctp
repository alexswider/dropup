<h3>Clients</h3>
<div>
    <?php foreach ($clients as $client): ?>
    <p>
        <?= $this->Html->link($client->name, $this->Url->build('/' . $client->urlName, true)) ?>
    </p>
    <?php endforeach; ?>
</div>