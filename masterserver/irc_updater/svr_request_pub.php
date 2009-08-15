<?php

include("../lib.php");

/* Dispatch request into handle function */
dispatch_request(array("get_online", "set_online", "set_online_ids", "shutdown"));

/* Getting list of servers */
function handle_get_online()
{
	$result = mysql_query("
		SELECT 
			id, port, ip, max_conn, num_conn, name, description, 
			minlevel, maxlevel, official
		FROM
			server
		WHERE
			updated > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
			
	$data = array();
	while($row = mysql_fetch_assoc($result)) {
		$data[$row['id']] = $row;
	}
	return $data;
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
	return array();
}

?>