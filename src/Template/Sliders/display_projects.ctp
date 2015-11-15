<?php $this->Html->addCrumb($client->name, '/'. $client->urlName) ?>
<h3>Projects</h3>
<div>
    <?php foreach ($projects as $project): ?>
    <p>
        <?= $this->Html->link($project->name, $this->Url->build('/' . $client->urlName . '/' . $project->urlName, true)) ?>
    </p>
    <?php endforeach; ?>
</div>