<?php

class ErrorController extends Controller {
	public function notFound() {
		Response::status(404);
		return Response::text('404 not found!');
 	}
}