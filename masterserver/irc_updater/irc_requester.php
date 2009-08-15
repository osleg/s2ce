<?php

include("../lib.php");

/* Open database connection */
db_open();

/* Parse request */
$action = get_input("f");
$valid_actions = array("auth", "item_list", "clan_roster", "get_all_stats");
if(!in_array($action, $valid_actions))
	die("Wrong action");

/* Dispatch request */
$func = "handle_".$action;
$data = $func();

/* Display output */
s2_serialize($data);

/* Request handling */
function handle_auth()
{
	global $config;
	
	$email = mysql_real_escape_string(get_input("email"));
	$password = mysql_real_escape_string(get_input("password"));
	
	$query = "
		SELECT * FROM user 
		WHERE nickname = '{$email}' 
		AND password = MD5('{$config['hash']}{$password}')";
	
	$result = mysql_query($query);
	
	if(!mysql_num_rows($result) == 1) {
		/* No user found, return error */
		return array();	
	} else {
		/* Return user data */
		$data = array();
		$row = mysql_fetch_assoc($result);
		$data["account_id"] = $row['id'];
		$data["nickname"] = $row['nickname'];
		$data["username"] = $row['username'];
		$data["account_type"] = 1;	
		
		/* Buddy list */
		$data["buddy"] = array("error" => "No buddies found.");
		
		/* Stats */
		$data["player_stats"] = array($row['id'] => array());
		$data["ranked_stats"] = array($row['id'] => array());
		
		return $data;
	}
}

function handle_item_list()
{
	return array();		
}

function handle_clan_roster()
{
	return array();
}

function handle_get_all_stats()
{
	return array();		
}

?>