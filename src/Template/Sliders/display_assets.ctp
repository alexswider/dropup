<?php $this->Html->addCrumb($client->name, '/' . $client->urlName) ?>
<?php $this->Html->addCrumb($project->name, '/' . $client->urlName . '/' . $project->urlName) ?>
<?php $this->Html->addCrumb($item->name, '/' . $client->urlName . '/' . $project->urlName . '/' . $item->idItem) ?>

<?php if ($isAdmin): ?>
<?= $this->Html->script('jquery-2.1.4.min') ?>
<?= $this->Html->script('jquery-ui.min') ?>
<?= $this->Html->script('drop') ?>

<script>
    $(document).ready(function() {
        $('#assets').sortable();
        $('.asset').mousedown(function() {
            $('#save-order').show();
        });
    });
</script>
<?php endif; ?>
<div id="assets">
    <?php if ($isAdmin): ?>
    <?= $this->Form->create(null, ['url' => '/saveOrder/' . $idItem]) ?>
    <?= $this->Form->input('refpage', ['type' => 'hidden', 'value' => $this->request->here]) ?>
    <?= $this->Form->input('orderAsset', ['type' => 'hidden', 'id' => 'orderAsset']) ?>
    <?= $this->Form->button('Save order', ['id' => 'save-order']) ?>
    <?= $this->Form->end() ?>
    <?php endif; ?>
    <?php foreach ($data as $key => $asset): ?>
    <div class="asset" id="<?= $asset->idAsset ?>">
        <p class="order"><?= $key+1 ?></p>
        <img src="<?= $this->Url->build($asset->imagePath, true) ?>">
        <p><?= $asset->description ?></p>
    </div>
    <?php endforeach; ?>
    <?php if ($isAdmin): ?>
    <div id="new-asset">
        <div id="dropzone">
            <p>Add new asset</p>
        </div>
        <div id="info">
            <?= $this->Form->create() ?>
            <?= $this->Form->input('image', ['type' => 'hidden']) ?>
            <?= $this->Form->input('description') ?>
            <?= $this->Form->button('Save') ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    <?php endif; ?>
</div>