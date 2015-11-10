<?= $this->Html->script('jquery-2.1.4.min') ?>
<?= $this->Html->script('jquery.filedrop.min') ?>
<?= $this->Html->script('jquery-ui.min') ?>
<?= $this->Html->script('drop') ?>

<div id="assets">
    <?= $this->Form->create(null, ['url' => '/saveOrder/' . $idItem]) ?>
    <?= $this->Form->input('refpage', ['type' => 'hidden', 'value' => $this->request->here]) ?>
    <?= $this->Form->input('orderAsset', ['type' => 'hidden', 'id' => 'orderAsset']) ?>
    <?= $this->Form->button('Save order', ['id' => 'save-order']) ?>
    <?= $this->Form->end() ?>
    <?php foreach ($assets as $key => $asset): ?>
    <div class="asset" id="<?= $asset->idAsset ?>">
        <p class="order"><?= $key+1 ?></p>
        <img src="<?= $this->Url->build($asset->imagePath, true) ?>">
        <p><strong><?= $asset->name ?></strong></p>
        <p><?= $asset->description ?></p>
    </div>
    <?php endforeach; ?>
    <div id="new-asset">
        <div id="dropzone">
            <p>Add new asset</p>
        </div>
        <div id="info">
            <?= $this->Form->create() ?>
            <?= $this->Form->input('image', ['type' => 'hidden']) ?>
            <?= $isNew ? $this->Form->input('item name') : '' ?>
            <?= $this->Form->input('name') ?>
            <?= $this->Form->input('description') ?>
            <?= $this->Form->button('Save') ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>