<?php

include("config.php");

/* Serialize a PHP object into the Savage 2 data language */
function s2_serialize($object) 
{		
	if(is_string($object)) {
		/* Strings: s:[length]:"[text]"; */
		print "s:".strlen($object).':"'.$object.'";';
	} elseif(is_array($object)) {
		/* Arrays: a:[length]:{[key][value][key][value];...} */
		print "a:".count($object).":{";
		
		foreach($object as $key => $value) {
			s2_serialize($key);
			s2_serialize($value);
		}
		
		print "}";
	} elseif(is_numeric($object)) {
		/* Integer: i:[value]; */
		print "i:".$object.";";
	} else {
		/* Not available: N; */
		print "N;";
	}
}

/* Get input */
function get_input($key, $default = "")
{
	if(!isset($_POST[$key]))
		return $default;
	return $_POST[$key];
}

/* Open database connection */
function db_open()
{
	global $config;
	
	$db = $config['db'];
	
	mysql_connect($db['host'], $db['username'], $db['password'])
		or die("No connection to database");
	
	mysql_select_db($db['database'])
		or die("Cannot select database");
}

/* Dispatch request into a handle function */
function dispatch_request($valid_actions)
{
	/* Parse request */
	$action = get_input("f");	
	if(!in_array($action, $valid_actions))
		die("Wrong action");
	
	/* Dispatch request */
	$func = "handle_".$action;
	$data = $func();
	
	/* Display output */
	s2_serialize($data);		
}

/* Open database connection */
db_open();

?>