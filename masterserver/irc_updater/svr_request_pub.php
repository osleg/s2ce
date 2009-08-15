<?php

include("../lib.php");

/* Dispatch request into handle function */
dispatch_request(array("get_online", "set_online", "set_online_ids", "shutdown"));

/* Getting list of servers */
function handle_get_online()
{
	return array();
}

/* Add a server */
function handle_set_online()
{
	return array();
}

/* Save accounts on a server */
function handle_set_online_ids()
{
	return array();		
}

/* Remove a server */
function handle_shutdown()
{
		
}

?>