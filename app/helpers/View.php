<?php

namespace helpers;

class View {

	function renderJson($data) {
		\F3::expire();
		header('Content-Type: application/json; charset=' . \F3::get('ENCODING'));
		return json_encode($data);
	}

}
