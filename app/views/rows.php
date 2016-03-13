<?php $this->parent('layout')?>
<?php $this->section('title')?>
	some title
<?php $this->end()?>
<?php $this->section('content')?>
	<style type="text/css">

	/*.custom-blocks-list > div {padding:0;}*/
	.custom-blocks-list > div > div {border:1px solid black; margin-bottom:30px;}

	</style>
	<div class="container" ng-controller="RowsController as rowsCtrl">
		<div class="row custom-blocks-list">
			<div class="col-xs-8">
				<div ng-repeat="col in rowsCtrl.rows[0]">{{col}}</div>
			</div>
			<div class="col-xs-8">
				<div ng-repeat="col in rowsCtrl.rows[1]">{{col}}</div>
			</div>
			<div class="col-xs-8">
				<div ng-repeat="col in rowsCtrl.rows[2]">{{col}}</div>
			</div>
		</div>
	</div>
<?php $this->end()?>