<?php $this->Html->addCrumb($this->request->params['clientName'], '/'. $this->request->params['clientName']) ?>
<h3>Projects</h3>
<div>
    <?php foreach ($projects as $project): ?>
    <p>
        <?= $this->Html->link($project->name, $this->Url->build('/' . $client->urlName . '/' . $project->urlName, true)) ?>
    </p>
    <?php endforeach; ?>
</div>