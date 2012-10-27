<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id => array('/'.$this->module->id),
	'Accounts'
);
?>
<h1>Connected accounts</h1>

<?php $this->widget('bootstrap.widgets.TbAlert', array('alerts'=>array('success','warning','error')));?>

<?php if(empty($accounts)) : ?>
<p>No accounts connected. Why not <?php $this->widget('bootstrap.widgets.TbButton', array('label'=>'add one now', 'url'=>array('addAccount'), 'type'=>'primary', 'size'=>'small')); ?>?</p>
<?php else : ?>
<?php $gridDataProvider = new CArrayDataProvider($accounts); ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$gridDataProvider,
	'template'=>"{items}",
	'columns'=>array(
		array('name'=>'type', 'header'=>'Service'),
		array('name'=>'name', 'header'=>'Account name'),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'htmlOptions'=>array('style'=>'width: 50px'),
		),
	),
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array('label'=>'Add another one', 'url'=>array('addAccount'), 'type'=>'primary', 'size'=>'large')); ?>
<?php endif; ?>