<?php $this->Html->addCrumb($client->name, '/' . $client->urlName) ?>
<?php $this->Html->addCrumb($project->name, '/' . $client->urlName . '/' . $project->urlName) ?>
<?php $this->Html->addCrumb($item->name, '/' . $client->urlName . '/' . $project->urlName . '/' . $item->idItem) ?>

<?php if (isset($data)) : ?>
    <video width="<?= $data->width ?>" height="<?= $data->height ?>" controls>
        <source src="<?= $this->Html->Url->build($data->videoPath)?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <p class="description"><?= $data->description ?></p>
<?php elseif ($isAdmin): ?>
    <?= $this->Html->script('jquery-2.1.4.min') ?>
    <?= $this->Html->script('jquery-ui.min') ?>
    <?= $this->Html->script('drop') ?>
    <div id="assets">
        <div id="new-asset">
            <div id="dropzone">
                <p>Add new video</p>
            </div>
            <div id="info">
                <?= $this->Form->create() ?>
                <?= $this->Form->input('base64', ['type' => 'hidden']) ?>
                <?= $this->Form->input('description') ?>
                <div class="inline">
                    <?= $this->Form->input('width') ?>
                    <?= $this->Form->input('height') ?>
                </div>
                <?= $this->Form->button('Save') ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
<?php endif; ?>
