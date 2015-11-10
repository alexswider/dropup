<?= $this->Html->script('angular-dragula.min.js') ?>
<?= $this->Html->script('drop.js') ?>

<div id="drop-container" ng-controller="Images">
    <div ng-repeat="image in images" dragula='"bag-one"'>
        <h3>{{image.title}}</h3>
    </div>
</div>