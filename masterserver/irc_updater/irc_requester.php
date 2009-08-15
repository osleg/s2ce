<?php

include("../lib.php");

/* Dispatch request into handle function */
dispatch_request(array("auth", "item_list", "clan_roster", "get_all_stats"));

/* Authentification */
function handle_auth()
{
	global $config;
	
	$email = get_input("email");
	$password = get_input("password");
	
	$query = "
		SELECT * FROM user 
		WHERE 
			nickname = '{$email}' 
		AND 
			password = MD5('{$config['hash']}{$password}')";
	
	$result = mysql_query($query);
	
	if(!mysql_num_rows($result) == 1) {
		/* No user found, return error */
		return array("error" => "Invalid login.");	
	} else {
		/* Return user data */
		$data = mysql_fetch_assoc($result);
		$data["account_type"] = 1;	
		
		/* Buddy list */
		$data["buddy"] = array("error" => "No buddies found.");
		
		/* Stats */
		$data["player_stats"] = array($data['account_id'] => array());
		$data["ranked_stats"] = array($data['account_id'] => array());
		
		return $data;
	}
}

/* Item list [empty] */
function handle_item_list()
{
	return array();		
}

/* Clan roster [empty] */
function handle_clan_roster()
{
	return array();
}

/* All stats [empty] */
function handle_get_all_stats()
{
	return array();		
}

?>