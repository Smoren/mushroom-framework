<?php

use MushroomFramework\Pattern\Uuid;

class TestController extends Controller {
 	public function method() {
 		if(Router::checkMethod('GET')) {
 			$text = 'Method is GET!';
 		} else {
 			$text = 'Method is '.Router::getMethod();
 		}
 		return Response::text($text);
 	}

 	public function test() {
 		$tu1 = new TestUuid();
 		$tu1->title = rand(1000, 9999);
 		$tu1->save();
 		$result = "id: {$tu1->id}\ntitle: {$tu1->title}\n\n";

 		$tu2 = TestUuid::find($tu1->id);
 		$result .= "id: {$tu2->id}\ntitle: {$tu2->title}\n\n---\n\n";

 		$rows = TestUuid::select()->getList();
 		foreach($rows as $item) {
 			$result .= "id: {$item->id}\ntitle: {$item->title}\n\n";
 		}

 		return Response::text($result);
 	}

 	public function transfer() {
 		// return Router::transfer('Index.index');
 		return Router::transfer('Index.detail', array('id' => 5));
	}

 	public function model() {
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

 	public function user() {
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
}