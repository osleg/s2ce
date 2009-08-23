<?php

	include("../common/lib.php");

	$valid_controller = array("player", "server");

	/* Parse URL */
	$url = split(get_input("url", "/"), "/");
	
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
	include($file);

	/* Search for action */
	if(!isset($url[1]) or empty($url[1]))
		$action = "index";
	else	
		$action = $url[1];

	/* Get parameters */
	$params = array_splice($url, 0, 2);
	
	/* Search handler function */
	$function = "handle_{$action}";
	if(!function_exists($function)) {
		header("Status: 404");
		die;
	}
	
	/* Create output */
	ob_start();
	call_user_func_array($function, $params);
	$content = ob_get_clean();

	/* Insert into base layout */
	render_view("base", array('content' => $content));

	/* View helper */

	function render_view($filename, $data)
	{
		extract($data);
		$filename = "views/{$filename}.php";
		include($filename);					
	}

?>