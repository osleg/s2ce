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
	/* Sanitize input */
	$ip = $_SERVER["REMOTE_ADDR"];
	$port = intval(get_input("port"));
	$num_conn = intval(get_input("num_conn"));
	$max_conn = intval(get_input("num_max"));
	$name = get_input("name");
	$desc = get_input("desc");
	$minlevel = intval(get_input("minlevel"));
	$maxlevel = intval(get_input("maxlevel"));	
	$login = get_input("login");
	
	/* Create in database */
	$query = "
		INSERT INTO server SET 
			ip = '$ip', port = $port, num_conn = $num_conn, max_conn = $max_conn,
			name = '$name', description = '$description', minlevel = $minlevel,
			maxlevel = $maxlevel, login = '$login'
		ON DUPLICATE KEY UPDATE
			num_conn = $num_conn, max_conn = $max_conn, name = '$name', 
			description = '$description', minlevel = $minlevel, 
			maxlevel = $maxlevel, login = '$login'";
		
	mysql_query($query);
	
	/* Send id in answer */
	$id = mysql_insert_id();
	$data = array(
		"acct_id" => $id,
		"svr_id" => $id,
		"set_online" => 3,
		"UPD" => 11,
		"reservation" => -1);
	
	return $data;
}

/* Save accounts on a server */
function handle_set_online_ids()
{
	/* Update number of connections */
	$num_conn = intval(get_input("num_conn"));	
	$query = "
		UPDATE server SET
			num_conn = $num_conn
		WHERE
			login = '$login'";
			
	/* Return empty */
	return array();
}

/* Remove a server */
function handle_shutdown()
{
	/* Remove server from list */
	$id = intval(get_input("server_id"));
	$query = "
		DELETE FROM server WHERE
			id = $id";
	mysql_query($query);
	
	/* Return empty */
	return array();
}

?>