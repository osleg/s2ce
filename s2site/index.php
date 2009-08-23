<?php

	$valid_controller = array("player", "server");

	/* Parse URL */
	$url = split($_GET["url"], "/");
	
	if($url[0] == "")
		$controller = "home";
	else
		$controller = basename($url[0]);

	/* Search for controller */
	$file = "./controllers/{$controller}.php";
	if(!file_exists($file)) {
		header("Status: 404");
		die;
	}
	
	/* Dispatch request */
	dispatch($url);

?>