(function() {
	"use strict";
	var app = angular.module('store', []);
	app.controller('ProductsController', ['$http', function($http) {
		var productsCtrl = this;
		this.products = [];
		$http.get('/products').success(function(res) {
			productsCtrl.products = res;
		});
	}]);

	app.controller('TabsController', function() {
		this.tab = 1;
		this.isSelected = function(tab) {
			return tab == this.tab;
		}
		this.select = function(tab) {
			this.tab = tab;
		}
	});
	app.controller('ReviewsController', ['$http', function($http) {
		this.review = {};
		this.add = function(product) {
			if(!product.reviews) {
				product.reviews = [];
			}
			product.reviews.push(this.review);
			this.review = {};
			$http.post('/product', {product: product}).success(function(res) {
				console.log(res);
			});
		};
	}]);
	app.controller('RowsController', ['$http', function($http) {
		this.rows = [
			[
				'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
				'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolo',
				'fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel '
			],
			[
				'Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus.',
				'Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante.',
				'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.'
			],
			[
				'Etiam sit amet orci eget eros faucibus tincidunt.',
				'leo eget bibendum sodales, augue velit cursus nunc',
				'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.'
			]
		];
	}]);
})();