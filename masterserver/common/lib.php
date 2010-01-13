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

/* Get input values */
function get_input($key, $default = "")
{
	if(!isset($_GET[$key]))
		return mysql_real_escape_string($default);
	return mysql_real_escape_string($_GET[$key]);
}

function post_input($key, $default = "")
{
	if(!isset($_POST[$key]))
		return mysql_real_escape_string($default);
	return mysql_real_escape_string($_POST[$key]);
}

function post_serialized($key, $default = array())
{
	if(!isset($_POST[$key]))
		$result = $default;
	else
		$result = unserialize($_POST[$key]);
	return db_escape($result);
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

/* Send query */
function db_query($query) {
	$result = mysql_query($query);
	if (!$result)
		throw new Exception(mysql_error());
	return $result;
}

/* Escape anything */
function db_escape($values, $quotes = true) {
	if (is_array($values)) {
		foreach ($values as $key => $value) {
			$values[$key] = db_escape($value, $quotes);
		}
	}
	else if ($values === null) {
		$values = 'NULL';
	}
	else if (is_bool($values)) {
		$values = $values ? 1 : 0;
	}
	else if (!is_numeric($values)) {
		$values = mysql_real_escape_string($values);
		if ($quotes) {
			$values = '"' . $values . '"';
		}
	}
	return $values;
}

/* Dispatch request into a handle function */
function dispatch_request($valid_actions)
{
	/* Parse request */
	$action = post_input("f");	
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