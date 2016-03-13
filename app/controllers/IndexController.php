<?php

class IndexController extends Controller {
	public function getIndex() {
		// Form::start();
 		$test = Request::get();
		return Response::view('index');
 	}

 	public function getProducts() {
 		$products = Product::select()->getList();
 		return Response::json($products);
 	}

 	public function postProduct() {
 		$product = Request::json('product');
 		$dbProduct = Product::find($product['id']);
 		
 		if($dbProduct) {
 			$dbProduct->set($product);
 			$dbProduct->save();
 		}

 		return Response::json($dbProduct);
 	}

 	public function getTestModel() {
 		// $news = News::select('id', 'name')->getList();
 		// echo '<pre>'; print_r($news); echo '</pre>';
		if($news = News::find(1)) {
			$validator = $news->set(array(
				'name' => 'test name',
				'text' => 'a lot of text',
				'another' => 'this is not a field',
			));
			if($validator->success()) {
				echo "validation success\n";
				$news->save();
			} else {
				echo "validation fail\n";
				echo "error fields: ".implode(', ', $validator->errorFields)."\n";
			}
			print_r($news);
		}

		return Response::text("end");
 	}

 	public function getUser() {
 		// $user = User::register(array('email' => 'smoren', 'password' => '123', 'name' => 'Smoren Freelight'));
 		// $user = User::login('smoren', '123', true);
 		// User::logout();
 		if($user = User::authorized()) {
 			echo "authorized\n";
 		} else {
 			echo "NOT authorized\n";
 		}
 		print_r(array(User::error()));
 		print_r($user);
 		return Response::text('end');
 	}

 	public function getRows() {
 		return Response::view('rows');
 	}
}