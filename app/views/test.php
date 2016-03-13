<?php $this->parent('layout')?>
<?php $this->section('title')?>
	some title
<?php $this->end()?>
<?php $this->section('content')?>
	<div>I'm content</div>
	<div>some links: 
		<?php echo Html::link('Index@index', 'index', array(), array('test' => 1), array('class' => 'test'))?>,
		<?php echo Html::link('Index@testModel', 'model', array(), array(), array())?>,
		<?php echo Html::link('Index@test', 'test', array('10'), array(), array('class' => 'test'))?>
		<?php echo Uri::make('Index@index')?>
	</div>
<?php $this->end()?>