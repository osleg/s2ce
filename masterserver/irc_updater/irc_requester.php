<?php

include("../common/lib.php");

/* Dispatch request into handle function */
dispatch_request(array("auth", "item_list", "clan_roster", "get_all_stats", "nick2id"));

/* Authentification */
function handle_auth()
{
	global $config;
	
	$email = post_input("email");
	$password = post_input("password");
	
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

/* Get account ID for nickname */
function handle_nick2id()
{
	if(!isset($_POST["nickname"]) or !is_array($_POST["nickname"]))
		return array();
		
	$nicknames = $_POST["nickname"];
	
	$data = array();
	foreach($nicknames as $nick) {
		/* TODO: Optimize this by creating a single query for all nicknames */
		$safe_nick = mysql_real_escape_string($nick);

		/* Search nickname in database */
		$query = "
			SELECT 
				id 
			FROM 
				users
			WHERE
				nickname = '{$safe_nick}'";
		$result = mysql_query($query);

		/* Save in output (nickname -> id) */
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_assoc($result);
			$data[$nick] = "{$row["id"]}";
		}
	}
	
	return $data;
}

?>