<?php if ($isAdmin): ?>
<?= $this->Html->script('jquery-2.1.4.min') ?>
<?= $this->Html->script('jquery.friendurl.min') ?>
<?= $this->Html->script('projects') ?>
<?php endif; ?>

<?php $this->Html->addCrumb($client->name, '/'. $client->urlName) ?>
<h3>Projects</h3>
<div>
    <?php if ($isAdmin): ?>
    <p>
        <i class="fa fa-plus-circle"></i> <a id="add-project">Add new project</a>
    </p>
    <div class="new-project">
        <?= $this->Form->create() ?>
        <?= $this->Form->input('name') ?>
        <?= $this->Form->input('urlName') ?>
        <?= $this->Form->button('Add') ?>
        <?= $this->Form->end() ?>
    </div>
    <?php endif; ?>
    <?php foreach ($projects as $project): ?>
    <p>
        <?= $this->Html->link($project->name, $this->Url->build('/' . $client->urlName . '/' . $project->urlName, true)) ?>
        <?php if ($isAdmin): ?>
            <a href="/deleteProject/<?= $project->idProject ?>"><i class="fa fa-trash"></i></a>
        <?php endif; ?>
    </p>
    <?php endforeach; ?>
</div>