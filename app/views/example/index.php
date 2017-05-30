<?php $this->parent('layout')?>
<?php $this->section('title')?>
	Welcome to Mushroom Framework!
<?php $this->end()?>
<?php $this->section('content')?>
	<div class="container">
		<?php foreach($news as $item):?>
			<div>
				<h3><a href="<?php echo Uri::make('Index@detail', $item->id)?>"><?php echo $item->name?></a></h3>
				<div><?php echo $item->name?></div>
				<hr />
			</div>
		<?php endforeach?>
	</div>
<?php $this->end()?>