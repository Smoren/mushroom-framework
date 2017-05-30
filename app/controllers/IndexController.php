<?php

class IndexController extends Controller {
	public function getIndex() {
		return Response::view('index');
 	}

 	public function getTestModel() {
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
}