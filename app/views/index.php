<?php $this->parent('layout')?>
<?php $this->section('title')?>
	some title
<?php $this->end()?>
<?php $this->section('content')?>
	<div class="container">
		<div class="row" ng-controller="ProductsController as productsCtrl">
			<div class="list-group-item col-xs-12" ng-hide="product.soldOut" ng-repeat="product in productsCtrl.products">
				<h3>
					{{product.name}} 
					<em class="pull-right">{{product.price | currency}}</em>
				</h3>
				<section ng-controller="TabsController as tabsCtrl">
					<ul class="nav nav-pills">
						<li ng-class="{active:tabsCtrl.isSelected(1)}">
							<a href ng-click="tabsCtrl.select(1)">Description</a>
						</li>
						<li ng-class="{active:tabsCtrl.isSelected(2)}">
							<a href ng-click="tabsCtrl.select(2)">Specs</a>
						</li>
						<li ng-class="{active:tabsCtrl.isSelected(3)}">
							<a href ng-click="tabsCtrl.select(3)">Reviews</a>
						</li>
					</ul>
					<div ng-show="tabsCtrl.isSelected(1)">
						<h4>Description</h4>
						<blockquote>{{product.description}}</blockquote>
					</div>
					<div ng-show="tabsCtrl.isSelected(2)">
						<h4>Specs</h4>
						<blockquote>list of specs</blockquote>
					</div>
					<div ng-show="tabsCtrl.isSelected(3)">
						<h4>Reviews</h4>
						<blockquote ng-repeat="review in product.reviews">
							{{review.body}}
							<cite class="clearfix">— {{review.author}}</cite>
						</blockquote>
						<form ng-controller="ReviewsController as reviewsCtrl" ng-submit="reviewForm.$valid && reviewsCtrl.add(product)" name="reviewForm" novalidation>
							<blockquote ng-show="reviewsCtrl.review.body || reviewsCtrl.review.author">
								{{reviewsCtrl.review.body}}
								<cite class="clearfix" ng-show="reviewsCtrl.review.author">— {{reviewsCtrl.review.author}}</cite>
							</blockquote>
							<fieldset class="form-group">
								<textarea ng-model="reviewsCtrl.review.body" class="form-control" placeholder="text..." required></textarea>
							</fieldset>
							<fieldset class="form-group">
								<input ng-model="reviewsCtrl.review.author" type="email" class="form-control" placeholder="your@email.com" title="Email" required />
							</fieldset>
							<fieldset class="form-group">
								<input type="submit" value="Send" class="form-control btn btn-info" />
							</fieldset>
						</form>
	  				</div>
				</section>
			</div>
		</div>
	</div>
<?php $this->end()?>