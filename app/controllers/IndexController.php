<?php

class IndexController extends Controller {
	public function index() {
		$news = News::select()->getList();
		return Response::view('example/index')
			->with('news', $news);
 	}

 	public function detail($id) {
		$item = News::find($id);
		if(!$item) {
			return Router::transfer('Error.notFound');
		}
		return Response::view('example/index')
			->with('news', array($item));
 	}
}