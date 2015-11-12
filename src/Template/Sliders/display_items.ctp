<?php $this->Html->addCrumb($this->request->params['clientName'], '/'. $this->request->params['clientName']) ?>
<?php $this->Html->addCrumb($this->request->params['projectName'], '/'. $this->request->params['clientName'] . '/'. $this->request->params['projectName']) ?>

<h3>Items</h3>
<div>
    <?= $this->Html->link("Add new item", $this->Url->build('/' . $client->urlName . '/' . $project->urlName . '/new', true)) ?>
    <?php foreach ($itemsDate as $key => $date): ?>
    <div class="date">
        <h4><?php 
            if (gettype($date->date) == 'object') {
                echo $date->date->format('l, M. j');
        }
        ?></h4>
    </div>
        <?php foreach ($items[$key] as $item): ?>
        <p>
            <?= $this->Html->link($item->name, $this->Url->build('/' . $client->urlName . '/' . $project->urlName . '/' . $item->idItem, true)) ?>
        </p>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>