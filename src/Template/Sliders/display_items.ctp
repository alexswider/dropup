<?php $this->Html->addCrumb($client->name, '/'. $client->urlName) ?>
<?php $this->Html->addCrumb($project->name, '/' . $client->urlName . '/' . $project->urlName) ?>

<h3>Items</h3>
<div>
    <?php if ($isAdmin): ?>
    <?= $this->Html->script('jquery-2.1.4.min') ?>
    <script>
        $(document).ready(function() {
            $('#add-item').click(function() {
                $('.new-item').slideToggle();
            });
        });
    </script>
    <p><i class="fa fa-plus-circle"></i> <a id="add-item">Add new item</a></p>
    <div class="new-item slide-form">
        <?= $this->Form->create() ?>
        <?= $this->Form->input('name') ?>
        <?= $this->Form->select('type', ['assets' => 'Assets','media' => 'Media']) ?>
        <?= $this->Form->button('Add') ?>
        <?= $this->Form->end() ?>
    </div>
    <?php endif; ?>
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