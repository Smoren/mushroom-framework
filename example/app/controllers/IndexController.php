<?php

class IndexController extends Controller {
	public function index() {
 		// $dbs = new DatabaseSession(array(
 		// 	'type' => 'postgresql',
			// 'host' => 'localhost',
			// 'username' => 'root',
			// 'password' => '',
			// 'dbName' => 'test',
 		// ));

 		// $n = News::find($dbs, 1);
 		// $n->name = 'Postgresql';
 		// $n->text = 'Postgresql is my test ok!';
 		// $n->save();

 		// $news = new News($dbs);
 		// $news->name = "hhhhhhhhh";
 		// $news->text = "hhhhhhhhh hhhhhhhhh hhhhhhhhh hhhhhhhhh hhhhhhhhh hhhhhhhhh";
 		// $news->save();

 		$news = News::select()->getList();
		return Response::view('example/index')
			->with('news', $news);
 	}

 	public function detail($id) {
 		// $dbs = new DatabaseSession(array(
 		// 	'type' => 'postgresql',
			// 'host' => 'localhost',
			// 'username' => 'root',
			// 'password' => '',
			// 'dbName' => 'test',
 		// ));

 		$item = News::find($id);
		if(!$item) {
			return Router::transfer('Error.notFound');
		}
		return Response::view('example/index')
			->with('news', array($item));
 	}
}